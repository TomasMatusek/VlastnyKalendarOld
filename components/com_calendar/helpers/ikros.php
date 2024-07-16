<?php


class IkrosService {

    private static $IKROS_API_URL = "https://eshops.inteo.sk/api/v1/invoices/";
    private static $IKROS_API_SECRET = "9feddb2c-2218-4059-8691-701af7bb2f04";
    private static $IKROS_NUMBERING_SEQUENCE = "OF3";

    private static $VAT = 20;

    public static $ALDO_BANK_IBAN = "SK2109000000000011535932";
    public static $ALDO_BANK_SWIFT = "GIBASKBX";


    public static function updateInvoice($order, $model) {

        // Build invoice, items = photobooks + shipping method
        $invoice = new StdClass();
        $invoice->items = array();
        $now = time();

        // Automaticke cislovanie
        $invoice->numberingSequence = self::$IKROS_NUMBERING_SEQUENCE;

        // Datum vystavenia dokladu
        $invoice->createDate = self::formatDate($now);

        // Datum splatnosti
        $invoice->dueDate = self::formatDateIncreased($now, 7);

        // Datum dodania
        $invoice->completionDate = self::formatDate($now);

        // Cena za doklad bez DPH
        $invoice->totalPrice = self::priceWithoutVAT($order['order']['price_calendars']);

        // Cena za doklad s DPH
        $invoice->totalPriceWithVat = $order['order']['price_calendars'];

        // Fakturacne udaje
        $invoice->clientName             = $order['order']['billing_name'];
        $invoice->clientStreet           = $order['order']['billing_address'];
        $invoice->clientPostCode         = $order['order']['billing_zip'];
        $invoice->clientTown             = $order['order']['billing_city'];
        $invoice->clientCountry          = "Slovensko";
        $invoice->clientPhone            = $order['order']['billing_phone'];
        $invoice->clientEmail            = $order['order']['billing_mail'];
        $invoice->clientRegistrationId   = $order['order']['billing_ico'];
        $invoice->clientTaxId            = $order['order']['billing_dic'];
        $invoice->clientVatId            = $order['order']['billing_icdph'];

        // Bankove spojenie, sposob dorucenia a platby
        $invoice->variableSymbol         = "číslo faktúry";
        $invoice->openingText            = $order['order']['comment'];
        $invoice->senderBankIban         = self::$ALDO_BANK_IBAN;
        $invoice->senderBankSwift        = self::$ALDO_BANK_SWIFT;
        $invoice->orderNumber            = $order['order']['order_id'];
        $invoice->paymentType            = CalendarHelper::paymentMethodTranslate($order['order']['payment_method']);
        $invoice->deliveryType           = CalendarHelper::transportMethodTranslate($order['order']['transport_method']);

        // Dodacia (Postova) adresa
        $invoice->clientHasDifferentPostalAddress =
            $order['order']['shipping_name']    != $order['order']['billing_name'] ||
            $order['order']['shipping_address'] != $order['order']['billing_address'] ||
            $order['order']['shipping_zip']     != $order['order']['billing_zip'] ||
            $order['order']['shipping_city']    != $order['order']['billing_city'];

        if ($invoice->clientHasDifferentPostalAddress) {
            $invoice->clientPostalName       = $order['order']['shipping_name'];
            $invoice->clientPostalStreet     = $order['order']['shipping_address'];
            $invoice->clientPostalPostCode   = $order['order']['shipping_zip'];
            $invoice->clientPostalTown       = $order['order']['shipping_city'];
            $invoice->clientPostalCountry    = "Slovensko";
        }

        // Others
        $invoice->currency = "EUR";
        $invoice->senderIsVatPayer = false;

        foreach ($order['calendars'] as $key => $calendar) {
            $item = new StdClass();
            $item->name = 'Kalendár typ ' . strtoupper($calendar['type']) . ' ' . $calendar['start_year'];
            $item->count = $calendar['quantity'];
            $item->measureType = "KS";
            $item->typeId = 1;
            $item->unitPrice = self::priceWithoutVAT($calendar['order_sent_price']);
            $item->unitPriceWithVat = $calendar['order_sent_price'];
            $item->totalPrice = $item->unitPrice * $calendar['quantity'];
            $item->totalPriceWithVat = $item->unitPriceWithVat * $calendar['quantity'];
            $item->vat = self::$VAT;
            $item->hasDiscount = false;
            array_push($invoice->items, $item);
        }

        // Polozka - zlava na objednavku
        if ($order['order']['discount'] > 0) {
            $itemDiscountInEUR = $invoice->totalPriceWithVat - Price::applyDiscount($invoice->totalPriceWithVat, $order['order']['discount']);
            $item = new StdClass();
            $item->name = 'Použitie kupónu - zľava ' . $order['order']['discount'] . '%';
            $item->count = 1;
            $item->measureType = "KS";
            $item->typeId = 1;
            $item->unitPrice = self::priceWithoutVAT($itemDiscountInEUR * -1);
            $item->unitPriceWithVat = $itemDiscountInEUR * -1;
            $item->totalPrice = self::priceWithoutVAT($itemDiscountInEUR * -1);
            $item->totalPriceWithVat = $itemDiscountInEUR * -1;
            $item->vat = self::$VAT;
            $item->hasDiscount = false;
            array_push($invoice->items, $item);
        }

        // Polozka - sposob dopravy
        $item = new StdClass();
        $item->name = 'Spôsob dodania: ' . CalendarHelper::transportMethodTranslate($order['order']['transport_method']);
        $item->count = 1;
        $item->measureType = "KS";
        $item->unitPrice = self::priceWithoutVAT($order['order']['price_shipping_and_packing']);
        $item->unitPriceWithVat = $order['order']['price_shipping_and_packing'];
        $item->totalPrice = $item->unitPrice;
        $item->totalPriceWithVat = $item->unitPriceWithVat;
        $item->vat = self::$VAT;
        $item->typeId = 2;
        $item->hasDiscount = false;
        array_push($invoice->items, $item);

        // Put invoice details into iKros system
        $response = self::createInvoice($invoice);

        // Check iKros response
        if ($response['documents'] && count($response['documents']) > 0) {
            $invoiceDownloadURL = $response['documents'][0]['downloadUrl'];

            // Update order details in DB
            $model->updateOrderInvoiceURL($order['order']['order_id'], $invoiceDownloadURL);

            // Push invoice into iKros invoice system
            if (self::uploadInvoice($invoiceDownloadURL)) {
                return true;
            }
        }

        return false;
    }

    private static function uploadInvoice($downloadUrl) {
        get_headers($downloadUrl);
        return true;
    }

    private static function createInvoice($jsonData) {
        $curl = curl_init(self::$IKROS_API_URL);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array($jsonData)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . self::$IKROS_API_SECRET
        ));

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result, true);
    }

    private static function formatDate($timestamp) {
        return date('Y-m-d\TH:m:s', $timestamp);
    }

    private static function formatDateIncreased($timestamp, $plusDays) {
        return self::formatDate(strtotime('+' . $plusDays . ' day', $timestamp));
    }

    private static function priceWithoutVAT($basePrice) {
        return $basePrice / ( 1 + (self::$VAT / 100));
    }
}
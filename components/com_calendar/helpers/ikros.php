<?php


class IkrosService {

    private static $IKROS_API_URL = "https://api-economy.kros.sk/api/invoices/";

    private static $IKROS_API_BEARER_TOKEN = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJJZCI6Ijk1ZDQ4YWU5LTYwMGMtNDM2YS05MTM3LTBhYWRkYjZiYWI1YiIsIlRlbmFudElkIjoiMzA1ODY4IiwiU2NvcGUiOiJLcm9zLkVzdyIsIkNyZWRlbnRpYWxzIjoie1wiVXNlcklkXCI6MjEwOTY2fSIsIm5iZiI6MTcyMTEyOTM5OCwiZXhwIjo0MTAyNDQ0ODAwLCJpc3MiOiJrcm9zLnNrIiwiYXVkIjoiM3JkcGFydHlhcGkifQ.qJpUYL3XLKI_Fq0U9JgAuelrGkjV0BtsoqNfLtrat9k";

    private static $IKROS_NUMBERING_SEQUENCE = "OF3";

    public static function pushInvoice($order, $model)
    {
        $jsonData = self::createInvoiceData($order);
        $response = self::executeHttpRequest($jsonData);

        $model->updateOrderInvoiceURL($order['order']['order_id'], "https://fakturacia.kros.sk/company/305868/invoices/list?requestId=". $response['requestId']);
    }

    private static function printErrorResult($response)
    {
        echo '<pre>';
        var_dump($response);
        echo '</pre>';
        exit();
    }

    private static function createInvoiceData($order): string {
        $now = time();

        $root = new stdClass;
        $root->data = new stdClass;
        $root->data->externalId = "d93b19c9-7463-4066-a8c5-a86240cf75bc";
        $root->data->internalNote = "";
        $root->data->printedNote = "";
        $root->data->vatPayerType = 1;
        $root->data->useParagraph7or7a = false;
        $root->data->culture = "sk-SK";
        $root->data->openingText = $order['order']['comment'];
        $root->data->closingText = "";
        $root->data->registrationCourtText = "Firma zapísaná v registri " . CalendarConstants::$ALDO_DETAIL;
        $root->data->dueDate = self::formatDateIncreased($now, 7);
        $root->data->currency = "EUR";
        $root->data->exchangeRate = 1;
        $root->data->discountPercent = 0;
        $root->data->discountTotalPriceInclVat = 0;
        $root->data->issueDate = self::formatDate($now);
        $root->data->orderNumber = $order['order']['order_id'];
        $root->data->paymentType = CalendarHelper::paymentMethodTranslate($order['order']['payment_method']);
        $root->data->variableSymbol = "";

        $root->data->deliveryDate = self::formatDate($now);
        $root->data->advancePaymentDeduction = 0;
        $root->data->numberingSequence = self::$IKROS_NUMBERING_SEQUENCE;
        $root->data->documentNumber = "";
        $root->data->invoiceType = 0;
        $root->data->creditedInvoiceNumber = "";
        $root->data->mandatoryText = "";
        $root->data->mandatoryTextType = 0;
        $root->data->ossTaxState = 0;

        // Bank account
        $root->data->bankAccount = new stdClass;
        $root->data->bankAccount->iban = CalendarConstants::$ALDO_BANK_IBAN;
        $root->data->bankAccount->accountNumber = CalendarConstants::$ALDO_BANK_ACCOUNT;
        $root->data->bankAccount->isForeign = true;
        $root->data->bankAccount->swift = CalendarConstants::$ALDO_BANK_SWIFT;

        // Custom fields
        $root->data->customFields = array();
//        $root->data->customFields[0] = new stdClass;
//        $root->data->customFields[0]->label = "";
//        $root->data->customFields[0]->value = $order['order']['order_id'];

        // Accounting details
        $root->data->accountingDetails = new stdClass;
        $root->data->accountingDetails->syntheticAccount = "";
        $root->data->accountingDetails->analyticalAccount = "";
        $root->data->accountingDetails->descriptionAccounting = "";

        // Partner
        $root->data->partner = new stdClass;
        $root->data->partner->registrationId = $order['order']['billing_ico'];
        $root->data->partner->taxId = $order['order']['billing_dic'];
        $root->data->partner->vatId = $order['order']['billing_icdph'];
        $root->data->partner->phoneNumber = $order['order']['billing_phone'];
        $root->data->partner->email = $order['order']['billing_mail'];

        // Partner address
        $root->data->partner->address = new stdClass;
        $root->data->partner->address->businessName = $order['order']['billing_name'];
        $root->data->partner->address->contactName = $order['order']['billing_name'];
        $root->data->partner->address->street = $order['order']['billing_address'];
        $root->data->partner->address->postCode = $order['order']['billing_zip'];
        $root->data->partner->address->city = $order['order']['billing_city'];
        $root->data->partner->address->country = "Slovensko";

        // Partner postal address
        $root->data->partner->postalAddress = new stdClass;
        $root->data->partner->postalAddress->businessName = $order['order']['shipping_name'];
        $root->data->partner->postalAddress->contactName = $order['order']['shipping_name'];
        $root->data->partner->postalAddress->street = $order['order']['shipping_address'];
        $root->data->partner->postalAddress->postCode = $order['order']['shipping_zip'];
        $root->data->partner->postalAddress->city = $order['order']['shipping_city'];
        $root->data->partner->postalAddress->country = "Slovensko";

        // My company
        $root->data->myCompany = new stdClass;
        $root->data->myCompany->registrationId = CalendarConstants::$ALDO_ICO;
        $root->data->myCompany->taxId = CalendarConstants::$ALDO_DIC;
        $root->data->myCompany->vatId = CalendarConstants::$ALDO_ICDPH;
        $root->data->myCompany->phoneNumber = CalendarConstants::$ALDO_PHONE;
        $root->data->myCompany->email = CalendarConstants::$ALDO_EMAIL;
        $root->data->myCompany->web = CalendarConstants::$ALDO_WEB;

        // My company address
        $root->data->myCompany->address = new stdClass;
        $root->data->myCompany->address->businessName = CalendarConstants::$ALDO_NAME;
        $root->data->myCompany->address->contactName = CalendarConstants::$ALDO_CONTACT_PERSON_NAME;
        $root->data->myCompany->address->street = CalendarConstants::$ALDO_ADDRESS;
        $root->data->myCompany->address->postCode = CalendarConstants::$ALDO_ZIP;
        $root->data->myCompany->address->city = CalendarConstants::$ALDO_CITY;
        $root->data->myCompany->address->country = CalendarConstants::$ALDO_COUNTRY;

        // Items - calendars
        $index = 0;
        $root->data->items = array();
        for ($i = 0; $i < count($order['calendars']); $i++) {
            $index = $i;
            $calendar = $order['calendars'][$i];

            $root->data->items[$i] = new stdClass;
            $root->data->items[$i]->name = "Kalendár typ " . strtoupper($calendar['type']) . " " . $calendar['start_year'];
            $root->data->items[$i]->description = "";
            $root->data->items[$i]->amount = intval($calendar['quantity']);
            $root->data->items[$i]->measureUnit = "KS";
            $root->data->items[$i]->vatRate = CalendarConstants::$VAT;
            $root->data->items[$i]->totalPriceInclVat = floatval($calendar['quantity'] * $calendar['order_sent_price']);
            $root->data->items[$i]->itemCode = "";
            $root->data->items[$i]->warehouseCode = "";
            $root->data->items[$i]->eanCode = "";

            if ($order['order']['discount'] > 0) {
                $root->data->items[$i]->discountPercent = $order['order']['discount'];
                $root->data->items[$i]->discountName = 'Zľavový kupón: ' . $order['order']['discount'] . '%';
            } else {
                $root->data->items[$i]->discountPercent = 0;
                $root->data->items[$i]->discountName = "";
            }
        }

        // Item - shipping
        $index++;
        $root->data->items[$index] = new stdClass;
        $root->data->items[$index]->name = "Doprava a balné";
        $root->data->items[$index]->description = "Spôsob dodania: " . CalendarHelper::transportMethodTranslate($order['order']['transport_method']);
        $root->data->items[$index]->amount = 1;
        $root->data->items[$index]->measureUnit = "KS";
        $root->data->items[$index]->vatRate = CalendarConstants::$VAT;
        $root->data->items[$index]->totalPriceInclVat = floatval($order['order']['price_shipping_and_packing']);
        $root->data->items[$index]->itemCode = "";
        $root->data->items[$index]->warehouseCode = "";
        $root->data->items[$index]->eanCode = "";
        $root->data->items[$i]->discountPercent = 0;
        $root->data->items[$i]->discountName = "";

        return json_encode($root);
    }

    private static function executeHttpRequest($jsonData) {
        $curl = curl_init(self::$IKROS_API_URL);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . self::$IKROS_API_BEARER_TOKEN
        ));

        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    private static function formatDate($timestamp) {
        return date('Y-m-d', $timestamp);
    }

    private static function formatDateIncreased($timestamp, $plusDays) {
        return self::formatDate(strtotime('+' . $plusDays . ' day', $timestamp));
    }

    private static function priceWithoutVAT($basePrice) {
        return $basePrice / ( 1 + (CalendarConstants::$VAT / 100));
    }
}
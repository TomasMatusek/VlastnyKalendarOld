<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

$order = $this->data['order']['order'];
$zip = $this->data['zip'];
$user = $this->data['user'];
$user_detail = $this->data['user_profile'];
$gp_payment = $this->data['wp_payment'];

$calendar_types = 0;
$calendar_quantity = 0;
for ($i = 0; $i < count($this->data['order']['calendars']); $i++) {
    $calendar = $this->data['order']['calendars'][$i];
    $calendar_quantity += $calendar['quantity'];
    $calendar_types++;
}

$numbering = array(
    'a' => 1,
    'b' => 2,
    'c' => 3,
    'd' => 4,
    'e' => 5,
    'f' => 6,
    'g' => 7,
    'h' => 8,
    'i' => 9,
    'j' => 10,
    'k' => 13,
    'l' => 14,
    'm' => 11,
    'n' => 12,
    'o' => 15,
    'p' => 16,
    'q' => 17,
    'r' => 19,
    's' => 20,
    't' => 21,
    'u' => 22,
    'v' => 23,
    'w' => 18
);

?>
<html lang="en">
<head>
    <title>Detail</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="<?php echo CAL_ROOT_WEB . '/components/com_calendar/assets/js/calendar_admin.js';?>"></script>
</head>
<body>
    <div id="calendar-order">

        <div class="row">
            <div class="col-xs-6">
                <div class="content-box box-orange">
                    <div class="content-header">
                        <h2>Objednávka č. <?php echo $order['order_id']; ?></h2>
                    </div>
                    <div class="content-body no-padding">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                                <td>Id objednávky</td>
                                <td><?php echo $order['order_id']; ?></td>
                            </tr>
                            <tr>
                                <td>Používateľ</td>
                                <td>
                                    Id: <?php echo $order['user_id']; ?>,
                                    Meno: <?php echo $user->name; ?>,
                                    Prihlasovacie meno: <?php echo $user->username; ?>,
                                    Email: <?php echo $user->email; ?>,
                                    Adresa: <?php echo $user_detail->profile['address1']; ?>,
                                    Mesto: <?php echo $user_detail->profile['city']; ?>,
                                    Kraj: <?php echo $user_detail->profile['region']; ?>,
                                    Krajina: <?php echo $user_detail->profile['country']; ?>,
                                    PSC: <?php echo $user_detail->profile['postal_code']; ?>,
                                    Tel.c.: <?php echo $user_detail->profile['phone']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Faktúra č.</td>
                                <td>
                                    <a href="<?php echo $this->data['invoice_file_download']; ?>" class="orange" target="_blank">
                                        <?php echo $order['invoice_number'] == NULL ? '-' : $order['invoice_number']; ?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Odoslaná</td>
                                <td><?php echo date('d.m.Y H:i', $order['order_sent']); ?></td>
                            </tr>
                            <tr>
                                <td>Objednané kalendáre</td>
                                <td><?php echo $calendar_quantity; ?> ks [<?php echo $calendar_types; ?> type(s)]</td>
                            </tr>
                            <tr>
                                <td width="30%">Status</td>
                                <td width="70%">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?option=com_calendar&task=admin.changeStatus&order_id=<?php echo $order['order_id'] ;?>&status=sent"
                                           class="btn btn-default btn-filter <?php echo $order['status'] == 'sent' ? 'btn-filter-active' : ''; ?>"
                                           style="width: 60px;">
                                            Nová
                                        </a>
                                        <a href="index.php?option=com_calendar&task=admin.changeStatus&order_id=<?php echo $order['order_id'] ;?>&status=in_progress"
                                           class="btn btn-default btn-filter <?php echo $order['status'] == 'in_progress' ? 'btn-filter-active' : ''; ?>"
                                           style="width: 100px;">
                                            Spracováva sa
                                        </a>
                                        <a href="index.php?option=com_calendar&task=admin.changeStatus&order_id=<?php echo $order['order_id'] ;?>&status=done"
                                           class="btn btn-default btn-filter <?php echo $order['status'] == 'done' ? 'btn-filter-active' : ''; ?>"
                                           style="width: 100px;">
                                            Vybavená
                                        </a>
                                        <a href="index.php?option=com_calendar&task=admin.changeStatus&order_id=<?php echo $order['order_id'] ;?>&status=canceled"
                                           class="btn btn-default btn-filter <?php echo $order['status'] == 'canceled' ? 'btn-filter-active' : ''; ?>"
                                           style="width: 60px;">
                                            Zrušená
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xs-6">
                <div class="content-box box-orange">
                    <div class="content-header">
                        <h2>Management</h2>
                    </div>
                    <div class="content-body no-padding">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr style="text-align: center">
                                <td>
                                    <?php if ($order['ikros_url'] == '') : ?>
                                        <a class="btn btn-cal btn-orange" href="/index.php/component/calendar/?task=admin.generateIkrosInvoice&order_id=<?php echo $order['order_id']; ?>&user_id=<?php echo $order['user_id']; ?>" style="font-size: 13px; width: 200px;">
                                            Vygeneruj IKros faktúru
                                        </a>
                                    <?php else: ?>
                                        <a class="btn btn-cal btn-orange" target="_blank" href="<?php echo $order['ikros_url']; ?>" style="font-size: 13px; width: 200px;">
                                            Otvor faktúru
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if($order['transport_method'] === 'depo' || $order['transport_method'] === 'courier'): ?>
                                <tr style="text-align: center">
                                    <td>
                                        <?php if (empty($order['depo_number'])) : ?>
                                            <a class="btn btn-cal btn-orange" target="_blank" href="/index.php/component/calendar/?task=admin.createDepoOrder&id=<?php echo $order['order_id']; ?>" style="font-size: 13px; width: 200px;">
                                                Nahraj objednávku do DEPA
                                            </a>
                                        <?php else: ?>
                                            <a class="btn btn-cal btn-orange" target="_blank" href="http://admin.depo.sk/sender" style="font-size: 13px; width: 200px;">
                                                <?php echo $order['depo_number']; ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($order['transport_method'] === 'courier'): ?>
                            <tr style="text-align: center">
                                <td>
                                    <?php if (empty($order['remax_result'])) : ?>
                                        <a class="btn btn-cal btn-orange" href="/index.php/component/calendar/?task=admin.createRemaxOrder&id=<?php echo $order['order_id']; ?>" style="font-size: 13px; width: 200px;">
                                            Nahraj objednávku do REMAXU
                                        </a>
                                    <?php else: ?>
                                        <b>REMAX</b>: <?php echo $order['remax_result']; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td style="text-align: center">
                                    <button class="btn btn-cal btn-orange" id="generate-zip" style="font-size: 13px; width: 200px;">
                                        Vygeneruj PDF
                                    </button>
                                    <span id="generate-zip-status"></span>
                                </td>
                            </tr>
                            <tr style="text-align: center">
                                <td>
                                    <a class="btn btn-cal btn-orange" href="/index.php/component/calendar/?task=admin.clearTemporaryFiles&order_id=<?php echo $order['order_id']; ?>" style="font-size: 13px; width: 200px;">
                                        Zmazať temp súbory
                                    </a>
                                </td>
                            </tr>
                            <tr style="text-align: center">
                                <td>
                                    <a href="index.php?option=com_calendar&view=admin&layout=orders" class="btn-cal btn-orange">
                                        Späť na zoznam objednávok
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>                      

        <!-- Calendars list -->
        <div class="row">
            <div class="col-xs-12">
                <div class="content-box box-orange">
                    <div class="content-header">
                        <h2>Objednané kalendáre</h2>
                    </div>
                    <div class="content-body no-padding">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <thead>
                            <tr>
                                <td width="5%">Id</td>
                                <td width="5%">Typ</td>
                                <td width="10%">Začiatok</td>
                                <td width="10%">Úvodná strana</td>
                                <td width="5%">Jazyk</td>
                                <td width="10%">Vytvorený</td>
                                <td width="10%">Počet kusov</td>
                                <td width="10%">Cena kus</td>
                                <td width="10%">Cena</td>
                                <td width="8%">ZIP</td>
                                <td width="8%">ZIP</td>
                                <td width="8%">Editor</td>
                            </tr>
                            </thead>
                            <?php for ($i = 0; $i < count($this->data['order']['calendars']); $i++) : ?>
                                <?php $ids .= '&id[]=' . $calendar['cal_id']; ?>
                                <?php $calendar = $this->data['order']['calendars'][$i];?>
                                <tr class="bottom-line calendar-row" id="<?php echo $calendar['cal_id']; ?>">
                                    <td><?php echo $calendar['cal_id']; ?></td>
                                    <td><?php echo ucfirst($calendar['type']); ?> - <?php echo $numbering[$calendar['type']]; ?></td>
                                    <td><?php echo ucfirst($calendar['start_month']) . ' ' .  ucfirst($calendar['start_year']); ?></td>
                                    <td><?php echo $calendar['front_page'] == "1" ? 'Áno' : 'Nie'; ?></td>
                                    <td><?php echo ucfirst($calendar['language']); ?></td>
                                    <td><?php echo date('d.m.Y H:i', $calendar['create_time']); ?></td>
                                    <td><?php echo $calendar['quantity']; ?></td>
                                    <td><?php echo $calendar['order_sent_price']; ?> &euro;</td>
                                    <td><?php echo $calendar['quantity'] * $calendar['order_sent_price']; ?> &euro;</td>
                                    <td class="zip_download">
                                        <?php if (array_key_exists($calendar['cal_id'], $zip)): ?>
                                            <a class="orange" href="/generatedPDF/<?php echo $zip[$calendar['cal_id']]; ?>">
                                                Download
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="zip_delete">
                                        <?php if (array_key_exists($calendar['cal_id'], $zip)): ?>
                                            <a class="orange" href="/index.php/component/calendar/?task=admin.deleteZipFile&order_id=<?php echo $order['order_id']; ?>&calendar_id=<?php echo $calendar['cal_id']; ?>">
                                                Delete
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a class="orange" href="/index.php/vytvorit-kalendar/novy-kalendar?task=calendar.update&cal_id=<?php echo $calendar['cal_id']; ?>">Edit</a>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </div>
                    <div class="content-footer no-padding">

                        <a href="/index.php/component/calendar/?task=admin.removeCalendarBackupImages&order_id=<?php echo $order['order_id']; ?><?php echo $ids; ?>" role="button" class="btn-cal btn-red">
                            Zmazať súbory
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendars photos -->
        <div class="row">
            <div class="col-xs-12">
                <div class="content-box box-orange">
                    <div class="content-header">
                        <h2>Použité obrázky</h2>
                    </div>
                    <div class="content-body no-padding">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <?php for ($i = 0; $i < count($this->data['order']['calendars']); $i++) : ?>
                                <?php $id = $this->data['order']['calendars'][$i]['cal_id']; ?>
                                <tr class="bottom-line calendar-row" id="<?php echo $id; ?>">
                                    <td>
                                        <?php echo $id; ?> -
                                        <?php for ($j = 0; $j < count($this->data['images'][$id]['photos']); $j++) : ?>
                                            <?php $image = $this->data['images'][$id]['photos'][$j]; ?>
                                            <a class="orange" href="<?php echo str_replace('/img/', '/img_backup/', $image['image']); ?>" target="_blank">
                                                <?php echo $image['year'] . ' ' . $image['month'] . ', '; ?>
                                            </a>
                                        <?php endfor; ?>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <!-- Informacie o objednavke -->
            <div id="col-1">
                <div class="col-md-4">
                    <div class="content-box box-orange mt-30 min-height-500">
                        <div class="content-header">
                            <h2>Informácie o objednávke</h2>
                        </div>
                        <div class="content-body no-padding">
                            <table width="100%">
                                <tr>
                                    <td width="35%">Spôsob doručenia</td>
                                    <td width="65%"><?php echo CalendarHelper::transportMethodTranslate($order['transport_method']); ?></td>
                                </tr>
                                <tr>
                                    <td>Spôsob platby</td>
                                    <td><?php echo CalendarHelper::paymentMethodTranslate($order['payment_method']); ?></td>
                                </tr>
                                <tr>
                                    <td>Zľavový kód</td>
                                    <td><?php echo CalendarHelper::showDash($order['coupon_code']); ?></td>
                                </tr>
                                <tr class="no-bottom-line">
                                    <td colspan="2" width="100%">Poznánka k objednávke:</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <textarea name="comment" disabled="disabled" style="height: 150px; color: rgb(179, 56, 56); font-weight: 600;"><?php echo $order['comment']; ?></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fakturacna adresa -->
            <div id="col-2" class="billing-address-form">
                <div class="col-md-4">
                    <div class="content-box box-orange mt-30 min-height-500">
                        <div class="content-header">
                            <h2>Fakturačná adresa</h2>
                        </div>
                        <div class="content-body no-padding">
                            <table width="100%">
                                <tr>
                                    <td width="40%">Meno</td>
                                    <td width="60%"><?php echo $order['billing_name']; ?></td>
                                </tr>
                                <tr>
                                    <td>Telefónne číslo</td>
                                    <td><?php echo $order['billing_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td>E-mail</td>
                                    <td><?php echo $order['billing_mail']; ?></td>
                                </tr>
                                <tr>
                                    <td>Ulica</td>
                                    <td><?php echo CalendarHelper::showDash($order['billing_address_number'] == '' ? $order['billing_address'] : $order['billing_address'] . " " . $order['billing_address_number']); ?></td>
                                </tr>
                                <tr>
                                    <td>Mesto</td>
                                    <td><?php echo CalendarHelper::showDash($order['billing_city']); ?></td>
                                </tr>
                                <tr>
                                    <td>PSČ</td>
                                    <td><?php echo CalendarHelper::showDash($order['billing_zip']); ?></td>
                                </tr>
                                <tr>
                                    <td>IČO</td>
                                    <td><?php echo CalendarHelper::showDash($order['billing_ico']); ?></td>
                                </tr>
                                <tr>
                                    <td>DIČ</td>
                                    <td><?php echo CalendarHelper::showDash($order['billing_dic']); ?></td>
                                </tr>
                                <tr>
                                    <td>IČ DPH</td>
                                    <td><?php echo CalendarHelper::showDash($order['billing_icdph']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dodacia adresa -->
            <div id="col-3" class="shipping-address-form">
                <div class="col-md-4">
                    <div class="content-box box-orange mt-30 min-height-500">
                        <div class="content-header">
                            <h2>Dodacia adresa</h2>
                        </div>
                        <div class="content-body no-padding">
                            <table width="100%">
                                <tr>
                                    <td>Meno</td>
                                    <td><?php echo CalendarHelper::showDash($order['shipping_name']); ?></td>
                                </tr>
                                <tr>
                                    <td>Ulica</td>
                                    <td><?php echo CalendarHelper::showDash($order['shipping_address_number'] == '' ? $order['shipping_address'] : $order['shipping_address'] . " " . $order['shipping_address_number']); ?></td>
                                </tr>
                                <tr>
                                    <td>Telefónne číslo</td>
                                    <td><?php echo CalendarHelper::showDash($order['shipping_phone']); ?></td>
                                </tr>
                                <tr>
                                    <td>Mesto</td>
                                    <td><?php echo CalendarHelper::showDash($order['shipping_city']); ?></td>
                                </tr>
                                <tr>
                                    <td>PSČ</td>
                                    <td><?php echo CalendarHelper::showDash($order['shipping_zip']); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- ./row -->

        <div class="row">
            <div class="col-xs-12">
                <div class="content-box box-orange">
                    <div class="content-header">
                        <h2>Detail platby kartou</h2>
                        <h2 style="float: right;">
                            <?php if ($this->data['wp_payment']['id_paid']): ?>
                                <span style="color: green">ZAPLATENÁ</span>
                            <?php else: ?>
                                <span style="color: red">NE-ZAPLATENÁ</span>
                            <?php endif; ?>
                        </h2>
                    </div>
                    <div class="content-body no-padding">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <thead>
                                <tr>
                                    <td>Id</td>
                                    <td>OrderNumber</td>
                                    <td>ResponsePrCode</td>
                                    <td>ResponseSrCode</td>
                                    <td>ResponseFullText</td>
                                    <td>CreatedAt</td>
                                    <td>UpdatedAt</td>
                                    <td>Status</td>
                                </tr>
                            </thead>
                            <?php for ($i = 0; $i < count($gp_payment); $i++) : ?>
                                <tr>
                                    <td><?php echo $gp_payment[$i]['id']; ?></td>
                                    <td><?php echo $gp_payment[$i]['order_number']; ?></td>
                                    <td><?php echo $gp_payment[$i]['response_prcode']; ?></td>
                                    <td><?php echo $gp_payment[$i]['response_srcode']; ?></td>
                                    <td><?php echo $gp_payment[$i]['response_resulttext']; ?></td>
                                    <td><?php echo $gp_payment[$i]['created_at']; ?></td>
                                    <td><?php echo $gp_payment[$i]['updated_at']; ?></td>
                                    <td><?php echo $gp_payment[$i]['status']; ?></td>
                                </tr>
                            <?php endfor; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="content-box box-orange">
                    <div class="content-header">
                        <h2>Cena objednávky</h2>
                    </div>
                    <div class="content-body" style="overflow: auto;">
                        <div class="price-sumary">
                            <div>
                                <span class="price-description">Cena objednávky:</span>
                                <?php echo $order['price_calendars']; ?> &euro;
                            </div>
                            <div>
                                <span class="price-description">Cena po zľave:</span>
                                <span id="price-calendars">
                                    <?php echo Price::applyDiscount($order['price_calendars'], $order['discount']); ?>
                                </span>  &euro;
                                <?php if ($order['discount'] > 0): ?>
                                    <span class="label label-success calendar-label" id="price-discount-info">
                                            Zľava <?php echo $order['discount']; ?> %
                                        </span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="price-description">Dopravné a balné:</span>
                                <span id="price-shipping-and-packing"><?php echo $order['price_shipping_and_packing']; ?></span>  &euro;
                            </div>
                            <div>
                                <span class="price-description">Finálna cena s DPH:</span>
                                <span id="price-total"><?php echo Price::applyDiscount($order['price_calendars'], $order['discount']) + $order['price_shipping_and_packing']; ?></span>  &euro;
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden data used by JS -->
        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>"/>
        <input type="hidden" name="user_id" value="<?php echo $order['user_id']; ?>"/>
        <?php for ($i = 0; $i < count($this->data['order']['calendars']); $i++) : ?>
            <?php $calendar = $this->data['order']['calendars'][$i];?>
            <input type="hidden" name="calendar_id[]"
                   data-front-page="<?php echo $calendar['front_page'] == "1" ? "true" : "false"; ?>"
                   value="<?php echo $calendar['cal_id']; ?>"
                   data-type="<?php echo lcfirst($calendar['type']); ?>"/>
        <?php endfor; ?>

        <div class="popup" data-popup="help-modal-edit">
            <div class="popup-inner">
                <h2>Wow! This is Awesome! (Popup #1)</h2>
                <p>Donec in volutpat nisi. In quam lectus, aliquet rhoncus cursus a, congue et arcu. Vestibulum tincidunt neque id nisi pulvinar aliquam. Nulla luctus luctus ipsum at ultricies. Nullam nec velit dui. Nullam sem eros, pulvinar sed pellentesque ac, feugiat et turpis. Donec gravida ipsum cursus massa malesuada tincidunt. Nullam finibus nunc mauris, quis semper neque ultrices in. Ut ac risus eget eros imperdiet posuere nec eu lectus.</p>
                <p><a data-popup-close="help-modal-edit" href="#" class="btn-cal btn-orange" style="float: right;">Zavrieť</a></p>
                <a class="popup-close" data-popup-close="help-modal-edit" href="#">x</a>
            </div>
        </div>

    </div>
</body>
</html>
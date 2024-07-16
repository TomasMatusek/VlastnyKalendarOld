<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php $order = $this->data['detail']['order']; ?>

<div id="calendar-order">

    <?php if(isset($this->data['detail']) && $this->data['detail'] != false) : ?>

            <div class="row">
                <div class="col-xs-12">
                    <div class="content-box box-orange">
                        <div class="content-header">
                            <h2>Vaša objednávka č. <?php echo str_pad($order['order_id'], 4, '0', STR_PAD_LEFT); ?></h2>
                            <span class="title-right">Objednávka odoslaná: <?php echo date('d.m.Y H:i', $order['order_sent']); ?></span>
                        </div>
                        <div class="content-body no-padding">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <thead>
                                <tr>
                                    <td width="10%">Typ</td>
                                    <td width="15%">Začiatok kalendára</td>
                                    <td width="15%">Vytvorený</td>
                                    <td width="15%">Počet kusov</td>
                                    <td width="15%">Titulná strana</td>
                                    <td width="15%">Cena za kus</td>
                                    <td width="15%">Cena s DPH</td>
                                </tr>
                                </thead>
                                <?php for ($i = 0; $i < count($this->data['detail']['calendars']); $i++) : ?>
                                    <?php $calendar = $this->data['detail']['calendars'][$i];?>
                                    <tr class="bottom-line calendar-row">
                                        <td><?php echo ucfirst($calendar['type']); ?></td>
                                        <td><?php echo ucfirst($calendar['start_month']) . ' ' .  ucfirst($calendar['start_year']); ?></td>
                                        <td><?php echo date('d.m.Y H:i', $calendar['create_time']); ?></td>
                                        <td><?php echo $calendar['quantity']; ?></td>
                                        <td><?php echo $calendar['front_page'] == 1 ? 'Áno' : 'Nie'; ?></td>
                                        <td><?php echo $calendar['order_sent_price']; ?> &euro;</td>
                                        <td><?php echo $calendar['order_sent_price'] * $calendar['quantity']; ?> &euro;</td>
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
                        <div class="content-box box-orange mt-30 min-height-300">
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
                                            <textarea name="comment" disabled="disabled"><?php echo $order['comment']; ?></textarea>
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
                        <div class="content-box box-orange mt-30 min-height-300">
                            <div class="content-header">
                                <h2>Fakturačná adresa</h2>
                            </div>
                            <div class="content-body no-padding">
                                <table width="100%">
                                    <tr>
                                        <td width="40%">Meno a priezvisko</td>
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dodacia adresa -->
                <div id="col-3" class="shipping-address-form">
                    <div class="col-md-4">
                        <div class="content-box box-orange mt-30 min-height-300">
                            <div class="content-header">
                                <h2>Dodacia adresa</h2>
                            </div>
                            <div class="content-body no-padding">
                                <table width="100%">
                                    <tr>
                                        <td>Meno a priezvisko</td>
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
                            <a href="index.php?option=com_calendar&view=user&layout=list" class="btn-cal  btn-orange pull-right" style="margin-top: 25px;">
                                Späť na zoznam objednávok
                            </a>
                        </div>
                    </div>
                </div>
            </div>

    <?php endif; ?>
</div>

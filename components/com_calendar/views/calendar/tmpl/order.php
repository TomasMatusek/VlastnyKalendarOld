<?php defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript" src="<?php echo CAL_ROOT_WEB . '/components/com_calendar/assets/js/selectOptions.js';?>"></script>
<script type="text/javascript" src="<?php echo CAL_ROOT_WEB . '/components/com_calendar/assets/js/calendar_price.js';?>"></script>

<div id="calendar-order">

    <?php if(isset($this->data['finished_calendars']) && $this->data['finished_calendars'] != false) : ?>
        <form method="post" action="/index.php?option=com_calendar&task=user.submit" id="order_form" accept-charset="UTF-8">
            <div class="row">
                <!--
                <div class="alert alert-danger">
                    <strong style="font-size: 20px;">Pozor!</strong>
                    <p>Pokiaľ si zvolíte spôsob doručenia iný ako osobný, Vaša objednávka bude doručená až po novom roku, t.j. po 2.1.2020.</p>
                </div>
                -->
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="content-box box-orange">
                        <div class="content-header">
                            <h2>Vaša objednávka</h2>
                        </div>
                        <div class="content-body no-padding">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <thead>
                                    <tr>
                                        <td width="5%">Typ</td>
                                        <td width="15%">Začiatok kalendára</td>
                                        <td width="15%">Vytvorený</td>
                                        <td width="15%">Základna cena</td>
                                        <td width="15%">Príplatok za titulnú stranu</td>
                                        <td width="10%">Počet kusov</td>
                                        <td width="10%">Cena s DPH</td>
                                        <td width="8%"></td>
                                        <td width="8%"></td>
                                    </tr>
                                </thead>
                                <?php $final_price = 0; ?>
                                <?php for ($i = 0; $i < count($this->data['finished_calendars']); $i++) : ?>
                                    <?php $calendar = $this->data['finished_calendars'][$i];?>
                                    <?php $final_price += $calendar['price_total']; ?>
                                    <tr class="bottom-line calendar-row" id="calendar_<?php echo $calendar['cal_id']; ?>">
                                        <td class="calendar_type">
                                            <?php echo ucfirst($calendar['type']); ?>
                                            <input type="hidden" name="calendar_ids[]" value="<?php echo $calendar['cal_id']; ?>"/>
                                        </td>
                                        <td class="calendar_start_month">
                                            <?php echo ucfirst($calendar['start_month']) . ' ' .  ucfirst($calendar['start_year']); ?>
                                        </td>
                                        <td class="calendar_create_time">
                                            <?php echo date('d.m.Y H:i', $calendar['create_time']); ?>
                                        </td>
                                        <td class="calendar_price">
                                            <span><?php echo $calendar['price']; ?></span> &euro;
                                            <?php if ($calendar['discount_percentage'] > 0): ?>
                                                <span class="label label-success calendar-label">-<?php echo $calendar['discount_percentage']; ?> &percnt;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="calendar_price_cover">
                                            <span><?php echo $calendar['price_cover']; ?></span> &euro;
                                        </td>
                                        <td class="calendar_quantity">
                                            <select name="quantity[]"
                                                    style="background: #f5f5f5;" data-value="<?php echo $calendar['quantity']; ?>"
                                                    data-calendar-id="<?php echo $calendar['cal_id']; ?>"
                                                    value="<?php echo $calendar['quantity']; ?>">

                                                <?php for ($x = 1; $x < 101; $x++) : ?>
                                                    <option value="<?php echo $x; ?>" <?php echo $calendar['quantity'] == $x ? "selected='selected'" : ""; ?>><?php echo $x; ?> ks</option>
                                                <?php endfor; ?>
                                            </select>
                                        </td>
                                        <td class="calendar_price_total" data-value="<?php echo $calendar['price_total']; ?>">
                                            <?php echo $calendar['price_total']; ?> &euro;
                                        </td>
                                        <td class="calendar_edit">
                                            <a href="index.php?option=com_calendar&task=calendar.update&cal_id=<?php echo $calendar['cal_id']; ?>" class="orange">Upraviť</a>
                                        </td>
                                        <td class="calendar_delete">
                                            <a onclick="return confirm('Naozaj chcete zmazať tento kalendár ?')" href="index.php?option=com_calendar&task=calendar.deleteFromOrderForm&cal_id=<?php echo $calendar['cal_id']; ?>" class="orange">Zmazať</a>
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
                    <div class="col-md-6">
                        <div class="content-box box-orange mt-30 min-height-400">
                            <div class="content-header">
                                <h2>Informácie o objednávke</h2>
                            </div>
                            <div class="content-body no-padding">
                                <table width="100%">
                                    <tr>
                                        <td width="35%">Spôsob doručenia</td>
                                        <td width="65%">
                                            <select name="transport_method" id="shipping" aria-invalid="false">
                                                <option value="personal" selected="selected" data-value="<?php echo CAL_SHIPPING_PERSONAL; ?>">
                                                    Osobný odber (<?php echo CAL_SHIPPING_PERSONAL; ?> &euro;)
                                                </option>
                                                <option value="depo" data-value="<?php echo CAL_SHIPPING_DEPO; ?>">
                                                    Odberné miesto (<?php echo CAL_SHIPPING_DEPO; ?> &euro;)
                                                </option>
                                                <option value="courier" data-value="<?php echo CAL_SHIPPING_COURIER; ?>">
                                                    Kuriérom (<?php echo CAL_SHIPPING_COURIER; ?> &euro;)
                                                </option>
                                                <option value="post-sk" data-value="<?php echo CAL_SHIPPING_POST_SK; ?>">
                                                    Slovenskou poštou (<?php echo CAL_SHIPPING_POST_SK; ?> &euro;)
                                                </option>
                                                <option value="post-cz" data-value="<?php echo CAL_SHIPPING_POST_CZ; ?>">
                                                    Česká Republika - poštou (<?php echo CAL_SHIPPING_POST_CZ; ?> &euro;)
                                                </option>
                                                <option value="post-eu" data-value="<?php echo CAL_SHIPPING_POST_EU; ?>">
                                                    Európska únia - poštou (<?php echo CAL_SHIPPING_POST_EU; ?> &euro;)
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Spôsob platby</td>
                                        <td>
                                            <select name="payment_method" id="payment_method">
                                                <option value="cash" selected="selected" data-value="<?php echo CAL_PAYMENT_CASH; ?>">
                                                    Platba v hotovosti (+ <?php echo CAL_PAYMENT_CASH; ?> &euro;)
                                                </option>
                                                <option value="on-delivery" data-value="<?php echo CAL_PAYMENT_ON_DELIVERY; ?>">
                                                    Dobierka (+ <?php echo CAL_PAYMENT_ON_DELIVERY; ?> &euro;)
                                                </option>
                                                <option value="card" data-value="<?php echo CAL_PAYMENT_CARD; ?>">
                                                    Platba kartou online (+ <?php echo CAL_PAYMENT_CARD; ?> &euro;)
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr id="payment-description" style="display: none">
                                        <td>
                                            <img src="https://vlastnykalendar.sk/components/com_calendar/assets/img/GP_webpay.png" width="150px"/>
                                        </td>
                                        <td>
                                            Platba kartou online prostredníctvom platobného portálu po odoslaní objednávky. <b style="color: red;">Objednávku je potrebné uhradiť vopred !!!</b>
                                        </td>
                                    </tr>
                                    <tr class="depo-place-picker" style="display: none;">
                                        <td>Odberné miesto</td>
                                        <td style="text-align: center;">
                                            <button type="button" class="btn-cal btn-orange" data-popup-open="depo_pick_place" style="float: left; width: 85%;">
                                                Vybrať odberné miesto
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="depo-place-detail" style="display: none;">
                                        <td>Zvolené miesto</td>
                                        <td>
                                            <div id="depo-place-picked-detail">
                                                Detail
                                            </div>
                                            <div id="depo-place-not-picked" style="display: none; color: red;">
                                                Nezvolili ste si žiadne miesto!
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="depo-place-picker" style="display: none;">
                                        <td></td>
                                        <td><span style="color: red;" class="browser-warning">POZOR! Niektoré staršie prehliadače môžú mať problém a výberom odberného miesta. V takomto prípade si prosím stiahnite najnovšiu verziu prehliadača <a href="https://www.google.com/chrome/">Google Chrome</a>. Ďakujeme.</span></td>
                                    </tr>
                                    <tr>
                                        <td>Zľavový kód</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" id="special_code_input" class="form-control" placeholder="Zadajte zľavový kód">
                                                <input type="hidden" name="special_code" id="special_code_hidden" disabled="disabled">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="special_code_verify">Použiť</button>
                                                    <button class="btn btn-default" type="button" id="special_code_remove" style="display: none;">Zrušiť</button>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="no-bottom-line">
                                        <td colspan="2" width="100%">Poznánka k objednávke:</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <textarea name="comment" placeholder="Sem zadajte Vašu poznámku, ak nejakú máte ..." id="comment"></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DEPO - Vyber odberneho miesta - Modal Dialog -->

                <div class="popup" data-popup="depo_pick_place">
                    <div class="popup-inner-depo">
                        <h2 style="text-align: center;">Vyberte si prosím miesto vyzdvihnutia</h2>
                        <div style="text-align: center; margin: 10px auto;">
                            <?php $depoPickUpPlaceID = uniqid() . '-gfd78s-' . uniqid(); ?>
                            <input type="hidden" name="depo_pickup_place_id" id="depo-pickup-place-order-id" value="<?php echo $depoPickUpPlaceID; ?>" data-done="0"/>
                            <iframe src="https://admin.depo.sk/eshop?c=132&o=<?php echo $depoPickUpPlaceID; ?>" width="800" height="600" frameborder="0" allow="fullscreen"></iframe>
                            <p><a data-popup-close="depo_pick_place" id="depo_close_popup" href="#" class="btn-cal btn-orange" style="width: 100%;">Zavrieť</a></p>
                            <a class="popup-close" data-popup-close="depo_pick_place" href="#">x</a>
                        </div>
                    </div>
                </div>

                <!-- Fakturacna adresa -->
                <div id="col-2" class="billing-address-form">
                    <div class="col-md-6">
                        <div class="content-box box-orange mt-30 min-height-400">
                            <div class="content-header">
                                <h2>Fakturačná adresa</h2>
                                <div class="pull-right">
                                    <input type="checkbox" name="is_company" id="is_company"/>
                                    <label for="is_company" style="font-size: 14px;">
                                        Tovar nakupujem na firmu
                                    </label>
                                </div>
                            </div>
                            <div class="content-body no-padding">
                                <table width="100%">
                                    <tr id="tr_billing_name">
                                        <td width="40%">Meno a priezvisko</td>
                                        <td width="60%"><input type="text" name="billing_name" id="billing_name" placeholder="Meno a priezvisko"/></td>
                                    </tr>
                                    <tr id="tr_billing_phone">
                                        <td>Tel.č. (+421xxxxxxxxx):</td>
                                        <td><input type="text" name="billing_phone" id="billing_phone" placeholder="+421900123456"/></td>
                                    </tr>
                                    <tr id="tr_billing_mail">
                                        <td>E-mail</td>
                                        <td><input type="text" name="billing_mail" id="billing_mail" placeholder="E-mailová adresa"/></td>
                                    </tr>
                                    <tr id="tr_billing_address" style="display: none;">
                                        <td>Ulica</td>
                                        <td><input type="text" name="billing_address" id="billing_address" placeholder="Adresa doručenia"/></td>
                                    </tr>
                                    <tr id="tr_billing_address_number" style="display: none;">
                                        <td>Číslo domu</td>
                                        <td><input type="text" name="billing_address_number" id="billing_address_number" placeholder="Číslo domu"/></td>
                                    </tr>
                                    <tr id="tr_billing_city" style="display: none;">
                                        <td>Mesto</td>
                                        <td><input type="text" name="billing_city" id="billing_city" placeholder="Mesto"/></td>
                                    </tr>
                                    <tr id="tr_billing_zip" style="display: none;">
                                        <td>PSČ</td>
                                        <td><input type="text" name="billing_zip" id="billing_zip" placeholder="Poštové smerové číslo"/></td>
                                    </tr>

                                    <!-- Company -->
                                    <tr id="tr_billing_ico" style="display: none;">
                                        <td>IČO</td>
                                        <td><input type="text" name="billing_ico" id="billing_ico" placeholder="IČO"/></td>
                                    </tr>
                                    <tr id="tr_billing_dic" style="display: none;">
                                        <td>DIČ</td>
                                        <td><input type="text" name="billing_dic" id="billing_dic" placeholder="DIČ" data-required="false"/></td>
                                    </tr>
                                    <tr id="tr_billing_icdph" style="display: none;">
                                        <td>IČ DPH</td>
                                        <td><input type="text" name="billing_icdph" id="billing_icdph" placeholder="IČ DPH" data-required="false"/></td>
                                    </tr>

                                    <tr id="tr_different_shipping_address" style="display: none;">
                                        <td>
                                            Dodacia adresa
                                        </td>
                                        <td>
                                            <input type="checkbox" name="different_shipping_address" id="different_shipping_address"/> Iná ako fakturačná
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dodacia adresa -->
                <div id="col-3" class="shipping-address-form" style="display: none;">
                    <div class="col-md-4">
                        <div class="content-box box-orange mt-30 min-height-400">
                            <div class="content-header">
                                <h2>Dodacia adresa</h2>
                            </div>
                            <div class="content-body no-padding">
                                <table width="100%">
                                    <tr id="tr_shipping_name">
                                        <td>Meno a priezvisko</td>
                                        <td><input type="text" name="shipping_name" id="shipping_name" placeholder="Meno a priezvisko"/></td>
                                    </tr>
                                    <tr id="tr_shipping_address">
                                        <td>Ulica</td>
                                        <td><input type="text" name="shipping_address" id="shipping_address" placeholder="Adresa doručenia"/></td>
                                    </tr>
                                    <tr id="tr_shipping_address_number">
                                        <td>Číslo domu</td>
                                        <td><input type="text" name="shipping_address_number" id="shipping_address_number" placeholder="Číslo domu"/></td>
                                    </tr>
                                    <tr id="tr_shipping_phone">
                                        <td>Tel.č. (+421xxxxxxxxx):</td>
                                        <td><input type="text" name="shipping_phone" id="shipping_phone" placeholder="+421900123456"/></td>
                                    </tr>
                                    <tr id="tr_shipping_city">
                                        <td>Mesto</td>
                                        <td><input type="text" name="shipping_city" id="shipping_city" placeholder="Mesto"/></td>
                                    </tr>
                                    <tr id="tr_shipping_zip">
                                        <td>PSČ</td>
                                        <td><input type="text" name="shipping_zip" id="shipping_zip" placeholder="Poštové smerové číslo"/></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- ./row -->

            <div class="alert alert-danger" role="alert" style="display: none;" id="depo-error-place-not-selected">
                <div class="row">
                    <div class="col-xs-12">
                        <p><strong>Chyba pri odosielaní!</strong></p>
                        <p>Vybrali ste si vyzdvihnutie na odbernom mieste ale nezvolili ste si miesto. Kliknite na "Vybrať odberné miesto" a zvolte si výdajne miesto z ponuky.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="content-box box-orange">
                        <div class="content-header">
                            <h2>Odoslanie objednávky</h2>
                        </div>
                        <div class="content-body" style="overflow: auto;">
                            <div class="price-sumary">
                                <div>
                                    <div class="price-label">Cena objednávky:</div>
                                    <div id="price-calendars">0</div> &euro;
                                </div>
                                <div>
                                    <div class="price-label">Cena objednávky so zľavou:</div>
                                    <div id="price-calendars-discount">0</div> &euro;
                                    <span class="label label-success calendar-label" style="display: none;" id="price-discount-info"></span>
                                </div>
                                <div>
                                    <div class="price-label">Dopravné a balné:</div>
                                    <div id="price-shipping-and-packing">0</div> &euro;
                                </div>
                                <div>
                                    <div class="price-label">Finálna cena s DPH:</div>
                                    <div id="price-total">0</div> &euro;
                                </div>
                            </div>
                            <button type="submit" name="order_submit" id="order_submit" value="" class="btn-cal btn-orange pull-right send-order-btn">
                                Odoslať objednávku
                            </button>
                            <button id="order_submit_in_progress" type="button" class="btn-cal btn-gray pull-right send-order-btn" style="display: none; cursor: not-allowed;" disabled="disabled">
                                Odosielam objednávku ...
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="payment_price_for_item" id="payment_price_for_item" value="<?php echo CAL_PAYMENT_PRICE_PER_ITEM; ?>" />
            <input type="hidden" name="price_shipping_and_packing" value="0"/>
            
            <!-- Sposob platby -->
            <input type="hidden" name="cash" value="0" />
            <input type="hidden" name="post-sk" value="<?php echo CAL_PAYMENT_POST; ?>" />
            <input type="hidden" name="transfer-sk" value="<?php echo CAL_PAYMENT_TRANSFER; ?>" />
            <input type="hidden" name="courier" value="<?php echo CAL_PAYMENT_COURIER; ?>" />
            <input type="hidden" name="depo" value="<?php echo CAL_PAYMENT_DEPO; ?>" />
            <input type="hidden" name="transfer-cz" value="<?php echo CAL_PAYMENT_TRANSFER_CZ; ?>" />
            <input type="hidden" name="transfer-eu" value="<?php echo CAL_PAYMENT_TRANSFER_EU; ?>" />
            <input type="hidden" name="gp_webpay" value="<?php echo CAL_PAYMENT_GP_WEBPAY; ?>" />

            <input type="hidden" name="cash" value="<?php echo CAL_PAYMENT_CASH; ?>" />
            <input type="hidden" name="on-delivery" value="<?php echo CAL_PAYMENT_ON_DELIVERY; ?>" />
            <input type="hidden" name="card" value="<?php echo CAL_PAYMENT_CARD; ?>" />

            <input type="hidden" name="user_id" value="<?php echo $this->data['user_id']; ?>" />
            <?php echo JHtml::_( 'form.token' ); ?>
        </form>
    <?php endif; ?>
</div>


<?php defined('_JEXEC') or die('Restricted access');

$order = $this->data['order']['order'];
$calendars = $this->data['order']['calendars'];
$invoice_number = $this->data['invoice_number'];
$invoice_date = $this->data['invoice_date'];
$invoice_type = $this->data['invoice_type'];
$invoice_type_translate = $this->data['invoice_type_translate'];
$date = new DateTime();
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            * { font-family: DejaVu Sans !important; }
            .br-0 { border-right: 0px !important; }
            .bt-0 { border-top: 0px !important; }
            .bl-0 { border-left: 0px !important; }
            .bb-0 { border-bottom: 0px !important; }
            .p-0 { padding: 0px !important; }
            .table td, .table th { border-radius: 0px !important; border-color: #eee; }
            .table-bordered { border-radius: 0px !important; border-color: #eee !important; }
            .table-bordered th, .table-bordered td { border-radius: 0px !important; }
            #redim-cookiehint { display: none !important; }
        </style>
    </head>

    <body>
        <div class="invoice">

            <table class="table bt-0">
                <tr>
                    <td class="title bt-0" style="font-size: 18px; font-weight: 600;">
                        <?php echo $invoice_type_translate; ?>
                        <span class="pull-right">
                            <?php if($invoice_type == 'invoice'): ?>
                                <?php echo 'KA' . date('Y') . $invoice_number; ?>
                            <?php else: ?>
                                <?php echo 'KA' . date('Y') . str_pad($order['order_id'], 4, '0', STR_PAD_LEFT); ?>
                            <?php endif; ?>
                        </span>
                    </td>
                </tr>
            </table>

            <?php if($invoice_type == 'invoice'): ?>
                <table class="table" id="invoice-table">
                    <tr>
                        <td class="left-col p-0 bb-0" width="50%">
                            <table class="table table-bordered br-0 bt-0 bl-0" width="100%">
                                <tr>
                                    <td class="title bt-0 pb-15" style="text-align: center;">
                                        <span style="font-size: 18px; font-weight: 600;">Dodávateľ</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="supplier">
                                        <div class="desc">
                                            <div class="aldo-logo" style="background: url('http://www.vlastna-fotokniha.sk/images/logoAldo85.png') no-repeat; width: 300px; height: 88px;"></div>
                                            <address>
                                                <strong>Alica Dórová - ALDO</strong><br>
                                                Ursínyho 1<br>
                                                831 02, Bratislava<br>
                                                Slovenská republika
                                            </address>
                                            <div class="clear">&nbsp;</div>
                                            <address>
                                                <strong>IČO:</strong> 34463500<br>
                                                <strong>DIČ:</strong> 1020189115<br>
                                                <strong>IČ DPH:</strong> SK 1020189115<br>
                                                <p class="mt-25">ev.č.: žo-96/03270/001, reg. č. 2530/96</p>
                                                <p class="mt-25"><strong>Prevádzka: </strong>tlač, Ursínyho 1, 831 02 Bratislava</p>
                                            </address>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td class="right-col bt-0 p-0 bl-0 bb-0" width="50%">
                            <table class="table table-bordered bb-0">
                                <tr>
                                    <td class="title bt-0" style="text-align: center;">
                                        <span style="font-size: 18px; font-weight: 600;">Odberateľ</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="send-window" style="padding: 30px 0px; text-align: center; font-size: 16px;">
                                        <?php echo $order['billing_name']; ?><br/>
                                        <?php echo $order['billing_address']; ?><br/>
                                        <?php if (strlen($order['billing_city']) > 0): ?>
                                            <?php echo $order['billing_city'] . ", " . $order['billing_zip']; ?><br/>
                                        <?php endif; ?>
                                        <?php if (strlen($order['billing_ico']) > 0): ?>
                                            IČO: <?php echo $order['billing_ico']; ?><br/>
                                        <?php endif; ?>
                                        <?php if (strlen($order['billing_dic']) > 0): ?>
                                            DIČ: <?php echo $order['billing_dic']; ?><br/>
                                        <?php endif; ?>
                                        <?php if (strlen($order['billing_icdph']) > 0): ?>
                                            IČ DPH: <?php echo $order['billing_icdph']; ?><br/>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($order['shipping_address'] != ''): ?>
                                    <tr>
                                        <td class="title" style="text-align: center;">
                                            <span style="font-size: 18px;">Dodacia adresa</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="send-window" style="padding: 30px 0px; text-align: center; font-size: 16px;">
                                            <?php echo $order['shipping_name']; ?><br/>
                                            <?php echo $order['shipping_address']; ?><br/>
                                            <?php if (strlen($order['shipping_city']) > 0): ?>
                                                <?php echo $order['shipping_city'] . ", " . $order['shipping_zip']; ?><br/>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="dates p-0 bl-0">
                                        <table class="table table-bordered mb-0 bt-0 br-0">
                                            <tr>
                                                <td>Dátum vyhotov.</td>
                                                <td>Dátum splatnosti</td>
                                                <td>Dátum dodania</td>
                                            </tr>
                                            <?php if ( ! isset($this->date)): ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $this->data['invoice_date']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $this->data['invoice_date_plus_week']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $this->data['invoice_date_plus_week']; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td colspan="2">Číslo účtu: <strong>0011535932/0900</strong></td>
                                                <td>VS: <?php echo 'KA' . date('Y') . str_pad($order['order_id'], 4, '0', STR_PAD_LEFT); ?></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">Banka: <strong>Slovenská sporiteľňa, a.s.</strong></td>
                                                <td>KS: 0308</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">IBAN: <strong>SK2109000000000011535932</strong></td>
                                                <td>ŠS:</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">SWIFT: <strong>GIBASKBX</strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    Spôsob dopravy:
                                                    <strong><?php echo CalendarHelper::transportMethodTranslate($order['transport_method']); ?></strong><br/>
                                                    Spôsob platby:
                                                    <strong><?php echo CalendarHelper::paymentMethodTranslate($order['payment_method']); ?></strong><br/>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>

            <?php if ($invoice_type == 'order'): ?>
                <table width="100%">
                    <tr>
                        <td width="100%">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered mt-15">
                                <tr>
                                    <td colspan="2">Číslo účtu: <strong>0011535932/0900</strong></td>
                                    <td>VS: <strong><?php echo 'KA' . date('Y') . str_pad($order['order_id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Banka: <strong>Slovenská sporiteľňa, a.s.</strong></td>
                                    <td>KS: 0308</td>
                                </tr>
                                <tr>
                                    <td colspan="2">IBAN: <strong>SK2109000000000011535932</strong></td>
                                    <td>ŠS:</td>
                                </tr>
                                <tr>
                                    <td colspan="3">SWIFT: <strong>GIBASKBX</strong></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>

            <table class="table table-bordered mt-15" id="invoice-content">
                <tr>
                    <td width="5%">P.č.</td>
                    <td width="10%">Číslo položky</td>
                    <td width="20%">Názov položky</td>
                    <td width="5%">Množstvo</td>
                    <td width="3%">MJ</td>
                    <td width="7%">Zľava %</td>
                    <td width="10%">Cena bez DPH</td>
                    <td width="5%">DPH %</td>
                    <td width="10%">Spolu s DPH</td>
                </tr>
                <?php $i = 1; $price_total = 0; ?>
                <?php foreach ($calendars as $calendar): ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $calendar['cal_id']; ?></td>
                        <td>Kalendár typ <?php echo strtoupper($calendar['type']); ?> <?php echo $calendar['front_page'] == '1' ? 's titulnou stranou' : '';?></td>
                        <td><?php echo $calendar['quantity']; ?></td>
                        <td>ks</td>
                        <td><?php echo $order['discount']; ?></td>
                        <td><?php echo Price::dph(Price::applyDiscount($calendar['order_sent_price'] * $calendar['quantity'], $order['discount'])); ?></td>
                        <td>20</td>
                        <td><?php echo Price::applyDiscount($calendar['order_sent_price'] * $calendar['quantity'], $order['discount']); ?></td>
                    </tr>
                    <?php $i++; $price_total += Price::applyDiscount($calendar['order_sent_price'] * $calendar['quantity'], $order['discount']); ?>
                <?php endforeach; ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td>0</td>
                    <td>Dopravné a balné</td>
                    <td>1</td>
                    <td>ks</td>
                    <td>0</td>
                    <td><?php echo Price::dph($order['price_shipping_and_packing']); ?></td>
                    <td>20</td>
                    <td><?php echo $order['price_shipping_and_packing']; ?></td>
                    <?php $i++; $price_total += $order['price_shipping_and_packing']; ?>
                </tr>
            </table>

            <table width="100%">
                <tr>
                    <td width="25%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table mt-25">
                            <tr>
                                <td style="border-top:0px; border-left: 0px; width: 15em;">
                                    <div class="aldo-signarute" style="background: url('http://www.vlastna-fotokniha.sk/images/signature.jpg') no-repeat; width: 200px; height: 140px; margin: 0 auto;"></div>
                                </td>
                            </tr>
                            <tr>
                                <td style="border-left:0; text-align: center">
                                    Pečiatka a podpis
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td width="25%">
                        <!-- space -->
                    </td>

                    <td width="50%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered mt-15">
                            <tr>
                                <td>Celková fakturovaná suma:</td>
                                <td>EUR</td>
                                <td><?php echo $price_total; ?> €</td>
                            </tr>
                            <tr>
                                <td>K úhrade:</td>
                                <td>EUR</td>
                                <td><b><?php echo $price_total; ?> €</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </div>
    </body>
</html>
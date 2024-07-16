<?php
defined('_JEXEC') or die('Restricted access');
$date = new DateTime();
?>

<div id="calendar-users-orders">
    <div class="row">
        <div class="col-xs-12">

            <div class="content-box box-orange">
                <div class="content-header">
                    <h2>Vaše objednávky</h2>
                </div>
                <div class="content-body no-padding">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <thead>
                            <tr>
                                <td width="5%"><strong>Id</strong></td>
                                <td width="10%"><strong>Faktura</strong></td>
                                <td width="12%"><strong>Dátum vytvorenia</strong></td>
                                <td width="10%"><strong>Počet kusov</strong></td>
                                <td width="10%"><strong>Suma s DPH</strong></td>
                                <td width="15%"><strong>Doparava</strong></td>
                                <td width="15%"><strong>Platba</strong></td>
                                <td width="15%"><strong>Stav objednávky</strong></td>
                                <td width="5%"><strong>Detail</strong></td>
                            </tr>
                        </thead>
                        <?php foreach ($this->data['orders'] as $index => $order): ?>
                            <tbody>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td>
                                        <a class="orange" href="<?php echo "https://www.vlastnykalendar.sk/index.php?option=com_calendar&task=user.downloadOrderPdf&order_id=" . $order['order_id']; ?>">
                                            Stiahnuť
                                        </a>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', $order['order_sent']); ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><?php echo Price::applyDiscount($order['price_calendars'], $order['discount']) + $order['price_shipping_and_packing']; ?> &euro;</td>
                                    <td><?php echo CalendarHelper::transportMethodTranslate($order['transport_method']); ?></td>
                                    <td><?php echo CalendarHelper::paymentMethodTranslate($order['payment_method']); ?></td>
                                    <td><?php echo CalendarHelper::getOrderStatusTranslate($order['status']); ?></td>
                                    <td width="20%">
                                        <a class="orange" href="index.php?option=com_calendar&view=user&layout=detail&order_id=<?php echo $order['order_id']; ?>">Detail</a>
                                    </td>
                                </tr>
                            </tbody>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
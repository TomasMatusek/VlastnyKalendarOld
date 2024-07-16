<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$orders = $this->data['orders'];
$status = $this->data['status'];
$search = $this->data['search'];
?>

<div id="calendars-orders-admin-view">

    <div class="row">
        <div class="col-xs-6">
            <div class="content-box box-orange">
                <div class="content-header">
                    <h2>Filter objednávok</h2>
                </div>
                <div class="content-body no-padding">
                    <table width="100%">
                        <tr>
                            <td width="30%">Podľa statusu</td>
                            <td width="70%">
                                <div class="btn-group" role="group">
                                    <a href="index.php?option=com_calendar&view=admin&layout=orders"
                                       class="btn btn-default btn-filter <?php echo $status == '' ? 'btn-filter-active' : ''; ?>">
                                        Všetky
                                    </a>
                                    <a href="index.php?option=com_calendar&view=admin&layout=orders&status=sent"
                                       class="btn btn-default btn-filter <?php echo $status == 'sent' ? 'btn-filter-active' : ''; ?>">
                                        Nová
                                    </a>
                                    <a href="index.php?option=com_calendar&view=admin&layout=orders&status=in_progress"
                                       class="btn btn-default btn-filter <?php echo $status == 'in_progress' ? 'btn-filter-active' : ''; ?>">
                                        Spracováva sa
                                    </a>
                                    <a href="index.php?option=com_calendar&view=admin&layout=orders&status=done"
                                       class="btn btn-default btn-filter <?php echo $status == 'done' ? 'btn-filter-active' : ''; ?>">
                                        Vybavená
                                    </a>
                                    <a href="index.php?option=com_calendar&view=admin&layout=orders&status=canceled"
                                       class="btn btn-default btn-filter <?php echo $status == 'canceled' ? 'btn-filter-active' : ''; ?>">
                                        Zrušená
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Podľa mena a adresy</td>
                            <td>
                                <div class="input-group" style="width: 345px;">
                                    <form action="/index.php" method="get" style="display: inline-table; margin: 0px;">
                                        <input type="hidden" name="option" value="com_calendar">
                                        <input type="hidden" name="view" value="admin">
                                        <input type="hidden" name="layout" value="orders">
                                        <input type="text" name="search" class="form-control form-control-filter" placeholder="Zadajte meno alebo adresu" value="<?php echo $search; ?>">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-side" type="submit">Hľadaj</button>
                                        </span>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Akcie</td>
                            <td id="delete-orders-files-button-wrapper">
                                <a role="button" data-popup-open="delete-order-files" id="delete-order-files" class="btn btn-default btn-filter">
                                    Zmazať fotky a ZIP subory vybranych kalendarov
                                </a>
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
                    <table width="100%">
                        <tr>
                            <td style="text-align: center">
                                <a role="button" href="index.php?option=com_calendar&view=admin&layout=coupons" class="btn-cal btn-orange" style="width: 225px">
                                    Zľavové kupóny
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center">
                                <a role="button" href="index.php?option=com_calendar&view=admin&layout=sales" class="btn-cal btn-orange" style="width: 225px">
                                    Kalendárové zľavy
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center">
                                <a role="button" href="/index.php?option=com_calendar&task=admin.getClearDiskData&hash=QZz2HvhejCp0Y6yQZtMXeWam514CIxNgmUHJpsELH5M6kIxK5gJIMzoqDjoCN" class="btn-cal btn-orange" style="width: 225px">
                                    Čistenie diskového priestoru
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center">
                                <a role="button" href="/index.php?option=com_calendar&task=admin.removeEmptyFolders&hash=joCNqXq0MIlDsWMjQYzdGBrSpyLCjcLJ4RcXGGwSa0rvnnMxAnZi6yrijNdr5dLIVUnAW28lJPLtQmbUaoT" class="btn-cal btn-orange" style="width: 225px">
                                    Premazanie prazdnych priecinkov
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="content-box box-orange">
        <div class="content-body no-padding">
            <table width="100%">
                <thead>
                    <tr>
                        <td><input type="checkbox" id="select-all-orders"/></td>
                        <td><strong>Faktúra</strong></td>
                        <td><strong>ID</strong></td>
                        <td><strong>Dátum</strong></td>
                        <td><strong>Meno</strong></td>
                        <td><strong>Adresa</strong></td>
                        <td><strong>Platba</strong></td>
                        <td><strong>Doprava</strong></td>
                        <td><strong>Kusov</strong></td>
                        <td><strong>Cena</strong></td>
                        <td><strong>Status</strong></td>
                        <td><strong>Detail</strong></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $index => $order): ?>
                        <tr>
                            <td data-name="select-order">
                                <?php $calendar_command_ids = ''; ?>
                                <?php foreach($order['order_details']['calendars'] as $key => $calendar): ?>
                                    <?php $calendar_command_ids .= $calendar['cal_id'] . ','; ?>
                                <?php endforeach; ?>
                                <input class="order-checkbox" type="checkbox" value="<?php echo $order['order_id']; ?>" data-ids="<?php echo $calendar_command_ids; ?>"/>
                            </td>
                            <td data-name="invoice_number">
                                <?php if ($order['ikros_url'] != '') {
                                    echo '<a class="orange" href="'.$order['ikros_url'].'">IKROS</a>';
                                } else if ($order['invoice_number'] != null) {
                                    echo $order['invoice_number'];
                                } else {
                                    echo '-';
                                } ?>
                            </td>
                            <td data-name="calendar_id">
                                <?php echo $order['order_id']; ?>
                            </td>
                            <td data-name="order_sent">
                                <?php echo date('d.m.Y', $order['order_sent']); ?>
                            </td>
                            <td data-name="name">
                                <?php echo $order['shipping_name']; ?>
                            </td>
                            <td data-name="address">
                                <?php echo CalendarHelper::showDash($order['shipping_address_number'] == '' ? $order['shipping_address'] : $order['shipping_address'] . " " . $order['shipping_address_number']); ?>
                            </td>
                            <td data-name="payment_method">
                                <?php echo CalendarHelper::paymentMethodTranslate($order['payment_method']); ?>
                            </td>
                            <td data-name="shipping_method">
                                <?php echo CalendarHelper::transportMethodTranslate($order['transport_method']); ?>
                            </td>
                            <td data-name="quantity">
                                <?php echo $order['quantity']; ?>
                            </td>
                            <td data-name="price_total">
                                <?php echo (Price::applyDiscount($order['price_calendars'], $order['discount']) + $order['price_shipping_and_packing']); ?>
                            </td>
                            <td data-name="order_status">
                                <span class="<?php echo CalendarHelper::orderStatusLabelClassName($order['status']); ?>">
                                    <?php echo CalendarHelper::orderStatusTranslate($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a class="orange" href="index.php?option=com_calendar&view=admin&layout=order&order_id=<?php echo $order['order_id']; ?>">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer-pagination">

        <nav aria-label="Page navigation" style="text-align: center;">
            <ul class="pagination">
                <?php $page_start = 0; ?>
                <?php $active_page = $_GET['page_offset'] / 40; ?>
                <?php for ($page = 0; $page < $this->data['pages']; $page++): ?>
                    <li>
                        <?php $active = $active_page == $page ? 'active' : ''; ?>
                        <a href="/index.php/component/calendar/?view=admin&layout=orders&page_offset=<?php echo $page_start; ?>" class="<?php echo $active; ?>">
                            <?php echo ($page + 1); ?>
                        </a>
                    </li>
                    <?php $page_start += 40; ?>
                <?php endfor; ?>
            </ul>
        </nav>

    </div>

</div>

<div class="popup" data-popup="delete-order-files">
    <div class="popup-inner">
        <p><a data-popup-close="delete-order-files" href="#" class="btn-cal btn-orange" style="float: right;">Zavrieť</a></p>
        <a class="popup-close" data-popup-close="delete-order-files" href="#">x</a>
    </div>
</div>

<script type="text/javascript" src="<?php echo CAL_ROOT_WEB . '/components/com_calendar/assets/js/calendar_admin_orders.js';?>"></script>
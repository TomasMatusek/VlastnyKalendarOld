<?php
defined('_JEXEC') or die;
$prices = unserialize(CAL_PRICES);
$cover_prices = unserialize(CAL_COVER_PRICES);
$user = JFactory::getUser();
$userId = $user->get( 'id' );
$prices = Price::getCalendarPrices();
include_once('calendar_info.php');
?>

<div id="calendar">

    <div class="row">
        <div class="col-md-12">
            <div class="content-box box-orange">
                <div class="content-header">
                    <h2><span>Cenník</span></h2>
                </div>
                <div class="content-body">
                    <?php echo CAL_PRICELIST_TEXT; ?>
                </div> <!-- ./content-body -->
            </div>
        </div>
    </div>

    <div class="row">
        <?php $index = 1; ?>
        <?php foreach($calendar_info as $calendar_type => $calendar_detail):?>
            <?php if ($calendar_detail['display'] === false) { continue; } ?>
            <?php $padding_top = $index < 5 ? '0px' : '15px'; ?>
            <div class="col-md-3" style="padding-top: <?php echo $padding_top; ?>;" data-calendar-type="<?php echo $calendar_type; ?>">
                <div class="sppb-addon sppb-addon-pricing-table sppb-text-center ">
                    <div style="" class="sppb-pricing-box ">
                        <div class="sppb-pricing-header">
                            <div class="preview_head" data-popup-open="calendar_preview_<?php echo $calendar_type; ?>">
                                <?php if ($prices[$calendar_type]['onSale'] && ($prices[$calendar_type]['percentSale'] != 0)): ?>
                                    <div class="calendar-discount">
                                        <?php if ($prices[$calendar_type]['percentSale'] > 0): ?>
                                            -<?php echo $prices[$calendar_type]['percentSale']; ?>% zľava
                                        <?php else: ?>
                                            +<?php echo abs($prices[$calendar_type]['percentSale']); ?>% prirážka
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="calendar-discount-empty"></div>
                                <?php endif; ?>
                                <img src="<?php echo CAL_COMPONENT_WEB . "assets/img/calendars/thumbs/".$calendar_type.".select.thumb.png"; ?>" class="cal-preview">
                            </div>
                            <div class="pricing_head">
                                <div class="sppb-pricing-price">
                                    <?php if ($prices[$calendar_type]['onSale'] && $prices[$calendar_type]['percentSale'] != 0): ?>
                                        <span style="text-decoration: line-through;"><?php echo $prices[$calendar_type]['originalPrice']; ?></span>&euro;
                                        <span><?php echo $prices[$calendar_type]['newPrice']; ?></span>&euro;
                                    <?php else: ?>
                                        <span><?php echo $prices[$calendar_type]['originalPrice']; ?></span>&euro;
                                    <?php endif; ?>
                                </div><div class="sppb-pricing-vat">s DPH</div>
                            </div>
                        </div>
                        <div class="calendar-info">
                            <ul>
                                <?php foreach ($calendar_detail['info'] as $key => $text): ?>
                                    <li><?php echo $text; ?></li>
                                <?php endforeach; ?>
                                <li>Príplatok za titulnú stranu: <b><?php echo $cover_prices[$calendar_type]; ?>&euro;</b></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php $index++; ?>
        <?php endforeach; ?>
    </div>

    <?php foreach($calendar_info as $calendar_type => $calendar_detail):?>
        <div class="popup" data-popup="calendar_preview_<?php echo $calendar_type; ?>">
            <div class="popup-inner">
                <img src="<?php echo CAL_COMPONENT_WEB . "assets/img/calendars/thumbs/".$calendar_type.".select.png"; ?>" class="cal-preview">
                <p><a data-popup-close="calendar_preview_<?php echo $calendar_type; ?>" href="#" class="btn-cal btn-orange" style="float: right;">Zavrieť</a></p>
                <a class="popup-close" data-popup-close="calendar_preview_<?php echo $calendar_type; ?>" href="#">x</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php
defined('_JEXEC') or die;
$prices = unserialize(CAL_PRICES);
$cover_prices = unserialize(CAL_COVER_PRICES);
$user = JFactory::getUser();
$userId = $user->get( 'id' );
$prices = Price::getCalendarPrices();
include_once('calendar_info.php');
?>

<?php // if(in_array($userId, array(152,153))): ?>
<?php if(true): ?>

<!-- TEXT -->
<!--<div class="row">-->
<!--    <div class="col-md-12">-->
<!--        <div class="content-box box-orange">-->
<!--            <div class="content-header">-->
<!--                <h2><b><span style="color: #e63131;">POZOR!</span></b></h2>-->
<!--            </div>-->
<!--            <div class="content-body" style="color: #e63131; text-align: center; font-size: 14px;">-->
<!--                <b>Bohužial, z dôvodu veľkého zaťaženia kuriérskych spoločností, nevieme garantovať dodanie do Vianoc pre objednávky prijaté po 12.12.2018.</b>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!-- TEXT -->
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

<?php if(isset($this->data['non_finished_calendars']) && $this->data['non_finished_calendars'] != false)
    {
        echo '<div class="content-box box-orange">';
            echo '<div class="content-header">';
                echo '<h2>Vaše rozpracované kalendáre</h2>';
            echo "</div>";
            echo '<div class="content-body no-padding">';
                echo '<table cellpadding="0" cellspacing="0" border="0" width="100%">';
                    for ($i = 0; $i < count($this->data['non_finished_calendars']); $i++) {
                        $calendar = $this->data['non_finished_calendars'][$i];
                        echo "<tr>";
                            echo '<td>Typ: '.ucfirst($calendar['type']).'</td>';
                            echo '<td>Rok: '.ucfirst($calendar['start_year']).'</td>';
                            echo '<td>Začiatočný mesiac: '.ucfirst($calendar['start_month']).'</td>';
                            echo '<td>Vytvorený: '.date('d.m.Y H:i', $calendar['create_time']).'</td>';
                            echo '<td><a href="index.php?option=com_calendar&task=calendar.update&cal_id='.$calendar['cal_id'].'" class="orange">Dokončiť</a></td>';
                            echo '<td><a href="index.php?option=com_calendar&task=calendar.delete&cal_id='.$calendar['cal_id'].'" class="orange">Zmazať</a></td>';
                        echo "</tr>";
                    }
                echo "</table>";
            echo "</div>";
        echo "</div>";
	}
?>

<div id="calendar">
    <form method="post" action="/index.php?option=com_calendar&task=calendar.create">

        <!-- NASTAVENIA KALENDARU -->
        <div class="content-box box-orange">
            <div class="content-header">
                <h2>Vytvorenie nového kalendára</h2>
            </div>
            <div class="content-body">

                <div class="row">
                    <div class="col-xs-3 text-center">
                        <label class="control-label">Začať mesiacom</label>
                    </div>
                    <div class="col-xs-3 text-center">
                        <label class="control-label">Začať rokom</label>
                    </div>
                    <div class="col-xs-3 text-center">
                        <label class="control-label">Jazyk kalendára</label>
                    </div>
                    <div class="col-xs-3 text-center">
                        <label class="control-label">Titulná strana</label>
                    </div>
                </div> <!-- ./row -->

                <div class="row mt-10">
                    <div class="col-xs-3">
                        <div class="control-group">
                            <div class="controls">
                                <select name="start_month" class="input-medium create" style="width: 100%;">
                                    <option value="january">Január</option>
                                    <option value="february">Február</option>
                                    <option value="march">Marec</option>
                                    <option value="april">Apríl</option>
                                    <option value="may">Máj</option>
                                    <option value="june">Jún</option>
                                    <option value="july">Júl</option>
                                    <option value="august">August</option>
                                    <option value="september">September</option>
                                    <option value="october">Október</option>
                                    <option value="november">November</option>
                                    <option value="december">December</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="control-group">
                            <div class="controls">
                                <select name="start_year" class="input-small create" style="width: 100%;">
                                    <option value="2024" selected="selected">2024</option>
                                    <option value="2025" >2025</option>

                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="control-group">
                            <div class="controls">
                                <select name="language" class="input-medium create" style="width: 100%;">
                                    <option value="sk">Slovenčina</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="control-group">
                            <div class="controls">
                                <select name="front_page" class="input-mini create" style="width: 100%;">
                                    <option value="1">Áno</option>
                                    <option value="0">Nie</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> <!-- ./row -->

            </div> <!-- ./content-body -->

            <div class="content-footer" style="text-align: center; color: #a94442; padding-top: 0px;">
                Vaše obrázky a rozpracované kalendáre budu uschované po dobu 5 dní od Vášho posledného prihlásenia. Po uplynutí tejto doby budu automaticky zmazané.
            </div>
        </div> <!-- ./content-box -->

        <!-- TYP KALENDARU -->
        <div class="row">
            <?php $index = 1; ?>

            <?php foreach($calendar_info as $calendar_type => $calendar_detail):?>
                <?php $padding_top = $index < 5 ? '0px' : '15px'; ?>
                <div class="col-md-3" style="padding-top: <?php echo $padding_top; ?>;" data-calendar="<?php echo $calendar_type;?>">
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
                                    <li>Príplatok za titulnú stranu: <?php echo $cover_prices[$calendar_type]; ?>&euro;</li>
                                </ul>
                            </div>
                            <?php if ($calendar_detail['disabled'] === true): ?>
                            <div class="sppb-pricing-footer">
                                <button type="submit" value="<?php echo $calendar_type; ?>" name="type" class="sppb-btn sppb-btn-default sppb-btn sppb-btn-block" disabled="disabled" style="cursor: not-allowed;">
                                    Momentálne vypredaný
                                </button>
                            </div>
                            <?php else: ?>
                                <div class="sppb-pricing-footer">
                                    <button type="submit" value="<?php echo $calendar_type; ?>" name="type" class="sppb-btn sppb-btn-default sppb-btn sppb-btn-block">
                                        Vybrať šablónu <?php echo $index; ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php $index++; ?>
            <?php endforeach; ?>
        </div>

    </form>

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

<?php else: ?>
    Na stránke sa pracuje.
<?php endif; ?>

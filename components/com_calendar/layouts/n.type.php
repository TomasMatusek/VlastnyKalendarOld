<?php
$urlAffix = $this->data['current_year'] . '_' . $this->data['current_month'];
$urlAffixMonth = $this->data['current_year'] . '_month_' . $this->data['current_month'];
?>

<style type="text/css">
    .cal-dynamic-holder .wrapper-inside.typN.<?php echo $this->data['current_month']; ?> {
        background-color: white;
        background-image: url("https://www.vlastnykalendar.sk/components/com_calendar/assets/img/calendars/preview/n/type_n_<?php echo $urlAffix ?>_0.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: 710px 504px;
        height: 504px;
        overflow: hidden;
        position: relative;
        width: 710px;
    }

    .cal-dynamic-holder .wrapper-inside.typN .moveAble {
        border-radius: 0px;
    }

    <?php if ($this->data['current_month'] === 'cover'): ?>
    .month.month-<?php echo $this->data['current_month'];?> {
        background-color: rgba(0, 0, 0, 0);
        background-image: url("https://www.vlastnykalendar.sk/components/com_calendar/assets/img/calendars/preview/n/type_n_<?php echo $urlAffixMonth; ?>_0.png");
        background-repeat: no-repeat;
        background-position-x: 100%;
        background-position-y: 87%;
    }
    <?php endif; ?>
</style>

<script src="/components/com_calendar/assets/js/image_preloade.js"></script>

<div class="cal-dynamic-holder">
    <input type="hidden" name="cal_type" value="n"/>
    <?php for ($i = 1; $i < $this->data['positions'] + 1; $i++): ?>
        <div class="sett" style="float: left; ">
            <input type="hidden" name="cal_index[]" value="<?php echo $i; ?>"/>
            <input type="hidden" name="<?php echo "cal_img{$i}"; ?>" id="<?php echo "cal_img{$i}"; ?>"
                   value="<?php echo $this->data['imgs'][$i]['img']; ?>"/>
            <input type="hidden" name="<?php echo "cal_top{$i}"; ?>" id="<?php echo "cal_top{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_left{$i}"; ?>" id="<?php echo "cal_left{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_width{$i}"; ?>" id="<?php echo "cal_width{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_height{$i}"; ?>" id="<?php echo "cal_height{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_rotate{$i}"; ?>" id="<?php echo "cal_rotate{$i}"; ?>" value=""/>
        </div>
    <?php endfor; ?>

    <input type="hidden" name="total_positions" id="total_positions" value="<?php echo $this->data['positions']; ?>"/>

    <div class="wrapper-inside typN <?php echo $this->data['current_month']; ?>">
        <div class="wrapper" style="overflow: visible; position: relative;">
            <?php if ($this->data['current_month'] === 'cover'): ?>
                <div class="month month-<?php echo $this->data['current_month'] . ' ' . $this->data['current_year']; ?>">
                    &nbsp;
                </div>
            <?php endif; ?>
            <?php for ($i = 1; $i < $this->data['positions'] + 1; $i++): ?>
                <div class="<?php echo 'mover' . $i; ?> moveAble" id="<?php echo 'move' . $i; ?>"></div>
            <?php endfor; ?>
        </div>
        <?php if ($this->data['current_month'] == 'cover') { ?>
            <div id="customText">Váš text...</div>
        <?php } ?>
    </div>
</div>
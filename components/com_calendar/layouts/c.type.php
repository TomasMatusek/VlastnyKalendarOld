<?php
$urlAffix = $this->data['current_year'] . '_' . $this->data['current_month'];
$urlAffixMonth = $this->data['current_year'] . '_month_' . $this->data['current_month'];
?>

<style type="text/css">
    .cal-dynamic-holder .wrapper-inside.typC.<?php echo $this->data['current_month']; ?> {
        background-color: white;
        background-image: url("https://www.vlastnykalendar.sk/components/com_calendar/assets/img/calendars/preview/c/type_c_<?php echo $urlAffix ?>_0.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: 566px 800px;
        height: 800px;
        overflow: hidden;
        position: relative;
        width: 566px;
    }

    .cal-dynamic-holder .wrapper-inside.typC .moveAble {
        border-radius: 0px;
    }

    .month.month-<?php echo $this->data['current_month'];?> {
        background-color: rgba(0, 0, 0, 0);
        background-image: url("https://www.vlastnykalendar.sk/components/com_calendar/assets/img/calendars/preview/c/type_c_<?php echo $urlAffixMonth ?>_0.png");
        background-repeat: no-repeat;
        background-position-x: 100%;
        background-position-y: 83%;
    }
</style>

<script src="/components/com_calendar/assets/js/image_preloade.js"></script>

<div class="cal-dynamic-holder">
    <input type="hidden" name="cal_type" value="c"/>
    <div class="sett" style="float: left; ">
        <input type="hidden" name="cal_index[]" value="1"/>
        <input type="hidden" name="cal_img1" id="cal_img1" value="<?php echo $this->data['imgs'][1]['img']; ?>"/>
        <input type="hidden" name="cal_top1" id="cal_top1" value=""/>
        <input type="hidden" name="cal_left1" id="cal_left1" value=""/>
        <input type="hidden" name="cal_width1" id="cal_width1" value=""/>
        <input type="hidden" name="cal_height1" id="cal_height1" value=""/>
        <input type="hidden" name="cal_rotate1" id="cal_rotate1" value=""/>
    </div>

    <input type="hidden" name="total_positions" id="total_positions" value="1"/>

    <div class="wrapper-inside typC <?php echo $this->data['current_month']; ?>">
        <div class="wrapper" style="overflow: visible; position: relative;">
            <div class="month month-<?php echo $this->data['current_month'] . ' ' . $this->data['current_year']; ?>">&nbsp;</div>
            <?php for ($i = 1; $i < $this->data['positions'] + 1; $i++): ?>
                <div class="<?php echo 'mover' . $i; ?> moveAble" id="<?php echo 'move' . $i; ?>"></div>
            <?php endfor; ?>
        </div>
        <?php if ($this->data['current_month'] == 'cover') { ?>
            <div id="customText">Váš text...</div>
        <?php } ?>
    </div>
</div>
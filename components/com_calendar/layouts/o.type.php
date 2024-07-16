<?php
$urlAffix = $this->data['current_year'] . '_' . $this->data['current_month'];
$bgImgHeight = 666;
$modCalPositions = unserialize(CAL_LAYOUT_MOD_POSITIONS_COUNT);

if (array_key_exists('o', $modCalPositions)) {
    $month = $this->data['current_month'];
    if (array_key_exists("bg", $modCalPositions["o"])) {
        if (array_key_exists($month, $modCalPositions["o"]['bg'])) {
            $bgImgHeight = $modCalPositions["o"]["bg"][$month]['bgSize'];
        }
    }
}

if ($this->user_id == 152) {
    // echo '<pre>';
    // var_dump($this->data['imgs']);
}

?>

<style type="text/css">
    .cal-dynamic-holder .wrapper-inside.typO.<?php echo $this->data['current_month']; ?> {
        background-color: white;
        background-image: url("https://www.vlastnykalendar.sk/components/com_calendar/assets/img/calendars/preview/o/type_o_<?php echo $urlAffix ?>_0.jpg");
        background-position: center center;
        background-repeat: no-repeat;
        height: <?php echo $bgImgHeight; ?>px;
        overflow: hidden;
        position: relative;
        width: 710px;
    }

    .cal-dynamic-holder .wrapper-inside.typN .moveAble {
        border-radius: 0px;
    }
</style>

<script src="/components/com_calendar/assets/js/image_preloade.js"></script>

<?php if(!in_array($this->data['current_month'], array('january','december','june')) && $this->data['current_month'] != 'cover'): ?>
    <?php --$this->data['positions']; ?>
<?php endif; ?>

<div class="cal-dynamic-holder">
    <input type="hidden" name="cal_type" value="o"/>
    <?php for ($i = 1; $i < $this->data['positions'] + 1; $i++): ?>
        <div class="sett" style="float: left; ">
            <input type="hidden" name="cal_index[]" value="<?php echo $i; ?>"/>
            <input type="hidden" name="<?php echo "cal_img{$i}"; ?>" id="<?php echo "cal_img{$i}"; ?>" value="<?php echo $this->data['imgs'][$i]['img']; ?>"/>
            <input type="hidden" name="<?php echo "cal_top{$i}"; ?>" id="<?php echo "cal_top{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_left{$i}"; ?>" id="<?php echo "cal_left{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_width{$i}"; ?>" id="<?php echo "cal_width{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_height{$i}"; ?>" id="<?php echo "cal_height{$i}"; ?>" value=""/>
            <input type="hidden" name="<?php echo "cal_rotate{$i}"; ?>" id="<?php echo "cal_rotate{$i}"; ?>" value=""/>
        </div>
    <?php endfor; ?>

    <input type="hidden" name="total_positions" id="total_positions" value="<?php echo $this->data['positions']; ?>"/>

    <div class="wrapper-inside typO <?php echo $this->data['current_month']; ?>">
        <div class="wrapper" style="overflow: visible; position: relative;">
            <?php for ($i = 1; $i < $this->data['positions'] + 1; $i++): ?>
                <div class="<?php echo 'mover' . $i; ?> moveAble" id="<?php echo 'move' . $i; ?>"></div>
            <?php endfor; ?>
        </div>
        <?php if ($this->data['current_month'] == 'cover') { ?>
            <div id="customText">Váš text...</div>
        <?php } ?>
    </div>
</div>
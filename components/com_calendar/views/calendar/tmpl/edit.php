<?php
defined('_JEXEC') or die;
$user = JFactory::getUser();
$userId = $user->get( 'id' );
echo $this->data['javascript'];
// var_dump($this->data);
?>

<a name="main"></a>

<div id="calendar-edit">

<!--    <div class="row">-->
<!--        <div class="col-md-12">-->
<!--            <div class="content-box box-orange">-->
<!--                <div class="content-body" style="color: #e63131; text-align: center; font-size: 15px;">-->
<!--                    <b>Bohužial, z dôvodu veľkého zaťaženia kuriérskych spoločností, nevieme garantovať dodanie do Vianoc pre objednávky prijaté po 12.12.2018.</b>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

    <div class="row">
        <div class="col-xs-12">
            <!-- Low image quality -->
            <?php if (!empty($this->data['dimensionError'])): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Nízka kvalita obrazka!</strong>
                    <?php foreach($this->data['dimensionError'] as $key => $error): ?>
                        <p>Na pozícií <?php echo $error['position']; ?> ste umiestnili obrázok s nízkym rozlíšením. Minimálne doporučené rozlíšenie pre túto pozíciu je <?php echo $error['optimalDimensions']; ?> bodov!
                    <?php endforeach; ?>
                    <p>Váš kalendár si môžete objednať, avšak táto skutočnosť sa môže prejaviť nižšou kvalitou daného obrázka.</p>
                </div>
            <?php endif; ?>

            <!-- All possiotion filled -->
            <?php if ($this->data['positions_filled'] >= $this->data['positions_total']): ?>
                <div class="alert alert-success alert-dismissible" role="alert" style="overflow: hidden;">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="row">
                        <div class="col-xs-6">
                            <strong>Váš kalendár je hotový!</strong>
                            <p>Vyplnili ste všetky pozície a Váš kalendár bol pridaný do košíka. Nákupný košík nájdete v pravom hornom rohu a jeho prostredníctnom môžete pristupovať k už vytvoreným kalendárom. Teraz možete pristúpiť k objednávke alebo si vytvoriť další kalendár.</p>
                        </div>
                        <div class="col-xs-6">
                            <a href="index.php?option=com_calendar" role="button" data-toggle="modal" href="#helpModal" class="btn-cal btn-green pull-right calendar-create-new">
                                Vytvoriť další kalendár
                            </a>
                            <a href="index.php?option=com_calendar&task=user.orderform" role="button" class="btn-cal btn-green pull-right calendar-order">
                                Pristúpiť k objednávke
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <form action="/index.php?option=com_calendar&view=calendar&task=calendar.save" method="post" class="form-horizontal">

        <!-- SETTINGS -->
        <div class="calendar-settings">
            <div class="content-box box-orange">
                <div class="content-header">
                    <h2>Aktuálny mesiac: <?php echo ucfirst(Status::month($this->data['current_month'])); ?></h2>
                    <?php $positions_filled = $this->data['positions_filled'] > $this->data['positions_total'] ? $this->data['positions_total'] : $this->data['positions_filled']; ?>
                    <span class="title-right">Počet vyplnených pozícií: <?php echo $positions_filled . ' / ' . $this->data['positions_total']; ?></span>
                </div>

                <div class="content-body">
                    <!-- Months dropdown -->
                    <div class="btn-group">
                        <a href="#" data-toggle="dropdown" class="btn-cal btn-orange dropdown-toggle months-dropdown">
                            <span>Prejsť na mesiac</span>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php for ($i=0; $i<count($this->data['months']); $i++): ?>
                                <li>
                                    <?php
                                    echo "<a href='/index.php?option=com_calendar&task=calendar.month&month=".$this->data['months'][$i]."&year=".$this->data['years'][$i]."'>";
                                    echo "<span class='pull-left'>".ucfirst(Status::month($this->data['months'][$i]))."</span>";
                                    echo "<span class='label pull-right'>" . $this->data['years'][$i] ."</span>";
                                    echo '<div class="clearfix"></div>';
                                    echo "</a>";
                                    ?>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>

                    <!-- I need help -->
                    <a role="button" data-popup-open="help-modal-edit" class="btn-cal btn-orange">
                        Potrebujem pomoc
                    </a>

                    <!-- Save month -->
                    <button id="calendar-save-month-btn" type="submit" name="action" value="save" class="btn-cal btn-orange pull-right">
                        Uložiť tento mesiac
                    </button>
                    <button id="calendar-save-month-progress-btn" type="submit" name="action" value="save" class="btn-cal btn-gray btn-orange pull-right" disabled="disabled" style="display: none; cursor: not-allowed">
                        Ukladám zmeny...
                    </button>
                </div>
            </div>

            <!-- Your calendar text -->
            <?php if($this->data['current_month'] == 'cover') : ?>
                <div class="content-box box-orange">
                    <div class="content-header">
                        <h2>Text na obálku kalendára</h2>
                    </div>
                    <div class="content-body">
                        <input type="text" id="customTextInput" name="front_page_text" class="input-xlarge" style="width: 275px;" maxlength="50" value="<?php echo $this->data['front_page_text']; ?>" placeholder="Váš text...">
                        <p class="remaining-letters">Počet zostávajúcich znakov: <strong><span id="charNum">50</span></strong></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- CALENDAR EDITOR-->
        <div class="calendar-editor">
            <div class="row">
                <div class="col-xs-9">
                    <!-- Calendar layout -->
                    <?php include_once($this->data['layout']); ?>
                </div>
                <div class="col-xs-3">
                    <div class="row">

                        <!-- Position picker -->
                        <div class="col-xs-12">
                            <div class="content-box box-orange">
                                <div class="content-header">
                                    <h3>Vyberte pozíciu obrázka</h3>
                                </div>
                                <div class="content-body">
                                    <select id="position" name="position" class="input-medium" style="border-radius: 0px;">
                                        <?php for($i=1; $i<$this->data['positions']+1; $i++): ?>
                                            <?php $position = ($_GET['position'] > $this->data['positions']) ? 1 : $_GET['position']; ?>
                                            <?php $selected = ($i == $position) ? "selected='selected'" : ''; ?>
                                            <option value="<?php echo $i; ?>" <?php echo $selected; ?>>Pozícia <?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Image picker -->
                        <div class="col-xs-12">
                            <div class="content-box box-orange">
                                <div class="content-header">
                                    <h3>Vyberte obrázok</h3>
                                </div>
                                <div class="content-body">
                                    <a href="/index.php?option=com_calendar&view=calendar&layout=upload" class="btn-cal btn-orange" style="width: 100%;">
                                        Pridať nový obrázok
                                    </a>

                                    <!-- Image picker -->
                                    <div id="scrollbar1">
                                        <div class="viewport">
                                            <div class="overview">
                                                <ul class="upl-images">
                                                    <?php for($i=0; $i<count($this->data['images']['img']); $i++): ?>
                                                        <?php $j = $i + 1; ?>
                                                        <li>
                                                            <?php if (in_array($this->data['images']['thumb'][$i], $this->data['thumbs_used'])): ?>
                                                                <div class="image-already-used">Obrázok použitý</div>
                                                            <?php endif; ?>
                                                            <img id="image1" src="<?php echo $this->data['images']['thumb'][$i]; ?>" class="upl-img">
                                                            <input type="submit" name="action" value="Vybrať obrázok" class="addImage" style="position: absolute; display: none;" data-img="<?php echo $this->data['images']['img'][$i];?>"/>
                                                        </li>
                                                    <?php endfor; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div> <!-- ./row -->
                </div> <!-- ./col-xs-12 -->
            </div> <!-- ./row -->

        </div> <!-- ./calendar-layout -->
    </form>
</div> <!-- ./calendar-edit -->

<div class="popup" data-popup="help-modal-edit">
    <div class="popup-inner">
        <h2>Potrebujem pomoc.</h2>
        <p>Ak potrebuje pomoc kontaktujte nás telefonicky alebo prostredníctvom emailu.</p>
        <p>Identifikátor: <?php echo $userId; ?>_<?php echo $this->data['id']; ?>_<?php echo $this->data['type']; ?>_<?php echo $this->data['current_month']; ?>_<?php echo $this->data['current_year']; ?></p>
        <p><a data-popup-close="help-modal-edit" href="#" class="btn-cal btn-orange" style="float: right;">Zavrieť</a></p>
        <a class="popup-close" data-popup-close="help-modal-edit" href="#">x</a>
    </div>
</div>
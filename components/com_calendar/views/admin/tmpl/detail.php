<?php

/**
 * @package			Photobook Component
 * @subpackage	NewOrder Form View HTML
 */

defined('_JEXEC') or die('Restricted access');
$data = $this->calendar;

if($this->data["basePrice"] <= $this->data['zlavaDnaDiscount'])
{
	$this->data['zlavaDnaDiscount'] = $this->data['basePrice'];
	$this->data['toPay'] = 0;
}
else {
	$this->data['toPay'] = $this->data['basePrice'] - $this->data['zlavaDnaDiscount'];
}

/*echo "<pre>";var_dump($this->data['final_price']);echo "</pre>";
echo "<pre>";var_dump($this->data['fees']);echo "</pre>";
echo "<pre>";var_dump($this->data['smallCouponDiscountValue']);echo "</pre>";*/

?>
	<?php $current_page = (isset($_GET['limit'])) ? (($_GET['limit'] / 15) * 15)  : 0; ?>
	<?php $status = (isset($_GET['status'])) ? '&status='.$_GET['status']  : ""; ?>

<?php // print_r($this->data); ?>
<style type="text/css">
.calendar .button {
	margin: 0px !important;
	display: table-cell !important;
	border-radius: 0px;
	background: #555;
}

</style>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery( document ).ready(function() {

	jQuery("#batch").click(function() {
    jQuery('#batchModal').modal('show');
	});

	jQuery("#remove").on( 'click', function() {
    jQuery('#sureModal').modal('show');
	});

	jQuery('#batchModal').on('shown', function () {
    jQuery("div.modal-backdrop").each(function(){
      if(jQuery(this).data('wired') !== 'true'){
        jQuery(this).on('click', function() {
          if(myCondition == true){
            jQuery('#batchModal').modal('hide');
          }
        }).data('wired', true);
      }
    })
  });


});
</script>

    <a class="pull-right btn btn-primary btn-mini" href="/index.php?option=com_calendar&view=admin&limit=<?php echo $current_page . $status; ?>">Návrat späť na zoznam</a>

<div class="page-header">
    <h1>Detail objednávky - <?php echo $data['cal_id']; ?> </h1>

</div>

<div class="well">
<div class="left">
    <dl class="dl-horizontal">
    		<dt>Číslo objednávky:</dt>
        <dd><?php echo 'KA2013'.$this->data['cal_id']; ?>&nbsp;</dd>
        <dt>Počet kusov:</dt>
        <dd><?php echo $this->data['quantity']; ?> ks&nbsp;</dd>
	   		<dt>Spôsob odberu:</dt>
        <dd><?php echo Status::shipping($this->data['transport_method']); ?>&nbsp;</dd>
        <dt>Spôsob platby:</dt>
        <dd><?php echo Status::payment($this->data['payment_method']); ?>&nbsp;</dd>
        <dt>Komentár:</dt>
        <dd><?php echo $this->data['comment']; ?>&nbsp;</dd>
        <dt>Cena:</dt>
        <dd><?php echo round(( ( ( $this->data['final_price'] - $this->data['fees'] ) * $this->data['smallCouponDiscountValue'] ) + $this->data['fees'] ),2); ?> € <br /><small>[Kupón: <?php echo $this->data['smallCouponName'];?>]</small></dd>
    </dl>
</div>

<div class="left ml-100">
    <dl class="dl-horizontal">
    		<dt>Typ:</dt>
        <dd><?php echo Status::type($data['type']); ?>&nbsp;</dd>
        <dt>Začiatočný mesiac:</dt>
        <dd><?php echo ucfirst($data['start_month']); ?></dd>
	   	<dt>Jazyk:</dt>
        <dd><?php echo strtoupper($data['language']); ?></dd>
				<dt>Status:</dt>
        <dd><?php echo Status::order($data['status']); ?></dd>
        <dt>Dátum vytvorenia:</dt>
        <dd><span class="label label-info"><?php echo date('d-m-Y H:i:s', $data['create_time']); ?></span></dd>
    </dl>
</div>
<div class="clear"></div>

<?php if($this->data["zlavaDnaCouponUsage"] == 'yes') { ?>
    <hr size="1" style="border-bottom: 1px solid #DDDDDD;margin: 15px 0;" />
	  <div class="alert alert-info">
   	 <h4>Kupón "Zľava dňa" bol použitý</h4>
    </div>
    <dl class="dl-horizontal" style="">
    	<dt>Počet kupónov:</dt>
    	<dd><?php echo $this->data["zlavaDnaCouponsCount"]; ?> ks</dd>
      <dt>Zľava z kupónov:</dt>
    	<dd><?php echo $this->data["zlavaDnaDiscount"]; ?> &euro;</dd>
      <dt>Zostáva k úhrade:</dt>
    	<dd><span class="label label-info"><?php echo $this->data["toPay"]; ?> &euro;</span></dd>
      <dt>Kupóny:</dt>
    	<dd><?php echo $this->data['zlavaDnaUsedCodes'];?></span></dd>
    </dl>
  <?php } ?>

</div>

<div class="pull-left" style="float:left; margin-right: 10px;">
    <dl class="dl-horizontal">
		    <dt>Fakturačné údaje</dt>
        <dd>&nbsp;</dd>
				<dt>Meno a priezvisko:</dt>
        <dd><?php echo $this->data['billing_name']; ?>&nbsp;</dd>
        <dt>Adresa:</dt>
        <dd><?php echo $this->data['billing_address']; ?> &nbsp;</dd>
        <dt>Mesto:</dt>
        <dd><?php echo $this->data['billing_city']; ?> &nbsp;</dd>
        <dt>PSČ:</dt>
        <dd><?php echo $this->data['billing_zip']; ?> &nbsp;</dd>
        <dt>Telefón:</dt>
        <dd><?php echo $this->data['billing_phone']; ?>&nbsp;</dd>
        <dt>E-mail:</dt>
        <dd><?php echo $this->data['billing_mail']; ?>&nbsp;</dd>
    </dl>
</div>

<div class="pull-left" style="float:left">
    <dl class="dl-horizontal">
    		<dt>Dodacie údaje</dt>
        <dd>&nbsp;</dd>
				<dt>Meno a priezvisko:</dt>
        <dd><?php echo $this->data['shipping_name']; ?>&nbsp;</dd>
        <dt>Adresa:</dt>
        <dd><?php echo $this->data['shipping_address']; ?> &nbsp;</dd>
        <dt>Mesto:</dt>
        <dd><?php echo $this->data['shipping_city']; ?> <?php echo $this->data['shipping_zip']; ?> &nbsp;</dd>
        <dt>Telefón:</dt>
        <dd><?php echo $this->data['shipping_phone']; ?>&nbsp;</dd>
    </dl>
</div>

<div class="clear">&nbsp;</div>

<?php if($data['type'] != 'r' && $data['type'] != 's' && $data['type'] != 't') { ?>
  <?php /*<a href="/index.php?option=com_calendar&task=admin.batchpdf&order=<?php echo $this->data['cal_id']; ?>&limit=<?php echo $current_page . $status; ?>" class="btn btn-danger" id="batch">Batch process</a>*/ ?>
  <?php if (!file_exists($this->data['zipFilePath'])) { ?>
  <a href="/index.php?option=com_calendar&task=admin.zip_user_files&order=<?php echo $this->data['cal_id']; ?>&limit=<?php echo $current_page . $status; ?>" class="btn btn-primary ml-15" style="margin-left:15px;">ZIP generated</a>
  <?php } else { ?>
  <a href="<?php echo $this->data['zipDownloadFilePath']; ?>" class="btn btn-success" style="margin-left:15px;">Stiahni ZIP</a>
  <a href="/index.php?option=com_calendar&task=admin.zip_user_remove_files&order=<?php echo $this->data['cal_id']; ?>&limit=<?php echo $current_page . $status; ?>" class="btn btn-danger">Zmazat ZIP</a>
  <?php } ?>
<?php } ?>

<?php /*
<div class="form-actions" align="left">
<?php for($i=0; $i<count($this->cal['months']); $i++) : ?>
	<?php if($this->cal['months'][$i] == 'cover') { ?>
	<a class="btn btn-mini mr-5" href="/index.php?option=com_calendar&view=pdfcreator&layout=pdf&calendar=<?php echo $this->data['cal_id']; ?>&tmpl=component&month=<?php echo $this->cal['months'][$i]; ?>&cover=true"><?php echo $this->cal['months'][$i]; ?></a>
  <?php } elseif($data['type'] != 'r' && $data['type'] != 's' && $data['type'] != 't') { ?>
	<a class="btn btn-mini mr-5" href="/index.php?option=com_calendar&view=pdfcreator&layout=pdf&calendar=<?php echo $this->data['cal_id']; ?>&tmpl=component&month=<?php echo $this->cal['months'][$i]; ?>"><?php echo $this->cal['months'][$i]; ?></a>
  <?php } ?>
<?php endfor; ?>
</div>
*/ ?>

<?php if (!file_exists($this->data['zipFilePath'])) { ?>
<style type="text/css">
#pdfData.table td {
text-align: center;
}
</style>
<script type="text/javascript">
jQuery( document ).ready(function() {

	jQuery('#ajaxBatch').on('click', function () {

		jQuery(this).button('loading');

		jQuery('div.genState').each(function(index, element) {
       jQuery(this).remove();
    });

		jQuery("#pdfData td span.state").each(function(index, element) {
			var spinner = "<img src='/components/com_calendar/assets/img/loading_spinner.gif' />";
       jQuery(this).html(spinner);
    });

		<?php if(in_array($data['type'], unserialize(CAL_WITH_COVER_ONLY))) { ?>
			var months = ['cover'];
		<?php
		} else {
			if($this->cal['months'][0] == 'cover') { ?>
			var months = ['cover', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
			<?php } else { ?>
			var months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
			<?php } ?>
		<?php } ?>
		jQuery.each(months , function(i, val) {
		  ajaxGeneratePDF(<?php echo $this->data['cal_id']; ?>, months[i]);
		});

		jQuery(document).ajaxStop(function() {
			jQuery("#ajaxBatch").button('reset');
		});

	});
});
function ajaxGeneratePDF(calendar_id, month)
{
	jQuery.ajax({
    type: "POST",
    url: "/index.php?option=com_calendar&task=admin.ajaxBatch&tmpl=component",
    data: { 'order': calendar_id, 'month': month },
    dataType: 'html',
		timeout: 110000,
		error: function(jqXHR, textStatus, errorThrown) {
			jQuery("#pdfData td#"+month).html('<strong>'+month+'</strong><br /><span class="label label-important">ERROR</span>');
    }
    }).done(function(result) {
  		jQuery("#pdfData td#"+month).html(result);
  	});
}
</script>
<br /><br />
<div class="well">
	<h4>AJAX [Single Batch]</h4>
	<button id="ajaxBatch" data-loading-text="Pracujem .. prosím čakajte. Ak sa zaseknem dám vedieť, vtedy refreshni web a skús to znova!" class="btn btn-info">Vytvorit PDF pre jednotlive strany</button>
  <hr />
  <table id="pdfData" class="table table-bordered" width="100%" border="1">
  <?php if(in_array($data['type'], unserialize(CAL_WITH_COVER_ONLY))) { ?>
  <tr>
	  <td colspan="6" id="cover">Cover</br><span class="state"></span></td>
  </tr>
	<?php } else { ?>
		<?php if($this->cal['months'][0] == 'cover') { ?>
    <tr>
      <td colspan="6" id="cover">Cover</br><span class="state"></span></td>
    </tr>
    <?php } ?>
    <tr>
      <td id="january">Januar</br><span class="state"></span></td>
      <td id="february">Februar</br><span class="state"></span></td>
      <td id="march">Marec</br><span class="state"></span></td>
      <td id="april">April</br><span class="state"></span></td>
      <td id="may">Maj</br><span class="state"></span></td>
      <td id="june">Jun</br><span class="state"></span></td>
    </tr>
    <tr>
      <td id="july">Jul</br><span class="state"></span></td>
      <td id="august">August</br><span class="state"></span></td>
      <td id="september">September</br><span class="state"></span></td>
      <td id="october">Oktober</br><span class="state"></span></td>
      <td id="november">November</br><span class="state"></span></td>
      <td id="december">December</br><span class="state"></span></td>
    </tr>
  <?php } ?>
  </table>
</div>
<?php } else { ?>
<br /><br />
<div class="alert alert-danger">
	<h4>Generovanie nie je mozne</h4>
  <p>PDF subory su zazipovane na disku servera, pre opatovne generovanie prosim zmaz ZIP tlacidlom vyssie.</p>
</div>
<?php } ?>

<div class="form-actions admin-actions">
  <div>

    <a href="/index.php?option=com_calendar&task=admin.change_status&status=0&order=<?php echo $this->data['cal_id']; ?>" class="btn btn-info btn-mini">Nová (0)</a>

    <a href="/index.php?option=com_calendar&task=admin.change_status&status=1&order=<?php echo $this->data['cal_id']; ?>" class="btn btn-info btn-mini">Spracováva sa (1)</a>

    <a href="/index.php?option=com_calendar&task=admin.change_status&status=2&order=<?php echo $this->data['cal_id']; ?>" class="btn btn-info btn-mini" style="margin-right: 20px;">Vybavené (2)</a>

	<?php $invoice_file = "calendar/".$this->user_id."/invoice/invoice".$this->data['cal_id'].".pdf"; //echo $invoice_file;?>

	<?php if (file_exists($invoice_file)): ?>

		<a href="<?php echo "calendar/".$this->user_id."/invoice/invoice".$this->data['cal_id'].".pdf" ; ?>" class="btn btn-success btn-mini">Stiahnúť faktúru (PDF)</a>

	<?php else: ?>

<!--		<a href="<?php echo "/index.php?option=com_calendar&order=".$this->data['cal_id']."&user=".$this->user_id."&type=invoice&task=admin.pdf" ; ?>" class="btn btn-success btn-mini">Vygenerovať faktúru</a> -->

	<?php endif; ?>

    <a href="<?php echo "calendar/".$this->data['user_id']."/invoice/order".$this->data['cal_id'].".pdf" ; ?>" class="btn btn-success btn-mini">Stiahnúť objednávku (PDF)</a>

    <a class="pull-right btn btn-primary btn-mini" href="/index.php?option=com_calendar&view=admin&limit=<?php echo $current_page . $status; ?>">Návrat späť na zoznam</a>

  <?php // CUSTOM ORDER NUMBER FOR INVOICE // ?>
  <br /><br />
  <form action="<?php echo "/index.php?option=com_calendar&order=".$this->data['cal_id']."&user=".$this->user_id."&type=invoice&task=admin.custom_invoice";?>" method="post" class="form-horizontal">
		<input type="text" name="invoiceNumber" id="invoiceNumber" placeholder="Sem zadaj svoje vytuzene cislo" value=""/>

    <?php
    	// <input type="text" name="invoiceDate" id="invoiceDate" placeholder="Datum" value=""/>
     echo JHTML::calendar(date('d-m-Y', time()),'invoiceDate','invoiceDate','%d-%m-%Y', ' class="input-small"');
		?>

	  <button type="submit" class="btn btn-success">Vygenerovať faktúru</button>
  </form>

  <hr />

  <button id="remove" class="btn btn-danger btn-mini">Zmazať objednávku</button>

  </div>
  <div class="clear">&nbsp;</div>
</div>

	<div id="sureModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="sureModalLabel" aria-hidden="true">
   	<div class="modal-header">
	    <h3 id="sureModalLabel">Si si istý?</h3>
    </div>
	  <div class="modal-body">
  		<p>Si si istý, že chceš zmazať túto objednávku?</p>
    </div>
    <div class="modal-footer">
	    <a href="/index.php?option=com_calendar&task=admin.remove_order&order=<?php echo $this->data['cal_id']; ?>" class="btn btn-danger">Zmazať objednávku</a>
      <button aria-hidden="true" data-dismiss="modal" class="btn btn-success">Zatvoriť / Návrat</button>
    </div>
  </div>

		<div id="batchModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="batchModalLabel" aria-hidden="true">
    	<div class="modal-header">
		    <h3 id="batchModalLabel">Generovanie PDF</h3>
	    </div>
  	  <div class="modal-body">
    		<p style="text-align:center;"><img src="https://www.vlastnykalendar.sk/preload.gif" /><br />
        GENERUJEM ...</p>
      </div>
      <div class="modal-footer">
        <button aria-hidden="true" data-dismiss="modal" class="btn btn-danger">Zatvoriť</button>
      </div>
    </div>

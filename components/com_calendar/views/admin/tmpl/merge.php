<?php

/**
 * @package			Photobook Component
 * @subpackage	NewOrder Form View HTML
 */

defined('_JEXEC') or die('Restricted access');
?>

<div class="page-header">
    <h1>Spojenie objednávok</h1>
</div>
<a href="/index.php?option=com_calendar&view=admin" class="btn btn-primary">Späť do administrácie</a><br /><br />
<div class="well">
	<form class="form-horizontal" action="<?php echo JRoute::_('/index.php?option=com_calendar&task=admin.merge_orders'); ?>" method="post">
  	<legend>Generovanie faktúry</legend>
     <div class="control-group">
				<label class="control-label" style="width:190px;">ID objednávok <span class="label label-info">oddeľovač ,</span></label>
				<div class="controls" style="margin-left:200px;">
					<input type="text" placeholder="Čiarkami oddeľ ID objednávok" name="orders" id="orders" class="input-xlarge">
				</div>
			</div>
      <div class="control-group input-prepend">
				<label class="control-label" style="width:190px;">Faktura cislo:</label>
				<div class="controls" style="margin-left:200px;">
	        <span class="add-on"><?php echo "KA".date("Y"); ?></span>
					<input type="text" placeholder="Číslo FA" name="invoice_num" id="invoice_num" class="input-mini" value="">
				</div>
			</div>
     <div class="control-group">
	         <label class="control-label" for="transport_method" style="width:190px;">Spôsob doručenia</label>
           <div class="controls" style="margin-left:200px;">
             <select name="transport_method" id="shipping">
               <option value="personal" selected="selected">Osobne (0 €)</option>
               <option value="post">Slovenskou poštou (<?php echo CAL_SHIPPING_POST; ?> €)</option>
               <option value="courier">Kuriérom (<?php echo CAL_SHIPPING_COURIER; ?> €)</option>
             </select>
          </div>
      </div>
      <div class="control-group">
        	<label class="control-label" for="payment_method" style="width:190px;">Spôsob platby</label>
          <div class="controls" style="margin-left:200px;">
          <select name="payment_method" id="payment_method">
					<option class="cash" value="cash" selected="selected">Platba v hotovosti (0 €)</option>
            <option class="tranfer" value="transfer">Platba prevodom na účet (<?php echo CAL_PAYMENT_PERSONAL; ?> €)</option>
            <span class="wrap-cod"><option class="cod" value="cod">Dobierka (<?php echo CAL_PAYMENT_POST; ?> €)</option></span>
      	    <span class="wrap-cod-courier"><option class="cod" value="cod-courier">Kuriérovi (0 €)</option></span>
          </select>
          </div>
      </div>
      <div class="clearfix clear"></div>
      <div class="control-group input-append">
				<label class="control-label" style="width:190px;">Koeficient navysenia ceny</label>
				<div class="controls" style="margin-left:200px;">
					<input type="text" placeholder="Čiarkami oddeľ ID objednávok" name="coeficient" id="coeficient" class="input-mini" value="<?php echo CAL_PAYMENT_PRICE_PER_ITEM; ?>">
          <span class="add-on">€</span>
				</div>
			</div>
     	<div class="alert alert-danger">
      <strong>DESATINA CIARKA JE BODKA [ . ]!</strong>
      </div>
      
      <style type="text/css">
			.calendar .button {
				margin: 0px !important;
				display: table-cell !important;
				border-radius: 0px;
				background: #555;
			}
			</style>
			
			<div class="control-group input-append">
				<label style="width:190px;" class="control-label">Datum vystavenia</label>
				<div style="margin-left:200px;" class="controls">
					<?php echo JHTML::calendar(date('d-m-Y', time()),'date','date','%d-%m-%Y', ' class="input-small"'); ?>
				</div>
			</div>
      
      <div class="form-actions">
	      <button type="submit" class="btn btn-primary">Vygeneruj faktúru</button>
      </div>
  </form>
</div>

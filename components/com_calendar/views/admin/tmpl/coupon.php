<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="page-header">
    <h1>Správa zľavovych kupónov</h1>
</div>
<a href="/index.php?option=com_calendar&view=admin" class="btn btn-primary">Späť na zoznam</a><br /><br />
<div class="well">
	<form class="form-horizontal" action="<?php echo JRoute::_('/index.php?option=com_calendar&task=admin.coupon_disable'); ?>" method="post">
  	<legend>Zrusiť platnosť kupónu</legend>
     <div class="control-group">
				<label class="control-label">Kód kupónu:</label>
				<div class="controls">
					<input type="text" placeholder="Kód kupónu" name="code" id="code">
				</div>
			</div>
      <div class="form-actions">
	      <button type="submit" class="btn btn-primary">Zrušiť platnosť</button>
      </div>
  </form>
</div>

<?php if(false) { ?>
<div class="well">
	<form class="form-horizontal" action="/index.php?option=com_calendar&task=admin.coupon_add" method="get">
  	<legend>Pridať nový kupón</legend>
	    
      <div class="control-group">
				<label class="control-label">Kód kupónu:</label>
				<div class="controls">
					<input type="text" placeholder="Kód kupónu">
				</div>
			</div>
      
      <div class="control-group">
				<label class="control-label">Zľava v €:</label>
				<div class="controls">
					<input type="text" placeholder="Kód kupónu">
				</div>
			</div>
      
      <div class="form-actions">
	      <button disabled type="submit" class="btn btn-primary">Pridať kupón</button>
      </div>
  </form>
</div>
<?php } ?>
<?php $payment = $this->data['order']['order']['payment_method']; ?>
<?php $id = $this->data['order']['order']['order_id']; ?>
<?php $paymentLink = $this->data['payment_link']; ?>

<style>
    .system-message-container {
        display: none !important;
    }
</style>

<?php if($payment == 'card'): ?>
    <!-- Order is already paid -->
    <?php if($this->data['is_paid']): ?>
        <div class="alert alert-success" role="alert" style="overflow: hidden;">
            <div class="row">
                <div class="col-xs-12" style="text-align: center">
                    <p style="font-size: 20px;"><strong>Vaša objednávka je zaplatená.</strong></p>
                </div>
            </div>
        </div>
    <?php else: ?>

        <!-- Order is not paid -->
        <?php if( ! $this->data['payment_error']): ?>
            <div class="alert alert-success" role="alert" style="overflow: hidden;">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <p style="font-size: 20px;"><strong>Zvolili ste si platbu kartou.</strong></p>
                        <a href="<?php echo $paymentLink; ?>"
                            role="button"
                            class="btn-cal btn-green calendar-gp-webpay"
                            style="margin-bottom: 15px; font-size: 20px;">
                            ZAPLATIŤ KARTOU <?php echo $this->data['price']; ?> &euro;
                        </a>
                    </div>
                </div>
            </div>
        <!-- There was an payment error -->
        <?php else: ?>
            <div class="alert alert-danger" role="alert" style="overflow: hidden;">
                <div class="row">
                    <div class="col-xs-12" style="text-align: center">
                        <p style="font-size: 20px;"><strong>Počas platby sa vyskykla chyba!</strong></p>
                        <p style="margin: 0px;">
                            <?php echo $this->data['payment_error_text']; ?>
                            (<?php echo 'PRCODE: ' . $this->data['payment_error_prcode']; ?>, <?php echo 'SRCODE: ' . $this->data['payment_error_srcode']; ?>)
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<div class="alert alert-success" role="alert" style="overflow: hidden;">

    <div class="row">
        <div class="col-xs-8">
            <p style="font-size: 20px;"><strong>Objednávka úspešne odoslaná.</strong></p>
            <p>Na Váš email Vám v prílohe zasielame detail objednávky (skontrolujte si Váš SPAM priečinok - nevyžiadanú poštu).</p>
            <p>O stave Vašej objednávky a jej dokončení, budete informovaný emailom.</p>
            <p style="margin: 0px;">Faktúru obdržíte po spracovaní objednávky.</p>
        </div>
        <div class="col-xs-4">
            <a href="/index.php?option=com_calendar&task=user.downloadOrderPdf&order_id=<?php echo $id; ?>"
               role="button"
               class="btn-cal btn-green pull-right calendar-order"
               style="margin-top: 50px;">
                Stiahnuť objednávku
            </a>
            <a href="/index.php?option=com_calendar&view=user&layout=list"
               role="button"
               class="btn-cal btn-green pull-right calendar-order"
               style="margin-top: 50px;">
                Zobraziť Vaše objednávky
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <hr style="margin: 15px 0px;" />
            <?php if($payment == 'post-sk'): ?>
                <p style="margin: 0px;"><?php echo CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_POST; ?></p>
            <?php endif; ?>

            <?php if($payment == 'transfer-sk' || $payment == 'transfer-cz' || $payment == 'transfer-eu'): ?>
                <p style="margin: 0px;"><?php echo CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_TRANSFER; ?></p>
            <?php endif; ?>

            <?php if($payment == 'courier'): ?>
                <p style="margin: 0px;"><?php echo CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_COURIER; ?></p>
            <?php endif; ?>

            <?php if($payment == 'cash'): ?>
                <p style="margin: 0px;"><?php echo CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_CASH; ?></p>
            <?php endif; ?>

            <?php if($payment == 'depo'): ?>
                <p style="margin: 0px;"><?php echo CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_DEPO; ?></p>
            <?php endif; ?>

            <?php if($payment == 'card'): ?>
                <p style="margin: 0px;"><?php echo CAL_ORDER_SUCCESS_TRANSPORT_METHOD_DESCRIPTION_GP_WEBPAY; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <hr style="margin: 15px 0px;" />
            <p><?php echo CAL_ORDER_SUCCESS_THANKS; ?></p>
        </div>
    </div>

</div>
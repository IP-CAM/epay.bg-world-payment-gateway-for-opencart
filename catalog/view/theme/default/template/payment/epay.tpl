<form action="<?php echo str_replace('&', '&amp;', $action); ?>" method="post" id="payment">
	<input type=hidden name=PAGE value="credit_paydirect">
	<input type=hidden name=ENCODED value="<?php echo $ENCODED; ?>">
	<input type=hidden name=CHECKSUM value="<?php echo $CHECKSUM; ?>">
	<input type=hidden name=URL_OK value="<?php echo $ep_returnurl; ?>">
	<input type=hidden name=URL_CANCEL value="<?php echo $ep_cancelurl; ?>"><br>
  <div class="buttons">
    <div class="right"><a id="button-confirm" onclick="$('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></div>
  </div>
  <script type="text/javascript"><!--
	$('#button-confirm').bind('click', function() {
		$.ajax({
			type: 'GET',
			url: 'index.php?route=payment/epay/confirm'
		});
	});
//--></script>
</form>
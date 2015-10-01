<?php foreach($this->messages as $message) : ?>

<div class="<?php echo $message['type'] ?> settings-error notice is-dismissible" id="setting-error-settings_updated">
	<p><?php echo $message['message'] ?></p>
	<button class="notice-dismiss" type="button">
		<span class="screen-reader-text"><?php _e('Hide this message', 'atomicon-gallery') ?></span>
	</button>
</div>

<?php endforeach; ?>
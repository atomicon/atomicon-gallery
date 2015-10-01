<?php include (dirname(__FILE__).'/admin-header.php'); ?>

<form id="upload" method="post" enctype="multipart/form-data" action="<?php echo $this->admin_url( array('action' => 'atomicon-gallery-handle-upload') ) ?>">

	<div id="drop">
		<p>
			<?php _e('Drop Here', 'atomicon-gallery') ?>
		</p>
		<p>
			<?php _e('or', 'atomicon-gallery') ?>
		</p>
		<a class="button-primary"><?php _e('Upload', 'atomicon-gallery') ?></a>
		<input type="file" name="upl" multiple />
	</div>

	<ul>
		<!-- The file uploads will be shown here -->
	</ul>

</form>

<p class="submit">
	<a href="<?php echo $this->admin_url()?>" class="button button-secondary"><?php _e('Cancel', 'atomicon-gallery') ?></a>
	<a href="<?php echo $this->admin_url()?>" class="button button-primary"><?php _e('Done', 'atomicon-gallery') ?></a>
</p>

<?php include (dirname(__FILE__).'/admin-footer.php'); ?>
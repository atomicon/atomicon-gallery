<div class="wrap atomicon-gallery">

	<?php screen_icon(); ?>
	<h2><?php _e( 'Atomicon Gallery', 'atomicon-gallery' ); ?> - <?php _e('Create folder', 'atomicon-gallery') ?></h2>

	<form method="POST">

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="folder-name"><?php _e('Folder name', 'atomicon-gallery') ?></label></th>
					<td><input type="text" class="regular-text" id="folder-name" name="folder-name" value="<?php esc_attr_e($folder_name) ?>"></td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<a href="<?php echo $this->admin_url() ?>" class="button button-secondary" ><?php echo _e('Cancel', 'atomicon-gallery') ?></a>
			<button type="submit" name="action" value="create-folder" class="button button-primary" id="submit"><?php _e('Create folder', 'atomicon-gallery') ?></button>
		</p>

	</form>

</div>
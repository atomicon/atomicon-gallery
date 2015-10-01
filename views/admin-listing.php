<?php include (dirname(__FILE__).'/admin-header.php'); ?>

	<form method="POST">

	<?php include ('admin-actions.php') ?>

	<table class="wp-list-table widefat fixed striped atomicon-gallery">
		<thead>
			<tr>
				<td class="manage-column column-cb check-column" id="cb">
					<label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All', 'atomicon-gallery') ?></label>
					<input type="checkbox" id="cb-select-all-1">
				</td>
				<th class="manage-column column-title" id="title" scope="col"><?php _e('Title', 'atomicon-gallery') ?></th>
				<th class="manage-column column-type" id="type" scope="col"><?php _e('Type', 'atomicon-gallery') ?></th>
				<th class="manage-column column-size" id="type" scope="col"><?php _e('Size', 'atomicon-gallery') ?></th>
				<th class="manage-column column-size" id="type" scope="col"><?php _e('Dimensions', 'atomicon-gallery') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( $this->folder != '') : ?>
			<tr>
				<th class="check-column" scope="row">
					&nbsp;
				</th>
				<td data-colname="<?php _e('Title', 'atomicon-gallery') ?>" class="title column-title">
					<?php $new_folder = trim(dirname($this->folder), './ ' ); ?>
					<a href="<?php echo $this->admin_url( array('folder' => $new_folder ) ) ?>" title="<?php esc_attr_e('Up', 'atomicon-gallery') ?>">[ <?php esc_html_e('Up', 'atomicon-gallery') ?> ]</a>
				</td>
				<td><?php _e('Folder', 'atomicon-gallery') ?></td>
				<td><?php _e('-', 'atomicon-gallery') ?></td>
				<td><?php _e('-', 'atomicon-gallery') ?></td>
			</tr>
			<?php endif; ?>

			<?php $count = 0; ?>

			<?php foreach($this->core->folders($this->folder) as $item) : ?>
			<tr>
				<th class="check-column" scope="row">
					<label for="cb-select-<?php echo esc_attr_e($item['id']) ?>" class="screen-reader-text"><?php echo sprintf( __('Select %s', 'atomicon-gallery'), esc_html_e($item['title']) ) ?></label>
					<input type="checkbox" value="<?php echo esc_attr_e($item['title']) ?>" name="item[]" id="cb-select-<?php echo esc_attr_e($item['id']) ?>">
				</th>
				<td data-colname="<?php _e('Title', 'atomicon-gallery') ?>" class="title column-title">
					<?php $new_folder = trim($this->folder.'/'.$item['title'], '/ '); ?>
					<a href="<?php echo $this->admin_url( array('folder' => $new_folder) ) ?>" title="<?php esc_attr_e($item['title']) ?>">[ <?php esc_html_e($item['title']) ?> ]</a>
				</td>
				<td><?php _e('Folder', 'atomicon-gallery') ?></td>
				<td><?php _e('-', 'atomicon-gallery') ?></td>
				<td><?php _e('-', 'atomicon-gallery') ?></td>
			</tr>

			<?php $count ++; ?>

			<?php endforeach; ?>

			<?php foreach($this->core->images($this->folder) as $item) : ?>
			<tr>
				<th class="check-column" scope="row">
					<label for="cb-select-<?php echo esc_attr_e($item['id']) ?>" class="screen-reader-text"><?php echo sprintf( __('Select %s', 'atomicon-gallery'), esc_html_e($item['title']) ) ?></label>
					<input type="checkbox" value="<?php echo esc_attr_e($item['title']) ?>" name="item[]" id="cb-select-<?php echo esc_attr_e($item['id']) ?>">
				</th>
				<td data-colname="<?php _e('Title', 'atomicon-gallery') ?>" class="title column-title">
					<a href="<?php echo $item['url'] ?>" rel="atomicon-gallery" class="thickbox" title="<?php esc_attr_e($item['title']) ?>"><?php esc_html_e($item['title']) ?></a>
				</td>
				<td><?php _e('Image', 'atomicon-gallery') ?></td>
				<td><?php printf( __('%.2f kB', 'atomicon-gallery'), $item['size']/1024 ); ?></td>
				<td><?php esc_html_e( $item['width'] . ' x ' . $item['height']) ?></td>
			</tr>

			<?php $count ++; ?>

			<?php endforeach; ?>
		</tbody>
	</table>

	<?php echo $count == 0 ? __('No items found', 'atomicon-gallery') : ''; ?>


<?php include (dirname(__FILE__).'/admin-footer.php'); ?>

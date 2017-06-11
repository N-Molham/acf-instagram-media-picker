<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 20-Feb-17
 * Time: 6:10 PM
 */
?>
<div class="ginput_container" id="gf_container_<?php echo $form_id; ?>_<?php echo $field_id; ?>">
	<?php if ( false === $is_entry_detail ): ?>
		<div id="acf-imp-igmp-<?php echo $field_id; ?>" class="modal acf-imp-browse-modal fade" tabindex="-1" role="dialog" data-target-input="input_<?php echo $field_id; ?>">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<a href="javascript:void(0)" class="close pull-right" data-dismiss="modal" aria-label="<?php _e( 'Close', ACF_IMP_DOMAIN ); ?>">&times;</a>
						<h4 class="modal-title"><?php echo $browse_button_label; ?></h4>
					</div>
					<div class="modal-body">
						<ul class="acf-imp-media-items"></ul>
						<div class="acf-imp-loading"></div>
						<button type="button" class="button center-block acf-imp-load-more" data-max-id=""><?php _e( 'Load More', ACF_IMP_DOMAIN ); ?></button>
					</div>
					<div class="modal-footer">
						<button type="button" class="button" data-dismiss="modal"><?php _e( 'Done', ACF_IMP_DOMAIN ); ?></button>
					</div>
				</div>
			</div>
		</div><!-- .acf-imp-modal -->

		<button type="button" class="button acf-imp-button acf-imp-button-browse" data-toggle="modal" <?php echo $disabled_text; ?> <?php echo $logic_event; ?>
		        data-target="#acf-imp-igmp-<?php echo $field_id; ?>" data-items-num="<?php echo esc_attr( $items_number ); ?>" data-media-type="<?php echo esc_attr( $media_type ); ?>">
			<?php echo $browse_button_label; ?>
		</button>
	<?php endif; ?>

	<input type="<?php echo $is_entry_detail ? 'text' : 'hidden'; ?>" id="input_<?php echo $field_id; ?>" name="input_<?php echo $field_id; ?>" value="<?php echo esc_attr( $value ); ?>" />
</div>
<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 20-Feb-17
 * Time: 6:10 PM
 *
 * @param array $field_settings
 */
$media_count = count( array_filter( explode( ',', $field_settings['value']['images'] ) ) );
?>
<div id="<?php echo $field_settings['id']; ?>-modal" class="modal acf-imp-browse-modal fade" tabindex="-1" role="dialog"
     data-value-input="<?php echo $field_settings['id']; ?>" data-username-input="<?php echo $field_settings['id']; ?>-username"
     data-media-limit="<?php echo esc_attr( $field_settings['media_limit'] ); ?>">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<a href="javascript:void(0)" class="close pull-right" data-dismiss="modal" aria-label="<?php _e( 'Close', ACF_IMP_DOMAIN ); ?>">&times;</a>
				<h4 class="modal-title"><?php echo $field_settings['browse_button_label']; ?></h4>
			</div>
			<div class="modal-body">
				<input type="text" class="large-text code acf-imp-username" placeholder="<?php esc_attr_e( 'Instagram Username', ACF_IMP_DOMAIN ); ?>"
				       data-target-input="<?php echo $field_settings['id']; ?>-username">
				<ul class="acf-imp-media-items"></ul>
				<div class="acf-imp-loading"></div>
				<button type="button" class="button center-block acf-imp-load-more" data-max-id=""><?php _e( 'Load Items', ACF_IMP_DOMAIN ); ?></button>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-primary" data-dismiss="modal"><?php _e( 'Done', ACF_IMP_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div><!-- .acf-imp-modal -->

<button type="button" class="button acf-imp-button acf-imp-button-browse" data-toggle="modal" data-target="#<?php echo $field_settings['id']; ?>-modal"
        data-browse-label="<?php echo esc_attr( $field_settings['browse_button_label'] ); ?>" data-media-type="<?php echo esc_attr( $field_settings['media_type'] ); ?>">
	<?php echo $field_settings['browse_button_label'], ( $media_count ? ' (' . $media_count . ')' : '' ); ?>
</button>

<input type="hidden" id="<?php echo $field_settings['id']; ?>" name="<?php echo esc_attr( $field_settings['name'] ); ?>[images]" value="<?php echo esc_attr( $field_settings['value']['images'] ); ?>" />
<input type="hidden" id="<?php echo $field_settings['id']; ?>-username" name="<?php echo esc_attr( $field_settings['name'] ); ?>[username]" value="<?php echo esc_attr( $field_settings['value']['username'] ); ?>" />
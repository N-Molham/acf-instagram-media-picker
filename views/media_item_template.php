<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 20-Feb-17
 * Time: 8:51 PM
 */
?>
<script id="acf-imp-media-item-template" type="text/template">
	<li id="acf-imp-media-{code}" class="acf-imp-media-item acf-imp-media-type-{type}" data-media-id="{code}">
		<label class="acf-imp-media-item-checkbox pull-left">
			<img src="{thumbnail}" alt="{caption}" class="acf-imp-media-item-image" />
			<span class="acf-imp-media-item-bar clearfix">
				<input type="checkbox" value="{code}" {checked}> <?php _e( 'Select', ACF_IMP_DOMAIN ); ?>
				<span class="acf-imp-media-item-counts pull-right">
					<span class="dashicons dashicons-thumbs-up" title="<?php esc_attr_e('Likes', ACF_IMP_DOMAIN) ?>"></span> {likes}

					<span class="dashicons dashicons-admin-comments" title="<?php esc_attr_e('Comments', ACF_IMP_DOMAIN) ?>"></span> {comments}
				</span>
			</span>
		</label>
	</li>
</script>

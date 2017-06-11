<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 20-Feb-17
 * Time: 8:51 PM
 */
?>
<script id="acf-imp-media-item-template" type="text/template">
	<li id="acf-imp-media-{id}" class="acf-imp-media-item acf-imp-media-type-{type}" data-media-id="{id}">
		<label class="acf-imp-media-item-checkbox pull-left">
			<img src="{thumbnail}" title="{caption}" alt="{caption}" class="acf-imp-media-item-image" />
			<span class="acf-imp-media-item-bar clearfix">
				<input type="checkbox" value="{id}"> <?php _e( 'Select', ACF_IMP_DOMAIN ); ?>
				<span class="acf-imp-media-item-counts pull-right">
					<i class="fa fa-thumbs-up" aria-hidden="true"></i> {likes}

					<i class="fa fa-comment" aria-hidden="true"></i> {comments}
				</span>
			</span>
		</label>
	</li>
</script>

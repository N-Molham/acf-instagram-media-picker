<?php namespace ACF\Add_Ons\Instagram_Media_Picker;

use WP_Error;

/**
 * Instagram logic
 *
 * @package ACF\Add_Ons\Instagram_Media_Picker
 */
class Instagram extends Component
{
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		parent::init();
	}

	/**
	 * Fetch user's instagram account latest/recent media items
	 *
	 * @param string $username
	 * @param string $field_id
	 *
	 * @return array|WP_Error
	 */
	public function fetch_instagram_recent_media_items( $username, $field_id = '' )
	{
		// cache args
		$media_cache_key  = 'acf_instagram_media_' . $username;
		$media_cache_time = HOUR_IN_SECONDS;

		if ( '' !== $field_id )
		{
			$acf_field = get_field_object( $field_id );
			if ( is_array( $acf_field ) && isset( $acf_field['data_cache_hours'] ) )
			{
				// use field's cache settings
				$media_cache_time = absint( $acf_field['data_cache_hours'] ) * HOUR_IN_SECONDS;
			}
		}

		// load from cache first
		$media_items = get_transient( $media_cache_key );
		if ( false === $media_items )
		{
			// re-fetch the data
			$data_request = wp_safe_remote_get( 'https://www.instagram.com/' . $username . '/?__a=1' );
			if ( 200 !== wp_remote_retrieve_response_code( $data_request ) )
			{
				// error loading profile data
				return new WP_Error( 'acf_imp_fetch_status', __( 'Data fetch request was rejected', ACF_IMP_DOMAIN ) );
			}

			// parse response body
			$response = json_decode( wp_remote_retrieve_body( $data_request ) );
			if ( !is_object( $response ) || !isset( $response->user ) )
			{
				// error loading profile data
				return new WP_Error( 'acf_imp_fetch_parse', __( 'Error parsing fetch response', ACF_IMP_DOMAIN ) );
			}

			if ( $response->user->is_private )
			{
				// error loading profile data
				return new WP_Error( 'acf_imp_fetch_private', __( 'Given account is private!, please try different account.', ACF_IMP_DOMAIN ) );
			}

			$media_items = [];
			foreach ( $response->user->media->nodes as $media_item )
			{
				$media_items[] = [
					'id'        => $media_item->id,
					'type'      => $media_item->is_video ? 'video' : 'image',
					'permalink' => add_query_arg( [
						'taken-by' => $username,
						'__a'      => '1',
						'__b'      => '1',
					], 'https://www.instagram.com/p/' . $media_item->code . '/' ),
					'code'      => $media_item->code,
					'owner_uid' => $media_item->owner->id,
					'counts'    => [
						'likes'    => $media_item->likes->count,
						'comments' => $media_item->comments->count,
					],
					'image'     => [
						'thumbnail' => $media_item->thumbnail_src,
						'standard'  => $media_item->display_src,
					],
				];
			}
			unset( $media_item );

			// cache it
			set_transient( $media_cache_key, $media_items, $media_cache_time );
		}

		return $media_items;
	}
}

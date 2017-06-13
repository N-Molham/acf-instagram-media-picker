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
	 * Get media cache key based on given username
	 *
	 * @param string $username
	 *
	 * @return string
	 */
	public function get_media_cache_key( $username )
	{
		return 'acf_instagram_media_' . $username;
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
		$media_cache_key  = $this->get_media_cache_key( $username );
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
			$response = @json_decode( wp_remote_retrieve_body( $data_request ) );
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
					'owner'     => [
						'username' => $username,
						'uid'      => $media_item->owner->id,
					],
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

	/**
	 * Fetch given media code data
	 *
	 * @param string $media_code
	 * @param string $username
	 *
	 * @return array|WP_Error
	 */
	public function get_media_data( $media_code, $username )
	{
		$cache_key  = $this->get_media_cache_key( $username ) . '_' . $media_code;
		$media_item = get_transient( $cache_key );
		if ( false === $media_item )
		{
			$fetch_url = add_query_arg( [
				'taken-by' => $username,
				'__a'      => '1',
				'__b'      => '1',
			], 'https://www.instagram.com/p/' . $media_code . '/' );

			$fetch_request = wp_safe_remote_get( $fetch_url );
			if ( 200 !== wp_remote_retrieve_response_code( $fetch_request ) )
			{
				// request error
				return new WP_Error( 'acf_imp_fetch_error', __( 'Error getting given media data!', ACF_IMP_DOMAIN ) );
			}

			$response_body = @json_decode( wp_remote_retrieve_body( $fetch_request ) );
			if ( !is_object( $response_body ) || !isset( $response_body->graphql ) || !isset( $response_body->graphql->shortcode_media ) )
			{
				// parse error
				return new WP_Error( 'acf_imp_fetch_parse', __( 'Error parsing given media data!', ACF_IMP_DOMAIN ) );
			}

			// media data
			$media_item = &$response_body->graphql->shortcode_media;
			set_transient( $this->get_media_cache_key( $media_item->owner->username ) . '_' . $media_item->shortcode, $media_item, DAY_IN_SECONDS );
		}

		return [
			'id'        => $media_item->id,
			'type'      => $media_item->is_video ? 'video' : 'image',
			'permalink' => add_query_arg( [
				'taken-by' => $media_item->owner->username,
				'__a'      => '1',
				'__b'      => '1',
			], 'https://www.instagram.com/p/' . $media_item->shortcode . '/' ),
			'code'      => $media_item->shortcode,
			'owner'     => [
				'username' => $media_item->owner->username,
				'uid'      => $media_item->owner->id,
			],
			'counts'    => [
				'likes'    => $media_item->edge_media_preview_like->count,
				'comments' => $media_item->edge_media_to_comment->count,
			],
			'image'     => [
				'thumbnail' => '',
				'standard'  => $media_item->display_url,
			],
		];
	}

	/**
	 * Store target media data for later use
	 *
	 * @param string     $media_code
	 * @param string     $username
	 * @param int|string $post_id
	 *
	 * @return void
	 */
	public function store_media_data( $media_code, $username, $post_id )
	{
		global $wpdb;

		$table_name = $this->table_name();

		// fetch media data
		$media_data = $this->get_media_data( $media_code, $username );
		if ( is_wp_error( $media_data ) )
		{
			// data not found!
			return;
		}

		$query_data = [
			'media_code' => $media_data['code'],
			'media_type' => $media_data['type'],
			'username'   => $media_data['owner']['username'],
			'post_id'    => $post_id,
			'media_data' => maybe_serialize( $media_data ),
		];

		// check if exists or not
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT media_code FROM {$table_name} WHERE media_code = %s AND post_id = %s", $media_data['code'], $post_id ) ) )
		{
			// update
			$wpdb->update( $table_name, $query_data, [ 'media_code' => $media_data['code'], 'post_id' => $post_id ] );
		}
		else
		{
			// insert
			$wpdb->insert( $table_name, $query_data );
		}
	}

	/**
	 * Create the stored media table, if needed
	 *
	 * @return void
	 */
	public function create_media_table()
	{
		global $wpdb;

		// vars
		$table_name   = $this->table_name();
		$wpdb_collate = $wpdb->collate;

		$table_sql = "CREATE TABLE {$table_name} (
media_code varchar(64) NOT NULL,
media_type varchar(24) NOT NULL,
username varchar(64) NOT NULL,
post_id varchar(255) default NULL,
media_data TEXT NOT NULL,
PRIMARY KEY  (media_code),
KEY media_type (media_type),
KEY username (username),
KEY post_id (post_id)
) COLLATE {$wpdb_collate}";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $table_sql );
	}

	/**
	 * Media table name
	 *
	 * @return string
	 */
	public function table_name()
	{
		global $wpdb;

		return $wpdb->prefix . 'acf_imp_media';
	}
}

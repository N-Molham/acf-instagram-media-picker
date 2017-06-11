<?php namespace ACF\Add_Ons\Instagram_Media_Picker;

/**
 * AJAX handler
 *
 * @package ACF\Add_Ons\Instagram_Media_Picker
 */
class Ajax_Handler extends Component
{
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		parent::init();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		{
			$action = filter_var( isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '', FILTER_SANITIZE_STRING );
			if ( method_exists( $this, $action ) )
			{
				// hook into action if it's method exists
				add_action( 'wp_ajax_' . $action, [ &$this, $action ] );
			}
		}
	}

	/**
	 * Fetch user's instagram account latest media items
	 *
	 * @return void
	 */
	public function fetch_instagram_media_items()
	{
		// logged-in user ID
		$user_id = get_current_user_id();
		$max_id  = sanitize_key( (string) filter_input( INPUT_POST, 'max_id', FILTER_SANITIZE_STRING ) );

		$media_items = slc_user()->fetch_instagram_recent_media_items( $user_id, 9, $max_id );
		if ( is_wp_error( $media_items ) )
		{
			// error loading media
			$this->error( $media_items->get_error_message() );
		}

		// ajax success
		$this->success( $media_items );
	}


	/**
	 * AJAX Debug response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function debug( $data )
	{
		// return dump
		$this->error( $data );
	}

	/**
	 * AJAX Debug response ( dump )
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $args
	 *
	 * @return void
	 */
	public function dump( $args )
	{
		// return dump
		$this->error( print_r( func_num_args() === 1 ? $args : func_get_args(), true ) );
	}

	/**
	 * AJAX Error response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function error( $data )
	{
		wp_send_json_error( $data );
	}

	/**
	 * AJAX success response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data
	 *
	 * @return void
	 */
	public function success( $data )
	{
		wp_send_json_success( $data );
	}

	/**
	 * AJAX JSON Response
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response
	 *
	 * @return void
	 */
	public function response( $response )
	{
		// send response
		wp_send_json( $response );
	}
}

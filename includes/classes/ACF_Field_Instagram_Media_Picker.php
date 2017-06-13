<?php
/**
 * Created by PhpStorm.
 * User: Nabeel
 * Date: 11-Jun-17
 * Time: 12:07 PM
 */

namespace ACF\Add_Ons\Instagram_Media_Picker;

use acf_field;

class ACF_Field_Instagram_Media_Picker extends acf_field
{
	/**
	 * Whither or not to load the media item template
	 *
	 * @var boolean
	 */
	protected static $load_item_template = true;

	public function __construct()
	{
		// vars
		$this->name     = 'instagram_media_picker';
		$this->label    = __( 'Instagram Media Picker', ACF_IMP_DOMAIN );
		$this->category = 'choice';

		// default settings
		$this->defaults = [
			'media_type'          => 'image',
			'media_limit'         => 1,
			'browse_button_label' => __( 'Browse Images', ACF_IMP_DOMAIN ),
			'data_cache_hours'    => 1,
		];

		// localization
		$this->l10n = [
			'invalid_username' => __( 'Invalid username!', ACF_IMP_DOMAIN ),
		];

		parent::__construct();
	}

	/**
	 * Validate field value
	 *
	 * @param boolean $valid
	 * @param array   $value
	 * @param array   $field
	 * @param string  $input_name
	 *
	 * @return boolean
	 */
	public function validate_value( $valid, $value, $field, $input_name )
	{
		$value = filter_var_array( $value, [
			'images'   => [
				'filter'  => FILTER_VALIDATE_REGEXP,
				'options' => [ 'regexp' => '/^[a-zA-Z0-9]+((\,[a-zA-Z0-9]+)?)+$/' ],
			],
			'username' => [
				'filter'  => FILTER_VALIDATE_REGEXP,
				'options' => [ 'regexp' => '/^[a-zA-Z0-9\._]+$/' ],
			],
		] );

		if ( 2 !== count( $value ) )
		{
			// data is not in valid format
			return __( 'Invalid media data format!', ACF_IMP_DOMAIN );
		}

		$media_codes = array_filter( array_map( 'trim', explode( ',', $value['images'] ) ) );
		if ( $field['media_limit'] !== count( $media_codes ) )
		{
			return sprintf( __( '%s media required.', ACF_IMP_DOMAIN ), $field['media_limit'] );
		}

		foreach ( $media_codes as $media_code )
		{
			$media_data = acf_imp_instagram()->get_media_data( $media_code, $value['username'] );
			if ( is_wp_error( $media_data ) )
			{
				return __( 'Error loading media data for given information!', ACF_IMP_DOMAIN );
			}

			if ( $media_data['owner']['username'] !== $value['username'] || $media_data['code'] !== $media_code )
			{
				return __( 'Selected media is not owned by that user!', ACF_IMP_DOMAIN );
			}

			if ( $media_data['type'] !== $field['media_type'] )
			{
				return __( 'Selected media is not the correct type!', ACF_IMP_DOMAIN );
			}
		}

		return $valid;
	}

	/**
	 * When data is saved
	 *
	 * @param array $value
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return array
	 */
	public function update_value( $value, $post_id, $field )
	{
		$media_codes = array_filter( array_map( 'trim', explode( ',', $value['images'] ) ) );
		foreach ( $media_codes as $media_code )
		{
			acf_imp_instagram()->store_media_data( $media_code, $value['username'], $post_id );
		}

		return $value;
	}

	/**
	 * Render field output
	 *
	 * @param array $field_settings
	 *
	 * @return void
	 */
	public function render_field( $field_settings )
	{
		$field_settings['value'] = wp_parse_args( $field_settings['value'], [
			'images'   => '',
			'username' => '',
		] );

		acf_imp_view( 'media_picker_field', compact( 'field_settings' ) );
	}

	public function input_admin_head()
	{
		if ( self::$load_item_template )
		{
			acf_imp_view( 'media_item_template' );
		}
	}

	/**
	 * Load field assets
	 *
	 * @return void
	 */
	public function input_admin_enqueue_scripts()
	{
		// assets info
		$load_path      = Helpers::enqueue_path();
		$assets_version = Helpers::assets_version();

		// Bootstrap Modal assets
		wp_register_style( 'acf-imp-bootstrap-modal', $load_path . 'css/bootstrap-modal.css', null, $assets_version );
		wp_register_script( 'acf-imp-bootstrap-modal', $load_path . 'js/bootstrap-modal.js', [ 'jquery' ], $assets_version, true );

		// picker assets
		wp_enqueue_style( 'acf-imp-media-picker', untrailingslashit( ACF_IMP_URI ) . '/assets/dist/css/instagram-media-picker.css', [
			'dashicons',
			'acf-imp-bootstrap-modal',
		], $assets_version );

		wp_enqueue_script( 'acf-imp-media-picker', $load_path . 'js/instagram-media-picker.js', [
			'jquery',
			'acf-imp-bootstrap-modal',
		], $assets_version, true );

		wp_localize_script( 'acf-imp-media-picker', 'acf_imp_media_picker', [
			'ajax_url'   => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
			'ajax_nonce' => wp_create_nonce( 'acf_imp_fetch_media' ),
			'field_name' => $this->name,
		] );
	}

	/**
	 * Render field settings/options in backend
	 *
	 * @param array $field_settings
	 *
	 * @return void
	 */
	public function render_field_settings( $field_settings )
	{
		// media type setting
		acf_render_field_setting( $field_settings, [
			'label'        => __( 'Media Type', ACF_IMP_DOMAIN ),
			'instructions' => __( 'What type of instagram media type to choose from?', ACF_IMP_DOMAIN ),
			'type'         => 'radio',
			'name'         => 'media_type',
			'choices'      => [
				'image' => __( 'Image', ACF_IMP_DOMAIN ),
				'video' => __( 'Video', ACF_IMP_DOMAIN ),
			],
		] );

		// media items limit setting
		acf_render_field_setting( $field_settings, [
			'label'        => __( 'Number of Media Items', ACF_IMP_DOMAIN ),
			'instructions' => __( 'How many items required to be picked?', ACF_IMP_DOMAIN ),
			'type'         => 'number',
			'name'         => 'media_limit',
			'required'     => true,
			'min'          => 1,
			'step'         => 1,
		] );

		// browse button label setting
		acf_render_field_setting( $field_settings, [
			'label'        => __( 'Browse button label', ACF_IMP_DOMAIN ),
			'instructions' => __( 'What is the label of the media browse button?', ACF_IMP_DOMAIN ),
			'type'         => 'text',
			'name'         => 'browse_button_label',
			'required'     => true,
		] );

		// data cache hours setting
		acf_render_field_setting( $field_settings, [
			'label'        => __( 'Data Cache Duration', ACF_IMP_DOMAIN ),
			'instructions' => __( 'For how long should the user fetched instagram media items list be cached for? in hours', ACF_IMP_DOMAIN ),
			'type'         => 'number',
			'name'         => 'data_cache_hours',
			'required'     => true,
			'min'          => 1,
			'step'         => 1,
		] );
	}
}
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
			'instructions' => __( 'For how long should the user fetched instagram media items list be cached for? in hours, <code>0</code> to disable cache', ACF_IMP_DOMAIN ),
			'type'         => 'number',
			'name'         => 'data_cache_hours',
			'required'     => true,
			'min'          => 0,
			'step'         => 1,
		] );
	}
}
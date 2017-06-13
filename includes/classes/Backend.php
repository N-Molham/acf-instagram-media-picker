<?php namespace ACF\Add_Ons\Instagram_Media_Picker;

/**
 * Backend logic
 *
 * @package ACF\Add_Ons\Instagram_Media_Picker
 */
class Backend extends Component
{
	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		parent::init();

		// Plugin activation hook
		register_activation_hook( ACF_IMP_MAIN_FILE, [ &$this, 'plugin_activated' ] );
		add_action( 'admin_action_acf_imp_setup', [ &$this, 'plugin_activated' ] );

		// ACF: register custom field types
		add_action( 'acf/include_field_types', [ &$this, 'register_new_field' ] );
	}

	/**
	 * Register the new field type
	 *
	 * @return void
	 */
	public function register_new_field()
	{
		acf_register_field_type( new ACF_Field_Instagram_Media_Picker() );
	}

	/**
	 * When plugin gets activated
	 *
	 * @return void
	 */
	public function plugin_activated()
	{
		if ( !defined( 'WP_CLI' ) || ( defined( 'WP_CLI' ) && true !== WP_CLI ) )
		{
			if ( false === current_user_can( 'manage_options' ) )
			{
				// skip if current user has no permission
				return;
			}
		}

		acf_imp_instagram()->create_media_table();
	}
}

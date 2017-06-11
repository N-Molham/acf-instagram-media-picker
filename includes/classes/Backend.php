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
}

<?php namespace ACF\Add_Ons\Instagram_Media_Picker;

/**
 * Base Component
 *
 * @package ACF\Add_Ons\Instagram_Media_Picker
 */
class Component extends Singular
{
	/**
	 * Plugin Main Component
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function init()
	{
		// vars
		$this->plugin = Plugin::get_instance();
	}
}

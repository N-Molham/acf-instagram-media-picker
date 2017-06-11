<?php
/**
 * Created by Nabeel
 * Date: 2016-01-22
 * Time: 2:38 AM
 *
 * @package ACF\Add_Ons\Instagram_Media_Picker
 */

use ACF\Add_Ons\Instagram_Media_Picker\Component;
use ACF\Add_Ons\Instagram_Media_Picker\Plugin;

if ( !function_exists( 'acf_instagram_media_picker' ) ):
	/**
	 * Get plugin instance
	 *
	 * @return Plugin
	 */
	function acf_instagram_media_picker()
	{
		return Plugin::get_instance();
	}
endif;

if ( !function_exists( 'acf_imp_component' ) ):
	/**
	 * Get plugin component instance
	 *
	 * @param string $component_name
	 *
	 * @return Component|null
	 */
	function acf_imp_component( $component_name )
	{
		if ( isset( acf_instagram_media_picker()->$component_name ) )
		{
			return acf_instagram_media_picker()->$component_name;
		}

		return null;
	}
endif;

if ( !function_exists( 'acf_imp_view' ) ):
	/**
	 * Load view
	 *
	 * @param string  $view_name
	 * @param array   $args
	 * @param boolean $return
	 *
	 * @return void
	 */
	function acf_imp_view( $view_name, $args = null, $return = false )
	{
		if ( $return )
		{
			// start buffer
			ob_start();
		}

		acf_instagram_media_picker()->load_view( $view_name, $args );

		if ( $return )
		{
			// get buffer flush
			return ob_get_clean();
		}
	}
endif;

if ( !function_exists( 'acf_imp_version' ) ):
	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	function acf_imp_version()
	{
		return acf_instagram_media_picker()->version;
	}
endif;
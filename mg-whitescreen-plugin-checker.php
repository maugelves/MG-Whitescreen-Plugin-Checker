<?php
/*
Plugin Name: MG Whitescreen Plugin Checker
Plugin URI: https://maugelves.com/
Description: This Plugin checks which plugin is generating conflict in the website.
Version: 1.0
Author: Mauricio Gelves
Author URI: https://maugelves.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Set the constant for the $_GET parameter;
define( 'MGWPC_GET_PARAMETER', 'wpc' );

/**
 * Main function for the filter 'option_active_plugins'.
 *
 * @param $plugin_list
 *
 * @return array
 */
function mg_whitescreen_plugin_checker( $plugin_list ) {

	// Check for the defiend GET parameter.
	if( ! empty( $_GET[ MGWPC_GET_PARAMETER ] ) ):

		print_r( '<p>Comenzando el análisis de los plugins</p>' );

		// Select the type of Testing.
		switch ( $_GET[ MGWPC_GET_PARAMETER ] ):
			case 'complete': // Checks all possible combinations.
				mg_test_all_plugins_combinations( $plugin_list );
				break;
			case 'individually': // Checks every plugin individually.
				mg_test_all_plugins_individually( $plugin_list );
				break;
			case 'test': // Modify the current $plugin_list parameter to check the HTTP Code.
				$plugin_list = empty( $_POST ) ? [] : explode( ',', $_POST['plugins'] );
				break;
			default:
				break;
		endswitch;

		// Show final test message.
		switch ( $_GET[ MGWPC_GET_PARAMETER ] ):
			case 'complete':
			case 'individually':
				print_r( '<p>Test finalizado.</p>' );
				exit;
		endswitch;

	endif;

	// Otherwise it continues the normal flow of the filter.
	return $plugin_list;
}
add_filter('option_active_plugins', 'mg_whitescreen_plugin_checker');


/**
 * Shows HTTP error responses.
 *
 * @param $http_code
 * @param $active_plugins
 */
function mg_print_result( $http_code, $active_plugins ) {
	if( $http_code != 200 ) {
		$active_plugins = is_array( $active_plugins ) ? implode( ', ', $active_plugins ) : $active_plugins ;

		print_r( '<div style="margin-bottom: 10px; background-color: #eee; padding: 1em;">El/los plugin(s) <b>' . $active_plugins . '</b> ha devuelto un código <b>HTTP ' . $http_code . '</b></div>' );
	}
}


/**
 * This function checks all the plugins individually.
 *
 * @param $plugin_list array with all the active Plugins.
 */
function mg_test_all_plugins_individually( $plugin_list ) {

	$current_url = mg_get_current_url();

	foreach ( $plugin_list as $plugin ):

		// Make a new call
		$http_result = get_http_code_from_curl_call( mg_get_current_url() . '?' . MGWPC_GET_PARAMETER . '=test', $plugin );

		mg_print_result( $http_result, $plugin );

	endforeach;

}

/**
 * This functions returns the current URL (since WordPress it's not fully loaded)
 * without GET parameters.
 *
 * @return string   Current URL.
 */
function mg_get_current_url() {

	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$request_uri = $_SERVER['HTTP_HOST'] . substr( $_SERVER['REQUEST_URI'], 0,  strpos( $_SERVER['REQUEST_URI'], '?' ));

	return $protocol . $request_uri;
}

/**
 * This functions checks all the possible combinations between the plugins.
 * It should be used only when individual check doesn't return any error.
 *
 * @param $plugin_list array with all the active Plugins.
 */
function mg_test_all_plugins_combinations( $plugin_list ) {

	// Get all the possible combinations.
	$plugins_combinations = mg_create_array_combinations( $plugin_list );

	foreach ( $plugins_combinations as $plugins_combination ):

		// Make a new call
		$http_result = get_http_code_from_curl_call( mg_get_current_url() . '?' . MGWPC_GET_PARAMETER . '=test', $plugins_combination );

		mg_print_result( $http_result, $plugins_combination );

	endforeach;

}


/**
 * This functions creates and returns an array with all the possible combinations from an array.
 *
 * @param $array    array     Original array to be combined.
 *
 * @return array    Array of combined arrays.
 */
function mg_create_array_combinations( $array ) {
	// initialize by adding the empty set
	$results = array(array( ));

	foreach ($array as $element)
		foreach ($results as $combination)
			array_push($results, array_merge(array($element), $combination));

	return $results;
}


/**
 * Checks the HTTP code for the calls to the website
 * activating only the plugins passed in the parameter.
 *
 * @param $website          string      Website to be checkd
 * @param $active_plugins   array       Array with the plugins to be tested.
 *
 * @return mixed
 */
function get_http_code_from_curl_call( $website, $active_plugins) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $website);
	curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
	curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT,10);

	if( ! empty( $active_plugins ) ):
		curl_setopt($ch, CURLOPT_POST, 1);
		$postfields = is_array( $active_plugins ) ? implode( ',', $active_plugins ) : $active_plugins;
		curl_setopt($ch, CURLOPT_POSTFIELDS, '&plugins=' . $postfields );
	endif;

	curl_exec($ch);

	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);

	return $httpcode;
}

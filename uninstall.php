<?php

/**
* Exit if accessed directly
*
*/
if ( ! defined( 'ABSPATH' ) ) exit;


/**
* Exit if this plugin is not being uninstalled
*
*/
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();


/**
* Delete the plugin's option
*
*/
delete_option( 'cc_badger' );
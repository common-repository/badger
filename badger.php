<?php
/*
Plugin Name: Badger
Plugin URI: http://cubecolour.co.uk/badger
Description: Change the browser tab title when another browser tab is active.
Author: Michael Atkins
Version: 2.0.0
Author URI: http://cubecolour.co.uk
Text Domain: badger
License: GPL

	Copyright 2017-2023 Michael Atkins

	Plugin Licenced under the GNU GPL:

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without restriction,
	including without limitation the rights to use, copy, modify,
	merge, publish, distribute, sublicense, and/or sell copies of
	the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall
	be included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	DEALINGS IN THE SOFTWARE.

*/

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Define Constants
 *
 */
define( 'CC_BADGER_PLUGIN_VERSION', '2.0.0' );
define( 'CC_BADGER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CC_BADGER_PLUGIN_BASENAME', plugin_basename(__FILE__) );


/**
* Add Links to this plugin's entry in the Plugins page
*
*/
function cc_badger_meta_links( $links, $file ) {

	if ( $file == CC_BADGER_PLUGIN_BASENAME ) {

		$link = array(
			'customize'	=> admin_url( 'customize.php?autofocus[section]=cc_badger_section' ),
			'support'	=> 'https://wordpress.org/support/plugin/badger',
			'review'	=> 'https://wordpress.org/support/view/plugin-reviews/badger#postform',
			'donate'	=> 'http://cubecolour.co.uk/wp',
			'twitter'	=> 'http://twitter.com/cubecolour',
		);

		$text = array(
			'customize'	=> __( 'Customize Badger', 'badger' ),
			'support'	=> __( 'Badger Support', 'badger' ),
			'review'	=> __( 'Review Badger', 'badger' ),
			'donate'	=> __( 'Donate', 'badger' ),
			'twitter'	=> __( 'Cubecolour on Twitter', 'badger' ),
		);

		$iconstyle		= ' style="-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;font-size:14px;margin:4px 0 -4px;" ';

		$addlinks = array(
			'<a href="' . $link[ 'customize' ] . '"><span class="dashicons dashicons-admin-generic"' . $iconstyle . 'title="' . $text[ 'customize' ] . '"></span></a>',
			'<a href="' . $link[ 'support' ] . '"> <span class="dashicons dashicons-sos"' . $iconstyle . 'title="' . $text[ 'support' ] . '"></span></a>',
			'<a href="' . $link[ 'review' ] . '"><span class="dashicons dashicons-star-filled"' . $iconstyle . 'title="' . $text[ 'review' ] . '"></span></a>',
			'<a href="' . $link[ 'donate' ] . '"><span class="dashicons dashicons-heart"' . $iconstyle . 'title="' . $text[ 'donate' ] . '"></span></a>',
			'<a href="' . $link[ 'twitter' ] . '"><span class="dashicons dashicons-twitter"' . $iconstyle . 'title="' . $text[ 'twitter' ] . '"></span></a>',
		);

		return array_merge( $links, $addlinks );
	}

	return $links;
}
add_filter( 'plugin_row_meta', 'cc_badger_meta_links', 10, 2 );


/**
 * Register and Enqueue the script
 *
 */
function cc_badger_script() {

	wp_register_script( 'badger', CC_BADGER_PLUGIN_URL . 'js/badger.js', '', CC_BADGER_PLUGIN_VERSION, false );

	//* Define Defaults
	$defaults = array (
		'alttitle'		=> get_bloginfo( 'name' ),
		'suffix'		=> 0,
	);

	//* Add options array with default values if it does not exist
    if( !get_option( 'cc_badger' ) ) {
        add_option( 'cc_badger', $defaults );
    }

	//* Get the options array
	$option = get_option( 'cc_badger', $defaults );

	//* Get the separator character from the suffix options array value
	switch ( $option[ 'suffix' ] ) {

		case 0:
			$sepchar = '';
	        break;

		case 1:
			$sepchar = ' - ';
			break;

		case 2:
			$sepchar = ' + ';
			break;

		case 3:
			$sepchar = ' > ';
			break;

		case 4:
			$sepchar = ' &raquo; ';
			break;

		default:
			$sepchar = ' ';
	}

	//* get the value of the alt title setting
	$alttitle = esc_html( $option[ 'alttitle' ] );

	$scriptdata = array(
	    'alttitle'	=> $alttitle . $sepchar,
	    'suffixindex' => absint( $option[ 'suffix' ] ),
	);

	wp_localize_script( 'badger', 'php_vars', $scriptdata );

	wp_enqueue_script( 'badger' );
}
add_action('wp_enqueue_scripts', 'cc_badger_script');



/**
 * Customizer
 *
 */
function cc_badger_customize_register( $wp_customize ){

	/**
	 * Customizer Section
	 *
	 */
	$wp_customize->add_section(
		'cc_badger_section', array(
			'description'		=> __( 'Replace the browser tab title with custom text which alternates with the original title when another tab is active', 'badger' ),
			'priority'			=> '800',
			'title'				=> __( 'Badger', 'badger' ),
			'capability'		=> 'edit_theme_options',
		)
	);


	/**
	 *  Alt Title
	 *
	 */
	$wp_customize->add_setting( 'cc_badger[alttitle]', array(
			'default'			=> get_bloginfo( 'name' ),
			'capability'		=> 'edit_theme_options',
			'sanitize_callback'	=> 'esc_textarea',
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		)
	);


	$wp_customize->add_control( 'alttitle', array(
			'label'				=> __( 'Alternative Title', 'badger' ),
			'description'		=> __( 'Alternates with the original title when the browser tab loses focus', 'badger' ),
			'section'			=> 'cc_badger_section',
			'priority'			=> '10',
			'type'				=> 'text',
			'settings'			=> 'cc_badger[alttitle]',
		)
	);


	/**
	 *  Suffix
	 *
	 */
	$wp_customize->add_setting( 'cc_badger[suffix]', array(
			'default'			=> 0,
			'capability'		=> 'edit_theme_options',
			'sanitize_callback'	=> 'cc_badger_esc_suffix',
			'type'				=> 'option',
			'transport'			=> 'postMessage',
		)
	);


	$wp_customize->add_control( 'suffix', array(
			'label'				=> __( 'Suffix', 'badger' ),
			'description'		=> __( 'The original title will be appended to the end of the alternative title with a separating character if selected', 'badger' ),
			'section'			=> 'cc_badger_section',
			'priority'			=> '20',
			'type'				=> 'select',
			'choices'			=> array(
				'0'				=> 'None',
				'1'				=> '- Original Title',
				'2'				=> '+ Original Title',
				'3'				=> '> Original Title',
				'4'				=> '&raquo; Original Title',
			),
			'settings'			=> 'cc_badger[suffix]',
		)
	);
}
add_action( 'customize_register', 'cc_badger_customize_register' );


/**
* Sanitize Suffix value
*
*/
function cc_badger_esc_suffix( $input ) {
	if ( ( absint( $input ) >= 0 ) && ( absint( $input ) <= 4 ) ) {
		return absint( $input );
	} else {
		return '0';
	}
}
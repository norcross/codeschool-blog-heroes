<?php
/*
Plugin Name: Codeschool Blog Heroes
Plugin URI: https://github.com/norcross/
Description: Create fields for entering and displaying hero images
Version: 0.0.1
Author: Andrew Norcross
Author URI: http://andrewnorcross.com

	Copyright 2015 Andrew Norcross

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// defined version
if( ! defined( 'CSBH_VER' ) ) {
	define( 'CSBH_VER', '0.0.1' );
}

// Start up the engine
class CSBH_Core
{

	/**
	 * Static property to hold our singleton instance
	 * @var Code_Docs_Core
	 */
	static $instance = false;

	/**
	 * [__construct description]
	 */
	private function __construct() {
		add_action( 'plugins_loaded',                       array( $this, 'load_files'                  )           );
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return
	 */
	public static function getInstance() {

		// check for an instance of the class before loading
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		// return the instance
		return self::$instance;
	}

	/**
	 * load our files
	 *
	 * @return [type] [description]
	 */
	public function load_files() {

		// admin files
		if ( is_admin() ) {
			require_once( 'lib/admin.php' );
		}

		// front end files
		if ( ! is_admin() ) {
			require_once( 'lib/front.php' );
		}
	}


/// end class
}

// Instantiate our class
$CSBH_Core = CSBH_Core::getInstance();
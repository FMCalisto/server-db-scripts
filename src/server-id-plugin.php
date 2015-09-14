<?php
/*
Plugin Name: Server-ID Plugin
Plugin URI: 
Description: 
Version: 1.0
Author: Francisco Maria Calisto
Author Email: francisco.mcalisto@gmail.com
*/
require_once 'server-config.php';
require_once 'server-util.php';
require_once 'server-people.php';
require_once 'server-pubs.php';
require_once 'server-advising.php';
require_once 'server-calls.php';

class Server-IDPlugin {


	const name = 'Server-ID Plugin';
	const slug = 'server-id_plugin';
	
	/**
	 * Constructor
	 */
	function __construct() {
		register_activation_hook( __FILE__, array( &$this, 'install_server-id_plugin' ) );


		add_action( 'init', array( &$this, 'init_server-id_plugin' ) );
	}
  

	function install_server-id_plugin() {

	}

	function init_server-id_plugin() {
		load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

		$this->register_scripts_and_styles();


		add_shortcode( 'server', array( &$this, 'render_shortcode' ) );
	
		if ( is_admin() ) {
			
		} else {
			
		}  
	}


	function render_shortcode($atts) {
		extract(shortcode_atts(array(
			'what' => '', 
			'cc' => ''
			), $atts));
	}
  

	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-admin-script', '/js/admin.js', true );
			$this->load_file( self::slug . '-admin-style', '/css/admin.css' );
		} else {
			$this->load_file( self::slug . '-script', '/js/widget.js', true );
			$this->load_file( self::slug . '-style', '/css/widget.css' );
		}
	} 
	

	private function load_file( $name, $file_path, $is_script = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} 
		} 

	} 
  function renderServer($input, $argv) {
    // connect to the database
    $idDBLink = odbc_connect('NAME', 'user', 'password');
    if (!$idDBLink) { exit("Connection to database failed! Please contact your DataBase Administrator."); }
  
    $html = "";
    if ($argv['what'] == 'person') {
      $id = split(",", trim($argv["id"]));
      if ($id != '') {
        // information about someone:
        //  1. personal contacts and summary
        //  2. publications by person
        //  3. advisory work by person
        //
        $html .= personById($idDBLink, $id[0]);
  
        $internalIds = authorIdByNumber($idDBLink, $id);  // use all Ids
        $html .= pubsById($idDBLink, $internalIds);
        $html .= advisingById($idDBLink, $internalIds);
      }
      
    }
    else if ($argv['what'] == 'advising') {
      $id = split(",", trim($argv["id"]));
      if ($id != '') {
        $internalIds = authorIdByNumber($idDBLink, $id);  // use all Ids
        $html .= iconv('latin1', 'UTF-8', advisingById($idDBLink, $internalIds));
      }
  
    }
    else if ($argv['what'] == 'publications') {
      // information about some "centro de custo":
      //  1. currently, only a list of publications
      //
      $cc = trim($argv["cc"]);
      $id = trim($argv["id"]);
      if ($cc != '') {
        $html .= iconv('latin1', 'UTF-8', pubsByCC($idDBLink, $cc));
      }
      else if ($id != '') {
        $html .= iconv('latin1', 'UTF-8', pubsById($idDBLink, authorIdByNumber($idDBLink, array($id))));
      }
    }
    /*else if ($argv['what'] == 'publications') {
      // information about some "centro de custo":
      //  1. currently, only a list of publications
      //
      $cc = trim($argv["cc"]);
      if ($cc != '') {
        $html .= pubsByCC($idDBLink, $cc);
      }
    }*/
    else if ($argv['what'] == 'calls') {
      // information about some "centro de custo":
      //  1. currently, only a list of publications
      //
      $cc = trim($argv["cc"]);
      $showClosed = isset($argv['showclosed']) ? trim($argv['showclosed']) : "";
      if ($cc != '') {
        $html .= iconv('latin1', 'UTF-8', callsByCC($idDBLink, $cc, $showClosed == "yes"));
      }
    }
    else {
      // oops! no text...
    }
  
    odbc_close($idDBLink);
    return $html;
  }
  
}
new Server-IDPlugin();

?>
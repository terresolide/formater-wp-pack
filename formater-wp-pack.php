<?php
/**
 * Plugin Name: Formater-wp-pack
 * Description: Manage svg and pdf files
 * Version: 0.1.0
 * Author: epointal
 * Author URI: http://elisabeth.pointal.org/
 * GitHub Plugin URI: https://github.com/epointal/formater-wp-pack
 **/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path( __FILE__ ) . '/class/Fm_pdf_manager.php';
require_once plugin_dir_path( __FILE__ ) . '/class/Fm_svg_manager.php';

Class Formater_WP_Pack
{
    private static $_instance;
    private static $_pdf_manager;
    private static $_svg_manager;
    
    private function __construct()
    {
        add_action('plugins_loaded', array( &$this, 'initialize'));
    }
    
    public function initialize(){
        self::$_pdf_manager = new Fm_pdf_manager();
        self::$_svg_manager =  new Fm_svg_manager();
        
    }
    
    public static function get_instance(){
        if( is_null( self::$_instance)){
            self::$_instance = new Formater_WP_Pack();
        }
        return self::$_instance;
    }
}

Formater_WP_Pack::get_instance();
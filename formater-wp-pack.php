<?php
/**
 * Plugin Name: Formater-wp-pack
 * Description: Manage svg and pdf files
 * Version: 1.0.3
 * Author: epointal
 * Author URI: http://elisabeth.pointal.org/
 * GitHub Plugin URI: terresolide/formater-wp-pack
 **/

if ( ! defined( 'ABSPATH' ) ) exit;



Class Formater_WP_Pack
{
    private static $_instance;
    private static $_pdf_manager;
    private static $_svg_manager;
    public static $url;
    public static $VERSION = '1.0.3';
    
    private function __construct()
    {
    	self::$url = plugins_url().'/formater-wp-pack';
    	add_action('plugins_loaded', array( &$this, 'initialize'));
    }
    
    public function initialize(){
        require_once plugin_dir_path( __FILE__ ) . '/class/Fm_pdf_manager.php';
        require_once plugin_dir_path( __FILE__ ) . '/class/Fm_svg_manager.php';
        self::$_pdf_manager = new Fm_pdf_manager();
        self::$_svg_manager = new Fm_svg_manager();     
    }
    
    public static function get_instance(){
        if( is_null( self::$_instance)){
            self::$_instance = new Formater_WP_Pack();
        }
        return self::$_instance;
    }
}

Formater_WP_Pack::get_instance();
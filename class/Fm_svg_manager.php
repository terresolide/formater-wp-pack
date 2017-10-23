<?php
/**
 * Manage svg files
 * @author epointal
 *
 */
if (! defined ( 'ABSPATH' ))
    exit ();


class Fm_svg_manager {
    // svg count in post/page
    public static $_count_svg = 0;
    public static $_plugin_dir = '';
    public function __construct( ) {
        self::$_plugin_dir = plugin_dir_path (__DIR__);
        
        if (is_admin ()) {
            // Add svg and xml as a supported upload type to Media Gallery
            add_filter ( 'upload_mimes', array (
                    &$this,
                    'svg_upload_mimes' 
            ) );
            
            // filter for svg in media gallery
            add_filter ( 'post_mime_types', array (
                    &$this,
                    'svg_post_mime_types' 
            ) );
            
            // Embed svg shortcode instead of link
            add_filter ( 'media_send_to_editor', array (
                    &$this,
                    'svg_media_send_to_editor'),
                    20,
                    3 
            ) ;
        } else {
            // include content for svg file
            add_shortcode ( "formater-svg", array( &$this, "include_file_svg") );
        }
    }
    
    /**
     * Add xml and svg in array of supported upload type
     * 
     * @param array $existing_mimes            
     * @return array
     */
    public function svg_upload_mimes($existing_mimes = array()) {
        if (! isset ( $existing_mimes ['xml'] )) {
            $existing_mimes ['xml'] = 'application/xml';
        }
        if (! isset ( $existing_mimes ['svg'] )) {
            $existing_mimes ['svg'] = 'image/svg+xml';
        }
        return $existing_mimes;
    }
    
    /**
     * Add svg filter in media gallery file type filter
     * 
     * @param array $post_mime_types            
     * @return unknown
     */
    public function svg_post_mime_types($post_mime_types = array()) {
        $post_mime_types ['image/svg+xml'] = array (
                'SVG',
                'Manage SVGs',
                'SVG <span class="count">(%s)</span>' 
        );
        return $post_mime_types;
    }
    
    /**
     * Return shortcode for svg instead of image
     * 
     * @param string $html
     *            the html string to insert in post
     * @param integer $id
     *            the post id
     * @param array $attachment
     *            the field in media gallery (url, align
     * @return string
     */
    public function svg_media_send_to_editor($html, $id, $attachment) {
        
        if (isset ( $attachment ['url'] ) && preg_match ( "/\.svg$/i", $attachment ['url'] )) {
            $class = "";
            if (isset ( $attachment ['align'] )) {
                switch ($attachment ['align']) {
                    case 'left' :
                    case 'right' :
                        $class = 'class="fm-' . $attachment ['align'] . '"';
                        break;
                }
            }
            $filter = '[formater-svg src="' . $attachment ['url'] . '" ' . $class . ' ][/formater-svg]';
            return apply_filters ( 'svg_override_send_to_editor', $filter, $html, $id, $attachment );
        } else {
            return $html;
        }
    }
    
    /**
     * Best practice wordpress is to register script and style
     * Not to include direct tag script or style
     * But it's heavy to load a js or css files for only one function
     * Then I give up this method
     */
    /**
     * Return the content of javascript file manage-svg i
     * ready to insert in post
     * 
     * @return string
     */
    private static  function svg_script() {
        if (WP_DEBUG) {
            $script = file_get_contents ( self::$_plugin_dir . 'js/manage-svg.js' );
        } else {
            $script = file_get_contents ( self::$_plugin_dir. 'dist/manage-svg-min.js' );
        }
        return '<script type="text/javascript">' . $script . '</script>';
    }
    /**
     * Return the content of css file manage-svg
     * ready to insert in post
     * 
     * @return string
     */
    private static function svg_style() {
        if (WP_DEBUG) {
            $style = file_get_contents ( self::$_plugin_dir. 'css/manage-svg.css' );
        } else {
            $style = file_get_contents ( self::$_plugin_dir. 'dist/manage-svg.css' );
        }
        return '<style>' . $style . '</style>';
    }
    /**
     * Return the html to include in post
     * 
     * @param array $attrs
     *            the shortcode attributes : src, class, hide_button
     * @param string $html            
     * @return string
     */
    public function include_file_svg($attrs = array(), $html = '') {
        $upload_info = wp_upload_dir ();
        $upload_dir = $upload_info ['basedir'];
        $upload_url = $upload_info ['baseurl'];
        
        $url = $attrs ["src"];
        // load by path instead url
        $path = realpath ( str_replace ( $upload_url, $upload_dir, $url ) );
  
        // $svg = file_get_contents($url);
        if (! file_exists ( $path )) {
            return "";
        }
        $doc = new DOMDocument ();
        $doc->load ( $path );
        $svgs = $doc->getElementsByTagName ( 'svg' );
        $content = '';
        
        if ($svgs->length == 0) {
            return "";
        } else {
            if (self::$_count_svg == 0) {
                /**
                 * Is not wordpress best practice to include tag script in html
                 * but it's heavy to load a script file for only one little function
                 * see before
                 */
                $content .= self::svg_style ();
                $content .= self::svg_script ();
            }
            self::$_count_svg ++;
            if (isset ( $attrs ['class'] ) && ! isset ( $attrs ['hide_button'] )) {
                $value = $attrs ['class'] == 'fm-right' ? 1 : 0;
                $content .= '<div class="formater-svg ' . $attrs ['class'] . '"  >';
                $content .= '<div class="fm-enlarge fa" onclick="formater_switch_svg( this, ' . $value . ')"></div>';
            } else {
                $content .= '<div class="formater-svg">';
            }
            $svg = $svgs->item ( 0 );
            $svg->setAttribute ( 'preserveAspectRatio', 'xMinYMin meet' );
            $content .= $doc->saveHTML ( $svg ) . '</div>';
       
            return $content;
        }
    }
}
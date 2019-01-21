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
            /* Add "shortcode" with GUTENBERG editor too
            */
            add_action( 'enqueue_block_editor_assets', array($this, 'gutenberg_enqueue_block_editor_assets') );
            // Add field "interactive" to svg in media manager
            add_filter( 'attachment_fields_to_edit', array( &$this, 'svg_attachment_field'), 10, 2 );
            add_filter( 'attachment_fields_to_save', array( &$this, 'svg_attachment_save_field' ), 10, 2 );
            
        } else {
        	add_action ( 'wp_enqueue_scripts', array( &$this, 'formater_register_svg_script' ));
            // include content for svg file
            add_shortcode ( "formater-svg", array( &$this, "include_file_svg") );
           
            // Gutenberg block js/svg-block
            if (function_exists('register_block_type')) {
            	register_block_type( 'formater-wp-pack/svg-block', array(
            			'render_callback' => array($this, 'include_file_svg')
            	) );
            }
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
     * Add a field "interactive" in media manager to svg
     * @param array $form_fields
     * @param WP_post $post
     * @return array
     */
    public function svg_attachment_field( $form_fields, $post )
    {
        
        if($post->post_mime_type == 'image/svg+xml'){
            
            $value = get_post_meta( $post->ID, 'fm_interactive', true ); 

            $html = $this->create_field_interactive( $post->ID , $value);
            
            $form_fields['fm_interactive'] = array(
                    'label' => 'Interactive',
                    'input' => 'html',
                    'value' => get_post_meta( $post->ID, 'fm_interactive', true),
                    'html'  => $html
                    );
        }
        return $form_fields;
    }
    
    /**
     * Save interactive value for svg file 
     * @param array $post
     * @param array $attachment
     * @return array
     */
    public function svg_attachment_save_field( $post, $attachment){
        
        if(isset($attachment['fm_interactive']))
        {
            update_post_meta($post['ID'], 'fm_interactive', 1);
            
        } else if($post['post_mime_type'] == 'image/svg+xml'){
            
            update_post_meta($post['ID'], 'fm_interactive', 0);
        }
        return $post;
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
        
        $interactive = false;
        if ( ( isset( $attachment['url']) && preg_match ( "/\.svg$/i", $attachment ['url'] )) 
                || preg_match ( "/\.svg/i", $html) ){
            $interactive = get_post_meta($id, 'fm_interactive', true);
          
        }

        if ( $interactive ) {
            $class = "";
            $url = wp_get_attachment_image_src( $id )[0];
            if ( isset ( $attachment ['align'] )) {
                switch ($attachment ['align']) {
                    case 'left' :
                    case 'right' :
                        $class = 'class="fm-' . $attachment ['align'] . '"';
                        break;
                }
            }
            $filter = '[formater-svg src="' . $url . '" ' . $class . ' ][/formater-svg]';
            return apply_filters ( 'svg_override_send_to_editor', $filter, $html, $id, $attachment );
        } else {
            return $html;
        }
    }
    /**
     * Create the checkbox for svg field interactive
     * @param integer $post_id
     * @param boolean|integer $value
     * @return string
     */
    private function create_field_interactive( $post_id, $value ){
        $checked = $value ? 'checked="checked"': '';
        
        $html = '<input type="checkbox" name="attachments['. $post_id .'][fm_interactive]"';
        $html .= ' id="attachments['. $post_id .'][fm_interactive]" ';
        $html .= ' value="' .$value .'" ';
        $html .= $checked . '  />';
        
        return $html;
    }
    /**
     * Best practice wordpress is to register script and style
     * Register js or css files 
     */
    public function formater_register_svg_script() {
    	if (WP_DEBUG) {
    		wp_register_script ( 'fmt_svg_js', Formater_WP_Pack::$url.'/js/manage-svg.js', Array (), Formater_WP_Pack::$VERSION, true );
    		wp_register_style ('fmt_svg_css', Formater_WP_Pack::$url.'/css/manage-svg.css', Array(), Formater_WP_Pack::$VERSION, null);
    	}else{
    		wp_register_script ( 'fmt_svg_js', Formater_WP_Pack::$url.'/dist/manage-svg-min.js', Array (), Formater_WP_Pack::$VERSION );
    		wp_register_style ('fmt_svg_css', Formater_WP_Pack::$url.'/dist/manage-svg.css', Array(), Formater_WP_Pack::$VERSION);
    		
        }
    }
    /**
     * Return the content of javascript file manage-svg i
     * ready to insert in post
     * @todo function unused
     * @return string
     */
    private static  function svg_script() {
        if (WP_DEBUG) {
            $script = file_get_contents ( self::$_plugin_dir . 'js/manage-svg.js' );
        } else {
            $script = file_get_contents ( self::$_plugin_dir. 'dist/manage-svg-min.js' );
        }
        return '<script type="text/javascript"><![CDATA[' . $script . ']]</script>';
    }
    /**
     * Return the content of css file manage-svg
     * ready to insert in post
     * @todo function unused
     * @return string
     */
    private static function svg_style() {
        if (WP_DEBUG) {
            $style = file_get_contents ( self::$_plugin_dir. 'css/manage-svg.css' );
        } else {
            $style = file_get_contents ( self::$_plugin_dir. 'dist/manage-svg.css' );
        }
        return '<style><![CDATA[' . $style . ']]</style>';
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
        if (!preg_match('/.+\.svg$/i', $path) || !file_exists ( $path )) {
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
            	wp_enqueue_script('fmt_svg_js');
            	wp_enqueue_style('fmt_svg_css');
               // $content .= self::svg_style ();
               // $content .= self::svg_script ();
            }
            self::$_count_svg ++;
            if (isset ( $attrs ['class'] ) && !empty($attrs['class'])) {
                $value = $attrs ['class'] == 'fm-right' ? 1 : 0;
                $content .= '<div class="formater-svg ' . $attrs ['class'] . '"  >';
                if (isset($attrs['hide_button']) && !$attrs['hide_button']) {
                	$content .= '<div class="fm-enlarge fa" onclick="formater_switch_svg( this, ' . $value . ')"></div>';
                }
            } else {
                $content .= '<div class="formater-svg">';
            }
            $svg = $svgs->item ( 0 );
            $svg->setAttribute ( 'preserveAspectRatio', 'xMinYMin meet' );
            
            $content .= self::sanitize_output($doc->saveHTML ( $svg )) . '</div>';
       
            return $content;
        }
    }
    public static function sanitize_output($buffer) {
    	
    	$search = array(
    			'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
    			'/[^\S ]+\</s',     // strip whitespaces before tags, except space
    			'/(\s)+/s',         // shorten multiple whitespace sequences
    			'/<!--(.|\s)*?-->/' // Remove HTML comments
    	);
    	
    	$replace = array(
    			'>',
    			'<',
    			'\\1',
    			''
    	);
    	
    	$buffer = preg_replace($search, $replace, $buffer);
    	
    	return $buffer;
    }
    // Wordpress 5 - enqueue scripts for Gutenberg editor
    
    public function gutenberg_enqueue_block_editor_assets() {
    	wp_enqueue_script(
    			'fm-svg-gutenberg-block-js', // Unique handle.
    			Formater_WP_Pack::$url. '/js/svg-blocks.js',
    			array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
    			Formater_WP_Pack::$VERSION
    			);
    }
}
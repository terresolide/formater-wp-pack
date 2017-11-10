<?php
/**
 * Manage svg files
 * use vuejs webcomponent formater-pdf-viewer-vjs
 * @see https://github.com/terresolide/formater-pdf-viewer-vjs
 * @author epointal
 */
if (! defined ( 'ABSPATH' ))
    exit ();

class Fm_pdf_manager
{
    const FORMATER_PDF_VIEWER_VJS_VERSION = '0.1.5';
    public static $_count_pdf = 0;
    public static $_formater_pdf_viewer_vjs_plugin_url = '';
     
    public function __construct(){
         if( is_admin() ){
             /**
              * Add pdf as a supported upload type to Media Gallery
              */
            add_filter ( 'upload_mimes', array( &$this, 'pdf_upload_mimes' ));
            /**
             * filter for gpx in media gallery
             */
            add_filter ( 'post_mime_types', array( &$this, 'pdf_post_mime_types' ));
            /**
             * embed-pdf shortcode instead link when insert pdf in post
             */
            add_filter ( 'media_send_to_editor', array( &$this, 'pdf_media_send_to_editor'), 21, 3 );
         }else{
             /**
              * Register script webcomponent formater-pdf-viewer-vjs
              */
             if (WP_DEBUG) {
                 // use master version
                 self::$_formater_pdf_viewer_vjs_plugin_url= "https://rawgit.com/terresolide/formater-pdf-viewer-vjs/master/dist/formater-pdf-viewer-vjs_" . self::FORMATER_PDF_VIEWER_VJS_VERSION . ".js";
             } else {
                 // use last tag version
                 self::$_formater_pdf_viewer_vjs_plugin_url = "https://cdn.rawgit.com/terresolide/formater-pdf-viewer-vjs/" . self::FORMATER_PDF_VIEWER_VJS_VERSION;
                 self::$_formater_pdf_viewer_vjs_plugin_url.= "/dist/formater-pdf-viewer-vjs_" . self::FORMATER_PDF_VIEWER_VJS_VERSION. ".js";
             }
             add_action ( 'wp_enqueue_scripts', array( &$this, 'formater_register_pdf_script' ));
             /**
              * Add shortcode to write webcomponent <formater-pdf-viewer> instead [embed-pdf]
              */
             add_shortcode ( "embed-pdf", array( &$this, "embed_pdf" ));
         }
     }
    
    
    
    public function pdf_upload_mimes($existing_mimes = array()) {
        if (! isset ( $existing_mimes ['pdf'] )) {
            $existing_mimes ['pdf'] = ' 	application/pdf';
        }
        return $existing_mimes;
    }
    
    
    public function pdf_post_mime_types($post_mime_types) {
        $post_mime_types ['application/pdf'] = array (
                'PDF',
                'Manage pdfs',
                'PDF <span class="count">(%s)</span>' 
        );
        return $post_mime_types;
    }


    public function formater_register_pdf_script() {
        wp_register_script ( 'pdf_vjs', self::$_formater_pdf_viewer_vjs_plugin_url, Array (), null, true );
    }


    public function pdf_media_send_to_editor($html, $id, $attachment) {
        if (isset ( $attachment ['url'] ) && preg_match ( "/\.pdf$/i", $attachment ['url'] )) {
            $title = $attachment ['post_title'];
            $rotate = ' rotate=0';
            if (isset ( $attachment ['rotate'] )) {
                $rotate = ' rotate=' . intVal ( $attachment ['rotate'] );
            }
            
            $filter = '[embed-pdf src=' . $attachment ['url'] . $rotate . ' ]' . $title . '[/embed-pdf]';

            return apply_filters ( 'pdf_override_send_to_editor', $filter, $html, $id, $attachment );
        } else {
            return $html;
        }
    }


    public function embed_pdf($attrs, $html = '') {
        global $_formater_pdf_count;
        $url = $attrs ["src"];
        // var_dump($attrs);
        
        if ($_formater_pdf_count == 0) {
            // load script webcomponent formater-pdf-viewer-vjs
            // only for the first component formater-pdf-viewer
            wp_enqueue_script ( 'pdf_vjs' );
            $_formater_pdf_count ++;
        }
        
        $lang = substr ( get_locale (), 0, 2 );
        $rotate = isset ( $attrs ["rotate"] ) ? intVal ( $attrs ["rotate"] ) : 0;
        return '<div style="clear:both;"></div>
    <formater-pdf-viewer src="' . $url . '" fa="true" lang="' . $lang . '" rotate="' . $rotate . '" ></formater-pdf-viewer>
    <p style="text-align: center"><i class="fa fa-file-pdf-o" style="color:red;"></i> <a href="' . $url . '">' . $html . '</a></p>';
        
        // / @todo when formater-pdf-viewer integration pk
        // return '<formater-pdf-viewer src="' .$url. '"></formater-pdf-viewer>';
    }	

}
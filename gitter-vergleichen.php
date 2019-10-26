<?php
/*
Plugin Name:  Gitter Vergleichen
Plugin URI:   https://www.hundeo.com/
Description:  Plug-in für Gitter Vergleichen
Version:      0.0.1
Author:       Enrico Bachmann
Author URI:   https://enrico-bachmann.de/
License:      Proprietary
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


// 1. customize ACF path
add_filter('acf/settings/path', 'gittervergleichen_acf_settings_path');
 
function gittervergleichen_acf_settings_path( $path ) {
 
    // update path
    $path = plugin_dir_path(__FILE__)  . '/theoptions/';
    
    // return
    return $path;
    
}
 

// 2. customize ACF dir
add_filter('acf/settings/dir', 'gittervergleichen_acf_settings_dir');
 
function gittervergleichen_acf_settings_dir( $dir ) {
 
    // update path

    $dir = plugins_url('/theoptions/', __FILE__);
    
    // return
    return $dir;
    
}
 

// 3. Hide ACF field group menu item
//add_filter('acf/settings/show_admin', '__return_false');


// 4. Include ACF
include_once( plugin_dir_path(__FILE__) . '/theoptions/acf.php' );

//Add Options Page
if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Vergleichen Sie die Rasteroptionen',
		'menu_title'	=> 'Vergleichen Sie die Rasteroptionen',
		'menu_slug' 	=> 'vergleichen-sie-die-rasteroptionen',
		'capability'	=> 'edit_posts',
		'parent_slug'	=> 'edit.php?post_type=comparegrid',
		'redirect'		=> false
	));
}

if ( ! function_exists('gittervergleichen_post_type') ) {

	// Register Custom Post Type
	function gittervergleichen_post_type() {
		if (!empty($the_label = get_field('compare_grid_name', 'option'))) {
			$the_label = get_field('compare_grid_name', 'option');
		} else {
			$the_label = 'gittervergleichen';
		}
		$safe_label = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $the_label)));
		$safe_label = substr($safe_label, 0, 20);

		$labels = array(
			'name'                  => _x( $the_label, 'Post Type General Name', 'astra' ),
			'singular_name'         => _x( $the_label, 'Post Type Singular Name', 'astra' ),
			'menu_name'             => __( $the_label, 'astra' ),
			'name_admin_bar'        => __( $the_label, 'astra' ),
			'archives'              => __( 'Archiv', 'astra' ),
			'attributes'            => __( 'Attribute', 'astra' ),
			'parent_item_colon'     => __( 'Eltern Artikel', 'astra' ),
			'all_items'             => __( 'Alle Elemente', 'astra' ),
			'add_new_item'          => __( 'Neues Element hinzufügen', 'astra' ),
			'add_new'               => __( 'Neue hinzufügen', 'astra' ),
			'new_item'              => __( 'Neuer Gegenstand', 'astra' ),
			'edit_item'             => __( 'Element bearbeiten', 'astra' ),
			'update_item'           => __( 'Artikel aktualisieren', 'astra' ),
			'view_item'             => __( 'Artikel anzeigen', 'astra' ),
			'view_items'            => __( 'Elemente anzeigen', 'astra' ),
			'search_items'          => __( 'Artikel suchen', 'astra' ),
			'not_found'             => __( 'Nicht gefunden', 'astra' ),
			'not_found_in_trash'    => __( 'Nicht im Papierkorb gefunden  ', 'astra' ),
			'featured_image'        => __( 'Ausgewähltes Bild', 'astra' ),
			'set_featured_image'    => __( 'Festgelegtes Bild einstellen', 'astra' ),
			'remove_featured_image' => __( 'Ausgewähltes Bild entfernen', 'astra' ),
			'use_featured_image'    => __( 'Verwenden Sie Featured Image', 'astra' ),
			'insert_into_item'      => __( 'In Artikel einfügen', 'astra' ),
			'uploaded_to_this_item' => __( 'Zu diesem Artikel hochgeladen', 'astra' ),
			'items_list'            => __( 'Artikelliste', 'astra' ),
			'items_list_navigation' => __( 'Artikelliste Navigation', 'astra' ),
			'filter_items_list'     => __( 'Artikelliste filtern', 'astra' ),
		);
		$args = array(
			'label'                 => __( $the_label, 'astra' ),
			'description'           => __( $the_label, 'astra' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail'),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-portfolio',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'       => true,
  			'rest_base'          => 'grids-api',
			"rewrite" => array( 'slug' => $safe_label, "with_front" => true ),
		);
		register_post_type( 'comparegrid', $args );

		add_image_size('compare-image', 200, 200, false, 'comparegrid');
	}

	add_action( 'init', 'gittervergleichen_post_type');
}

// Flushing Permalinks on options update
function chr_acf_save_post( $field ) {
    
    flush_rewrite_rules(true);

    return $field;
    
}

add_action('acf/load_field/name=compare_grid_name', 'chr_acf_save_post');

function chr_acf_save_post_option( $post_id ) {


	if ( $post_id == 'options' ) {

		// check if the repeater field has rows of data
		if( have_rows('special_page_options', 'option') ):

		 	// loop through the rows of data
		    while ( have_rows('special_page_options', 'option') ) : the_row();

		    	$post_title = get_sub_field('special_title', 'option');
		    	$post_associated = post_exists($post_title);

				if ($post_associated) { 

				    if ( ! wp_is_post_revision( $post_associated ) ) {

						$my_post = array(
						  'ID'	          => $post_associated,
						  'post_title'    => $post_title,
						  'post_content'  => get_sub_field('special_description', 'option'),
						  'post_excerpt'  => get_sub_field('raw_url'),
						  'post_type'     => 'page',
						  'post_status'   => 'publish',
						  'page_template' =>  'comparegrid-template.php'
						);				

				        // unhook this function so it doesn't loop infinitely
				        remove_action('acf/save_post', 'chr_acf_save_post_option');
				     
				        // update the post, which calls save_post again
				         $post_associated = wp_update_post($my_post);
				         update_sub_field('the_page_id', $post_associated, 'option');
				 
				        // re-hook this function
				        add_action('acf/save_post', 'chr_acf_save_post_option');
				    }
               				
				} else {
					$my_post = array(
					  'post_title'    => $post_title,
					  'post_content'  => get_sub_field('special_description', 'option'),
					  'post_excerpt'  => get_sub_field('raw_url'),
					  'post_type'     => 'page',
					  'post_status'   => 'publish',
					  'page_template' => 'comparegrid-template.php'
					);			
					$post_associated = wp_insert_post($my_post);							       
					update_sub_field('the_page_id', $post_associated, 'option');
				}

		    endwhile;

		else :

		    // no rows found

		endif;		
	}
    
}

add_action('acf/save_post', 'chr_acf_save_post_option', 20);

function comparegrid_page_template( $template ) {
      if ( is_singular( 'dwqa-question' ) ) {

          $template =  get_stylesheet_directory() . '/' . page-question.php;
      }
      return $template;
}

add_filter( 'page_template', 'comparegrid_page_template' );

//Enable archive template
function get_comparegrid_template( $archive_template ) {
     global $post;

     if ( is_post_type_archive ( 'comparegrid' ) ) {
          $archive_template = dirname( __FILE__ ) . '/comparegrid-archive-template.php';
     }

     return $archive_template;
}

add_filter( 'archive_template', 'get_comparegrid_template' ) ;

//Adds CSS and JS Files
function chr_enqueue_scripts() {
	wp_register_style('chr-style-css', plugins_url('/assets/style.css', __FILE__ ), array(), false, 'all');
	wp_register_script( 'chr-grid-script', plugins_url('/assets/chr-grid-script.js', __FILE__ ) , array('jquery') );
	wp_register_style('chr-select-2-css', plugins_url('/assets/select2.min.css', __FILE__ ), array(), false, 'all');	
	wp_register_script( 'chr-select-2-js', plugins_url('/assets/select2.min.js', __FILE__ ) , array('jquery') );
}

add_action('wp_enqueue_scripts', 'chr_enqueue_scripts');

function save_grid_meta( $post_id, $post, $update ) {

    $post_type = get_post_type($post_id);

    if ( "comparegrid" != $post_type ) return;
    $the_title = get_the_title();
    $the_title = strtoupper($the_title);
    update_field('letter', $the_title[0]);

}
add_action( 'save_post', 'save_grid_meta', 10, 3 );

//Populate Search Fields
function acf_load_search_field_choices( $field ) {

	$field['choices'] = array();

	$field_groups = acf_get_field_groups();

	foreach ( $field_groups as $group ) {

	  $the_fields = get_posts(array(
	    'posts_per_page'   => -1,
	    'post_type'        => 'acf-field',
	    'orderby'          => 'menu_order',
	    'order'            => 'ASC',
	    'suppress_filters' => true,
	    'post_parent'      => $group['ID'],
	    'post_status'      => 'any',
	    'update_post_meta_cache' => false
	  ));
	  foreach ( $the_fields as $field_details ) {
	  	$details = unserialize($field_details->post_content);
	  	if (($details['type'] == 'select') && ($field_details->post_name != 'field_5c1e039ab6fb0')) {
	  		$field['choices'][$field_details->post_name] = $field_details->post_title;
	  	}
	  }
	}

    return $field;
    
}

//Changes Compare Grid Archive Title
add_filter('acf/load_field/name=search_filter_fields', 'acf_load_search_field_choices');

//Populate Compare Fields
function acf_load_compare_field_choices( $field ) {

	$field['choices'] = array();

	$field_groups = acf_get_field_groups();

	foreach ( $field_groups as $group ) {
	  if ($group['location'][0][0]['value'] == 'comparegrid') {
		  $the_fields = get_posts(array(
		    'posts_per_page'   => -1,
		    'post_type'        => 'acf-field',
		    'orderby'          => 'menu_order',
		    'order'            => 'ASC',
		    'suppress_filters' => true,
		    'post_parent'      => $group['ID'],
		    'post_status'      => 'any',
		    'update_post_meta_cache' => false
		  ));
		  foreach ( $the_fields as $field_details ) {
		  	$details = unserialize($field_details->post_content);
		  	if (($field_details->post_name != 'field_5c26305c73d30') && ($field_details->post_name != 'field_5c0bd1cde5537')) {
		  		if (($details['type'] == 'select') || ($details['type'] == 'text'))
		  		$field['choices'][$field_details->post_name] = $field_details->post_title;
		  	}
		  }		  
	  }
	}

    return $field;
    
}

add_filter('acf/load_field/name=compare_fields', 'acf_load_compare_field_choices');


//Change Archive Pgae Title/Description/Image - SEO Framework
add_filter( 'the_seo_framework_generated_description', 'archive_page_description');

function archive_page_description($description) {
	/** 
	 * @link https://developer.wordpress.org/reference/functions/is_post_type_archive/
	 */	
	if (is_post_type_archive('comparegrid')) {
		if (get_field('archive_description', 'option')) {
			return get_field('archive_description', 'option');
		} else {
			return $description;
		}
	} else {
		return $description; 
	}
}

add_filter( 'the_seo_framework_title_from_generation', 'archive_page_title');

function archive_page_title($title) {
	/** 
	 * @link https://developer.wordpress.org/reference/functions/is_post_type_archive/
	 */
	if (is_post_type_archive('comparegrid')) {
		if (get_field('archive_title', 'option')) {
			return get_field('archive_title', 'option');
		} else {
			return $title;
		}
	} else {
		return $title; 
	}
}

define( 'THE_SEO_FRAMEWORK_DISABLE_TRANSIENTS', true );

add_filter('the_seo_framework_og_image_fallback', 'archive_page_image', 10, 2);

function archive_page_image($image, $post_id) {

	if (is_post_type_archive('comparegrid')) {
		if (get_field('archive_image', 'option')) {
			return get_field('archive_image', 'option');
		} else {
			return $image;
		}		
		return $image;
	}

	return $image;
}

//Add the Compare Page
function add_compare_page() {
	$compare_page = array(
	  'post_title'    => get_field('compare_grid_name', 'option') . ' Vergleich',
	  'post_name'     => 'vergleich',
	  'post_content'  => '',
	  'post_type'     => 'page',
	  'post_status'   => 'publish',
	  'page_template' => 'compare-template.php'
	);			
	if(!get_page_by_path('vergleich')) {
		wp_insert_post($compare_page);							       
	}
}
add_action('init', 'add_compare_page');

//Add Range Shortcode
include_once( plugin_dir_path(__FILE__) . '/shortcodes/chr-range.php' );

//Add Grid Loop Shortcode
include_once( plugin_dir_path(__FILE__) . '/shortcodes/chr-grid-loop.php' );

//Add Grid Search Shortcode
include_once( plugin_dir_path(__FILE__) . '/shortcodes/chr-grid-search.php' );

//Add Page Template
include_once( plugin_dir_path(__FILE__) . '/page-template-include.php' );


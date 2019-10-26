<?php

	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	function chr_grid_loop_func() {

		wp_enqueue_script('chr-grid-script');
		wp_enqueue_style('chr-style-css');

		$grid_color = get_field('bereichsfarbe', 'option');
		list($r, $g, $b) = sscanf($range_color, "#%02x%02x%02x");

		$number_of_the_page = get_field('nummer_der_gitterelemente', 'option');
		if (empty($number_of_the_page)) {$number_of_the_page = 10;}

		$args = array(
			'posts_per_page' => $number_of_the_page,
			'post_type'		=> 'comparegrid',
			'post_status'    => 'publish'
		);

		$the_query = new WP_Query( $args ); global $wp; 

		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    	$parts = parse_url($actual_link);
		parse_str($parts['query'], $urlquery);

		if ( isset($urlquery['letter'])) {
			echo '<input id="the-letter-value" type="hidden" value="'. $urlquery['letter'] . '">';
    	}		

		?>
		<style>
			body .check-container {
				color: <?php echo $grid_color; ?>;
			}
			body .check-container input:checked ~ .checkmark {
				background-color: <?php echo $grid_color; ?>;
			}
			body .check-container .checkmark {
				border-color: <?php echo $grid_color; ?>;
			}
			body .breed-letters-filter .breed-letters-filter__letter > a {
				border-color: <?php echo $grid_color; ?>;
				color: <?php echo $grid_color; ?>;
			}
			body .breed-letters-filter__cancel,
			body .breed-letters-filter__cancel:hover {
			    color: <?php echo $grid_color; ?>;
			}
			body .breed-letters-filter .breed-letters-filter__letter > a:hover,
			body .breed-letters-filter .breed-letters-filter__letter > a.active,
			body .grid-load-more,
			body .compare-please {
				background-color: <?php echo $grid_color; ?>;
			}
		</style>
			<div class="chr-grid-search dropdown secondary">
			  <input type="text" placeholder="Suche..." class="chr-input">	
			  <div class="dropdown-content chr-dropdown">
			    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			    	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>	
				<?php endwhile; ?>
				<?php endif; ?>	
			  </div>
			</div>			
		<?php if( $the_query->have_posts() ): ?>	
			<div class="the-grid">
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<div data-inner-value="<?php echo get_the_ID(); ?>" class="grid-item" style="border-bottom: 5px solid <?php echo $grid_color; ?>">
					<a href="<?php the_permalink(); ?>">
						<?php echo get_the_post_thumbnail($post->ID, 'post-thumbnail'); ?>
						<h3 style="color: <?php echo $grid_color; ?>"><?php the_title(); ?></h3>
					</a>
					<div class="grid-description"><?php 
						$str = get_field('einfuhrung');
						if (strlen($str) > 80) {
							$str = substr($str, 0, 80) . '...';
						}
						echo $str;					 
					?></div>
					<a class="grid-read-more" href="<?php the_permalink(); ?>" style="color: <?php echo $grid_color; ?>">Mehr erfahren »</a>
					<label class="check-container">RASSE VERGLEICHEN »
					  <input type="checkbox">
					  <span class="checkmark"></span>
					</label>						
				</div>
			<?php endwhile; ?>
			</div>
		<?php else: ?>
			<div class="no-items">Keine Artikel zum Anzeigen</div>	
		<?php endif; ?>
		<?php if ($the_query->max_num_pages > 1) { ?>
			<button class="grid-load-more">Mehr laden</button>
		<?php } ?>
		<?php wp_reset_query();	 // Restore global post data stomped by the_post(). ?>
			<div class="breed-letters-filter">
		    <div class="breed-letters-filter__items">
		    		<?php 
		    			$the_letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
		    			foreach ($the_letters as &$the_letter_value) { ?>
		    				<div class="breed-letters-filter__letter">
			            		<?php 
			            			$pos = strpos($actual_link, 'letter=');
			            			if ($pos !== false) {
			            				$final_url = $actual_link;
			            				$final_url[$pos + 7] = $the_letter_value;
			            			} else {
			            				$first_pos = strpos($actual_link, '?');
			            				if ($first_pos !== false) {
			            					$final_url = $actual_link . "&letter=" . $the_letter_value;
			            				} else {
			            					$final_url = $actual_link . "?letter=" . $the_letter_value;
			            				}
			            			}
			            		?>
			                    <a data-letter-value="<?php echo $the_letter_value; ?>" href="<?php echo $final_url; ?>">
			                        <span><?php echo $the_letter_value; ?></span>
			                    </a>		            		
		            		</div>
						<?php }
		    		?>
		    </div>

		    <div class="breed-letters-filter__action">
        		<?php 
        		    unset($urlquery['letter']);
        		    if (empty($urlquery)) {
        		     	$final_url = home_url($wp->request) . '/';
        		    } else {
        		    	$final_url = home_url($wp->request) . '/?';
        		    }
        			$the_url_counter = 0;
					foreach ($urlquery as $key => $value) {
						if ($the_url_counter == 0) {
							$final_url .= $key . '=' . $value;
						} else {
							$final_url .= '&' . $key . '=' . $value;
						}
						$the_url_counter++;
					}		            			
        		?>		    	
		        <a href="<?php echo $final_url; ?>" class="breed-letters-filter__cancel">Buchstabenfilter löschen</a>
		    </div>
		</div>		
		<?php wp_localize_script( 'chr-grid-script', 'chr_grid_parameters', array(
			'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
			'posts' => json_encode( $the_query->query_vars ), // everything about your loop is here
			'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
			'max_page' => $the_query->max_num_pages,
			'nonce' => wp_create_nonce('chr_nounce_grid')
		) );

	}
	add_shortcode('chr_grid_loop', 'chr_grid_loop_func');

	function chr_pre_get_posts($query) {
	
	// do not modify queries in the admin
	if( is_admin() ) {
		
		return $query;
		
	}

	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	if (is_page_template('comparegrid-template.php')) {
		if (isset($query->queried_object->post_excerpt)) {
			$actual_link = $query->queried_object->post_excerpt;
		}
	}

	if (!$query->is_main_query()) {
		// only modify queries for 'Compare Grid' post type

		if((isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'comparegrid') && (is_page_template('comparegrid-template.php') == false)) {

	    	$parts = parse_url($actual_link);
			parse_str($parts['query'], $urlquery);
			$outer_array = [];
			$outer_array['relation'] = 'AND';
			foreach ($urlquery as $key => $value1) {
				if ( isset($urlquery[$key]) ) {	
					$the_values = explode("%7C", $urlquery[$key]);
					if (sizeof($the_values) == 1) {
						$outer_array[] = array(
							'key' => $key,
							'value' => $value1,
							'compare' => '='
						); 							
					} else {
						$inner_array['relation'] = 'OR';
						foreach ($the_values as $value2) {
							$inner_array[] = array(
								'key' => $key,
								'value' => $value2,
								'compare' => '='
							);
					    }
					    array_push($outer_array, $inner_array);	
					}	
		    	}			
			}
			$query->set('meta_query', $outer_array);
		}		

	}	
	// return
	return $query;

}

add_action('pre_get_posts', 'chr_pre_get_posts');

function chr_loadmore_ajax_handler(){
 
	check_ajax_referer('chr_nounce_grid');

	$grid_color = get_field('bereichsfarbe', 'option');

	// prepare our arguments for the query
	$args = json_decode( stripslashes( $_POST['query'] ), true );
	$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	$args['post_status'] = 'publish';
	$arge['post_type'] = 'comparegrid';

	$the_query = new WP_Query( $args ); ?>

	<?php if( $the_query->have_posts() ): ?>
		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
			<div data-inner-value="<?php echo get_the_ID(); ?>" class="grid-item"  style="border-bottom: 5px solid <?php echo $grid_color; ?>">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail($post->ID, 'compare-image'); ?>
					<h3 style="color: <?php echo $grid_color; ?>"><?php the_title(); ?></h3>
				</a>
				<div class="grid-description"><?php
						$str = get_field('einfuhrung');
						if (strlen($str) > 220) {
							$str = substr($str, 0, 220) . '...';
						}
						echo $str;					
				?></div>
				<a class="grid-read-more" href="<?php the_permalink(); ?>" style="color: <?php echo $grid_color; ?>">Mehr erfahren »</a>
				<label class="check-container">RASSE VERGLEICHEN »
				  <input type="checkbox">
				  <span class="checkmark"></span>
				</label>				
			</div>
		<?php endwhile; ?>
	<?php endif; 
	die; 
}
 
 
 
add_action('wp_ajax_loadmore', 'chr_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore', 'chr_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}

?>
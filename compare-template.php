<?php 
	get_header(); 	
	wp_enqueue_script('chr-grid-script');
	wp_enqueue_style('chr-style-css');
	wp_enqueue_script('chr-select-2-js');
	wp_enqueue_style('chr-select-2-css');
?>

<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php
   $the_pure_url = esc_url_raw(remove_query_arg('compareitems'));
   $_SERVER['REQUEST_URI'] = $the_pure_url;
   $args = array(  
       'post_type' => 'comparegrid',
       'post_status' => 'publish',
       'posts_per_page' => -1,
       'orderby' => 'title',
       'order' => 'ASC'
   );
   wp_reset_query();
   $loop = new WP_Query( $args );
?>

<?php 
	$the_compare_path = $_GET['compareitems'];
	$the_compare_list = explode("|", $the_compare_path);
	$css_class_number = sizeof($the_compare_list);
	$the_compare_fields = get_field('compare_fields', 'option');
	$range_color = get_field('bereichsfarbe', 'option');
	list($rr, $gg, $bb) = sscanf($range_color, "#%02x%02x%02x");	
	$the_count_limiter = 1;
?>
	<style>
		body .select2-container--default .select2-results__option--highlighted[aria-selected] {
		  background-color: <?php echo $range_color; ?>;
		}

		body .select2-container--default .select2-search--dropdown .select2-search__field:focus {
		    outline: 1px solid <?php echo $range_color; ?>;
		}

		body .compare-go-to {
			background-color: <?php echo $range_color; ?>; 
			border-color: <?php echo $range_color; ?>;			
		}

		body .compare-go-to:hover {
			background-color: white; 
			color: <?php echo $range_color; ?>;
			border-color: <?php echo $range_color; ?>;
		}
	</style>
	<div id="primary" <?php astra_primary_class(); ?>>
		<h1><?php the_field('compare_grid_name', 'option'); ?> Vergleich</h1>	
		<div class="compare-grid-wrapper-description"><?php the_content(get_the_ID()); ?></div>	
		<div class="compare-grid-wrapper items-<?php echo $css_class_number; ?>">
			<?php if (!empty($the_compare_list[0])) : ?>
				<?php foreach ($the_compare_list as $the_compare_list_item) : ?>
					<?php if ($the_count_limiter > 5) {break;} ?>				
					<div data-value-id="<?php echo $the_compare_list_item; ?>" class="compare-item">
						<div class="the-image"><div class="close-icon" style="background-image: url(<?php echo plugins_url('/assets/close-icon.png', __FILE__ ) ?>);"></div><img class="compare-image" src="<?php echo get_the_post_thumbnail_url($the_compare_list_item, 'full'); ?>" alt="<?php echo get_the_title($the_compare_list_item); ?>"></div>	
						<h2 class="item-title"><a href="<?php the_permalink($the_compare_list_item); ?>"><?php echo get_the_title($the_compare_list_item); ?></a></h2>
							<select class="items-select" name="items">						
								<?php if ( $loop->have_posts() ) : while ( $loop->have_posts() ) : $loop->the_post(); ?>
									<?php $compare_search_result = array_search(get_the_ID(),$the_compare_list); ?>
									<option <?php if (($compare_search_result !== false) && (get_the_ID() != $the_compare_list_item)) {echo 'disabled="disabled';} if (get_the_ID() == $the_compare_list_item) {echo ' selected';} ?> value="<?php echo get_the_ID(); ?>"><?php the_title(); ?></option>
								<?php endwhile; ?>
								<?php endif; wp_reset_postdata(); ?>
							</select>													
						<?php 
							foreach($the_compare_fields as $compare_field):
								$the_field = get_field_object($compare_field, $the_compare_list_item);
						?>
							<?php if($the_field['type'] == 'select') : ?>
								<?php if ($the_field['name'] == 'gruppe'): ?>
									<div class="the-label"><?php echo $the_field['label']; ?></div>
									<div class="the-value" style="background-color: rgba(<?php echo $rr; ?>, <?php echo $gg; ?>, <?php echo $bb; ?>, 0.1);"><?php echo $the_field['value']; ?></div>								
								<?php else: ?>
									<div class="the-label"><?php echo $the_field['label']; ?></div>
									<?php echo do_shortcode('[chr_range id="' . $the_compare_list_item . '" field="' . $the_field['name'] . '"]'); ?>
							<?php endif; ?>
							<?php else : ?>
								<div class="the-label"><?php echo $the_field['label']; ?></div>
								<div class="the-value" style="background-color: rgba(<?php echo $rr; ?>, <?php echo $gg; ?>, <?php echo $bb; ?>, 0.1);"><?php echo $the_field['value']; ?></div>
							<?php endif; ?>
						<?php endforeach; ?>
						<a class="button compare-go-to" href="<?php the_permalink($the_compare_list_item); ?>">MEHR DETAILS</a>
					</div>				
				<?php $the_count_limiter++; endforeach; ?>
			<?php endif; ?>				
			<?php if ($the_count_limiter < 6) : ?>
				<div class="next-item">
					<div class="the-image" style="background-color: rgba(<?php echo $rr; ?>, <?php echo $gg; ?>, <?php echo $bb; ?>, 0.1);"><img src="<?php echo plugins_url('/assets/question-icon.png', __FILE__ ); ?>" alt="Compare Question Icon"></div>
					<h2>WÄHLE AUS</h2>
					<select class="items-select" name="items">						
						<?php if ( $loop->have_posts() ) : while ( $loop->have_posts() ) : $loop->the_post(); ?>
							<?php $compare_search_result = array_search(get_the_ID(),$the_compare_list); ?>							
							<option <?php if ($compare_search_result !== false) {echo 'disabled="disabled';} ?> value="<?php echo get_the_ID(); ?>"><?php the_title(); ?></option>
						<?php endwhile; ?>
						<?php endif; wp_reset_postdata(); ?>
					</select>						
				</div>
			<?php endif; ?>			
		</div>
		<input class="the-pure-url" type="hidden" value="<?php echo home_url($the_pure_url); ?>"> 
	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>

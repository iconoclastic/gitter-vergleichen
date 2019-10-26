<?php

get_header(); ?>

<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

	<div id="primary" <?php astra_primary_class(); ?>>

    <?php
    $the_counter = 0;
    // TO SHOW THE PAGE CONTENTS
    while ( have_posts() ) : the_post(); ?> <!--Because the_content() works only inside a WP Loop -->
    	<?php if ($the_counter == 0) { ?>
			<div class="comparegrid-content">
				<div class="comparegrid-sidebar">
					<?php include_once( plugin_dir_path(__FILE__) . '/templates/chr-grid-search-template.php' ); ?>
				</div>
				<div class="comparegrid-main">
					<h1 class="comparegrid-title"><?php the_title(); ?></h1>
					<div class="comparegrid-description">
						<?php the_content(); ?> <!-- Page Content -->
					</div>
					<?php include_once( plugin_dir_path(__FILE__) . '/templates/chr-grid-loop-template.php' ); ?> 
				</div>
			</div>  
    	<?php } else { break; } $the_counter++; ?>
    <?php
    endwhile; //resetting the page loop
    wp_reset_postdata();
    ?>	

	<div class="compare-wrapper">
		<span><small class="the-counter">0</small> / <small>5</small></span>
		<button data-path="<?php echo home_url('/vergleich/'); ?>" class="compare-please">VERGLEICH STARTEN</button>
	</div>

	<?php

	// check if the repeater field has rows of data
	if( have_rows('special_page_options', 'option') ): ?>

	    <div class="compare-grid-static-links">
	    	<ul> 
			 	<?php // loop through the rows of data
			    while ( have_rows('special_page_options', 'option') ) : the_row(); ?>
				
					<li><a href="<?php echo home_url('/' . sanitize_title(get_sub_field('special_title', 'option'))); ?>"><?php the_sub_field('special_title', 'option'); ?> »</a></li>

			    <?php endwhile;	?>
	    	</ul>
	    </div>

	<?php else :

	    // no rows found

	endif;

	wp_reset_query();

	?> 

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>

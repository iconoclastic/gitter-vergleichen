<?php get_header(); ?>

<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

	<div id="primary" <?php astra_primary_class(); ?>>

		<div class="comparegrid-content">
			<div class="comparegrid-sidebar">
				<?php do_shortcode('[chr_grid_search]'); ?>
			</div>
			<div class="comparegrid-main">
				<h1 class="comparegrid-title"><?php the_field('archive_title', 'option'); ?></h1>
				<div class="comparegrid-description">
					<img src="<?php the_field('archive_image', 'option'); ?>" alt="<?php the_field('archive_title', 'option'); ?>" class="arvhive-description">
					<p><?php the_field('archive_description', 'option'); ?></p>					
				</div>
				<?php do_shortcode('[chr_grid_loop]'); ?>
			</div>
		</div>

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

	wp_reset_postdata();

	?> 

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>

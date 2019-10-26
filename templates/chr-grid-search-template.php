<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

wp_enqueue_style('chr-style-css'); 

// check if the repeater field has rows of data
if( have_rows('special_page_options', 'option') ):

 	// loop through the rows of data
    while ( have_rows('special_page_options', 'option') ) : the_row();

		//echo get_sub_field('raw_url', 'option') . '<br>';
    	$the_page_id = get_sub_field('the_page_id', 'option');
    	if ($the_page_id == get_the_ID()) {
			$actual_link = get_sub_field('raw_url', 'option');
    	}

    endwhile;

else :

    // no rows found

endif;		

$parts = parse_url($actual_link);
parse_str($parts['query'], $urlquery);
$grid_color = get_field('bereichsfarbe', 'option');
global $wp;

wp_localize_script( 'chr-grid-script', 'chr_urlquery_parameters', array(
'urlQuery' => $urlquery,
'actualLink' => $parts['scheme'] . '://' . $parts['host'] . $parts['path']
) );

wp_enqueue_script('chr-grid-script');

?>

<style>
	body .chr-search-sidebar .accordion,
	body .chr-search-sidebar .clear-all {
		color: <?php echo $grid_color; ?>;
	}
	body .chr-search-sidebar .panel,
	body .chr-grid-search.dropdown .dropdown-content,
	body .chr-grid-search .chr-input {
		border-color: <?php echo $grid_color; ?>;
	}
	body .chr-search-sidebar .submit-filter {
		background-color: <?php echo $grid_color; ?>;
	}
</style>
<div class="chr-search-sidebar">
	<?php 
		$allgridposts = new WP_Query(array(
		    'post_type' => 'comparegrid',
		    'post_status' => 'publish'
		)); 
	?>

		<div class="chr-grid-search dropdown">
		  <input type="text" placeholder="Suche..." class="chr-input">	
		  <div class="dropdown-content chr-dropdown">
		    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		    	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>	
			<?php endwhile; ?>
			<?php endif; ?>	
		  </div>
		</div>

	<?php wp_reset_query();	?>	
	<?php 
		$the_search_fields = get_field('search_filter_fields', 'option');
		foreach($the_search_fields as $search_field):
			$the_field = get_field_object($search_field);
	?>

		<button class="accordion"><?php echo $the_field['label']; ?></button>
		<div class="panel">
			<?php
				echo '<div class="filed-value" style="display:none;">' . $the_field['name'] . '</div>';
				if( $the_field['choices'] ) {
					foreach( $the_field['choices'] as $value => $label ) { ?>
						<label class="check-container"><?php echo $label ?>
						  <input type="checkbox">
						  <span class="checkmark"></span>
						  <small style="display: none;"><?php echo $value ?></small>
						</label>
					<?php }
				} ?>
		</div>
	<?php endforeach; ?>
		
	<a href="" class="submit-filter button">SUCHEN »</a>
	<a href="#" class="clear-all">Suchfilter löschen</a>
	<input type="hidden" class="url-queries">
</div>	
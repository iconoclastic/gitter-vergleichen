<?php

	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	function chr_range_func($atts) {
		wp_enqueue_style('chr-style-css');
		$range_color = get_field('bereichsfarbe', 'option');
		list($r, $g, $b) = sscanf($range_color, "#%02x%02x%02x");
		$a = shortcode_atts( array(
			'field' => '',
			'id'    => ''
		), $atts );
		$the_result = '';
		$the_field = '';
		if ($a['id'] == '') {
			$the_field = get_field_object($a['field'], $post->ID);
		} else {
			$the_field = get_field_object($a['field'], $a['id']);
		}
		if( $the_field['choices'] ) {
			$the_result = '<div class="chr-the-range"><div class="chr-range-list" style="background-color: rgba(' . $r . ',' . $g . ',' . $b . ',0.1)">';
			$the_selected = 0;
			$the_progress_counter = 1;
			foreach( $the_field['choices'] as $value => $label ) {
				if ($the_field['value'] == $value) {$the_selected = $the_progress_counter;}
				$the_result .= '<div>';
				$the_result .= $label;
				$the_result .= '</div>';
				$the_progress_counter++;
			}
			$the_result .= '<div class="progress" style="width:' . ($the_selected / $the_progress_counter ) * 100 . '%; background: linear-gradient(to right, rgba(' . $r . ',' . $g . ',' . $b . ',0.5) 0% ,rgba(' . $r . ',' . $g . ',' . $b . ',1) 100%);"></div>';
			$the_result .= '</div>';
			$the_result .= '</div>';
		}
		return $the_result;
	}
	add_shortcode('chr_range', 'chr_range_func');

?>
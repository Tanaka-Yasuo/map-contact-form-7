<?php

add_filter(
	'map_meta_cap',
	function( $caps, $cap, $user_id, $args ) {
		$meta_caps = array(
			'map_wpcf7_read' => MAP_WPCF7_ADMIN_READ_CAPABILITY,
			'map_wpcf7_delete' => MAP_WPCF7_ADMIN_READ_WRITE_CAPABILITY,
		);
		
		$caps = array_diff( $caps, array_keys( $meta_caps ) );
		
		if ( isset( $meta_caps[$cap] ) ) {
			$caps[] = $meta_caps[$cap];
		}
		
		return $caps;
	}, 10, 4 );


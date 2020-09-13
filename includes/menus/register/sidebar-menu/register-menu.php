<?php
function TruFetcherRegisterNavMenus() {
	register_nav_menus(
		array(
			'sidebar-menu' => __( 'Sidebar Menu' ),
			'extra-menu' => __( 'Extra Menu' )
		)
	);
}
add_action( 'admin_init', "TruFetcherRegisterNavMenus" );
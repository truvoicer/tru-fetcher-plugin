<?php
function TruFetcherRegisterNavMenus() {
	register_nav_menus(
		[
			'nav-menu' => __( 'Extra Menu' ),
			'mobile-menu' => __( 'Sidebar Menu' ),
			'auth-menu' => __( 'Auth Menu' ),
		]
	);
}
add_action( 'admin_init', "TruFetcherRegisterNavMenus" );
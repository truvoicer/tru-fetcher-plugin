<?php
function TruFetcherRegisterNavMenus() {
	register_nav_menus(
		[
			'nav-menu' => __( 'Extra Menu' ),
			'mobile-menu' => __( 'Sidebar Menu' ),
		]
	);
}
add_action( 'admin_init', "TruFetcherRegisterNavMenus" );
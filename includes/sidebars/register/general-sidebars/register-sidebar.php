<?php
add_action( 'admin_init', "tru_fetcher_register_sidebars" );
function tru_fetcher_register_sidebars() {
    $leftSidebar = array(
        'name'          =>  __( 'Left Sidebar' ),
        'id'            => "left-sidebar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $leftSidebar );

    $rightSidebar = array(
        'name'          =>  __( 'Right Sidebar' ),
        'id'            => "right-sidebar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $rightSidebar );

    $topBar = array(
        'name'          =>  __( 'Top Bar' ),
        'id'            => "top-bar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $topBar );

    $footer = array(
        'name'          =>  __( 'Footer' ),
        'id'            => "footer",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $footer );
}
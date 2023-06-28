<?php
add_action( 'widgets_init', "tru_fetcher_register_sidebars" );
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

    $navBar = array(
        'name'          =>  __( 'Nav Bar' ),
        'id'            => "nav-bar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
        'show_in_rest' => true,
    );
    register_sidebar( $navBar );

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

    $blogSidebar = array(
        'name'          =>  __( 'Blog Sidebar' ),
        'id'            => "blog-sidebar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $blogSidebar );

    $feedsSidebar = array(
        'name'          =>  __( 'Feeds Sidebar' ),
        'id'            => "feeds-sidebar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $feedsSidebar );

    $accountAreaSidebar = array(
        'name'          =>  __( 'Account Area Sidebar' ),
        'id'            => "account-area-sidebar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $accountAreaSidebar );

    $comparisonSidebar = array(
        'name'          =>  __( 'Comparisons Sidebar' ),
        'id'            => "comparisons-sidebar",
        'description'   => '',
        'class'         => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => "</li>\n",
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => "</h2>\n",
    );
    register_sidebar( $comparisonSidebar );
}

<?php
if(!class_exists('Tru_Fetcher_Social_Media_Widget')) {

    class Tru_Fetcher_Social_Media_Widget extends WP_Widget {

        /**
         * Sets up the widgets name etc
         */
        public function __construct() {
            $widget_ops = array(
                'classname' => 'social_media_widget',
                'description' => 'Social media widget',
            );
            parent::__construct( 'social_media_widget', 'Social Media Widget', $widget_ops );
        }

        /**
         * Outputs the content of the widget
         *
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance ) {
            // outputs the content of the widget
        }

        /**
         * Outputs the options form on admin
         *
         * @param array $instance The widget options
         */
        public function form( $instance ) {
            // outputs the options form on admin
        }

        /**
         * Processing widget options on save
         *
         * @param array $new_instance The new options
         * @param array $old_instance The previous options
         *
         * @return array
         */
        public function update( $new_instance, $old_instance ) {
            // processes widget options to be saved
        }

    }

}

/**
 * Register our CTA Widget
 */
function register_tru_fetcher_social_media_widget()
{
    register_widget( 'Tru_Fetcher_Social_Media_Widget' );
}
add_action( 'widgets_init', 'register_tru_fetcher_social_media_widget' );

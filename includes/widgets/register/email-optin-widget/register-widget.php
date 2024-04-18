<?php

use TruFetcher\Includes\Widgets\Tru_Fetcher_Widgets;

if(!class_exists('Tru_Fetcher_Email_Optin_Widget')) {

    class Tru_Fetcher_Email_Optin_Widget extends WP_Widget {

        private Tru_Fetcher_Widgets $widgets;
        /**
         * Sets up the widgets name etc
         */
        public function __construct() {
            $widget_ops = array(
                'classname' => 'email_optin_widget',
                'description' => 'Email Optin Widget',
            );
            parent::__construct( 'email_optin_widget', 'Email Optin Widget', $widget_ops );
            $this->widgets = new Tru_Fetcher_Widgets();
            $this->widgets->setWidget($this);
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
            $this->widgets->renderWidgetTextInput('title', 'Title', $instance);
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
function register_tru_fetcher_email_optin_widget()
{
    register_widget( 'Tru_Fetcher_Email_Optin_Widget' );
}
add_action( 'widgets_init', 'register_tru_fetcher_email_optin_widget' );

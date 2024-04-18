<?php

use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Listings;
if(!class_exists('Tru_Fetcher_Listings_Widget')) {

    class Tru_Fetcher_Listings_Widget extends WP_Widget {

        /**
         * Sets up the widgets name etc
         */
        public function __construct() {
            $widget_ops = array(
                'classname' => 'listings_widget',
                'description' => 'Listings widget',
            );
            parent::__construct( 'listings_widget', 'Listings Widget', $widget_ops );
        }

        /**
         * Outputs the content of the widget
         *
         * @param array $args
         * @param array $instance
         */
        public function widget( $args, $instance ) {
            extract( $args );
            $listingsHelpers = new Tru_Fetcher_Api_Helpers_Listings();
            $listing = $listingsHelpers->getListingsRepository()->findById((int)$instance['listing']);

            $content = '';
            if ( ! empty( $listing['name'] ) ) {
                $content .= "<h4>Selected listing: </h4>";
                $content .= "<ul><li>id: {$listing['id']}</li><li>Name: {$listing['name']}</li></ul>";
            } else {
                $content .= "<p>No listing selected/found</p>";
            }
            echo $before_widget;
            echo $before_title . apply_filters( 'widget_title', 'Listings Widget' ) . $after_title;
            echo $content;
            echo $after_widget;
        }

        /**
         * Outputs the options form on admin
         *
         * @param array $instance The widget options
         */
        public function form( $instance ) {
            $listingsHelpers = new Tru_Fetcher_Api_Helpers_Listings();
            $listings = $listingsHelpers->getListingsRepository()->findListings();
            ?>
            <p>
                <label
                    for="<?php echo $this->get_field_name( 'listing' ); ?>">
                    <?php _e( 'Select Listing:' ); ?>
                </label>
                <select
                    class="widefat"
                    id="<?php echo $this->get_field_id( 'listing' ); ?>"
                    name="<?php echo $this->get_field_name( 'listing' ); ?>"
                >
                    <?php
                    foreach ($listings as $listing) {
                        $selected = ($listing['id'] == $instance['listing']) ? 'selected' : '';
                        echo "<option value='{$listing['id']}' $selected>{$listing['name']}</option>";
                    }
                    ?>
                </select>
            </p>
            <?php
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
            $instance          = array();
            $instance['listing'] = ( ! empty( $new_instance['listing'] ) ) ?  $new_instance['listing'] : null;
            return $instance;
        }

    }

}

/**
 * Register our CTA Widget
 */
function register_listings_widget()
{
    register_widget( 'Tru_Fetcher_Listings_Widget' );
}
add_action( 'widgets_init', 'register_listings_widget' );

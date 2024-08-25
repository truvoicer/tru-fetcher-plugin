<?php

namespace TruFetcher\Includes\Widgets;


use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Listings;

/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Widgets
{
    public const WIDGETS = [];

    public function init()
    {
        add_action('widgets_init', [$this, 'registerWidgets']);
    }

    public function registerWidgets()
    {
        foreach (self::WIDGETS as $widget) {
            $widgetInstance = new $widget();
            register_widget($widgetInstance);
        }
    }

}

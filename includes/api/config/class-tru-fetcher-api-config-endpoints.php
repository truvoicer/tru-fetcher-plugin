<?php
namespace TruFetcher\Includes\Api\Config;

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
class Tru_Fetcher_Api_Config_Endpoints {
    public const ENDPOINTS = [
        'user_profile' => [
            'update' => '/user/profile/update'
        ],
        'general' => [
            'skills' => '/general/skills'
        ],
    ];
}

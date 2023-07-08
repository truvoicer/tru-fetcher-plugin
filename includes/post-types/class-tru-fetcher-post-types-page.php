<?php

namespace TruFetcher\Includes\PostTypes;

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
class Tru_Fetcher_Post_Types_Page extends Tru_Fetcher_Post_Types_Base
{
    public const NAME = 'page';
    public const ID_IDENTIFIER = 'page_id';
    protected string $idIdentifier = self::ID_IDENTIFIER;
    protected string $name = self::NAME;

}

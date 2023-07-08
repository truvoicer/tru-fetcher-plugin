<?php
namespace TruFetcher\Includes\Taxonomy;

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
class Tru_Fetcher_Taxonomy_Category extends Tru_Fetcher_Taxonomy_Base
{
    public const NAME = 'category';
    public const ID_IDENTIFIER = 'category_id';
    protected string $name = self::NAME;
    protected string $idIdentifier = self::ID_IDENTIFIER;

}

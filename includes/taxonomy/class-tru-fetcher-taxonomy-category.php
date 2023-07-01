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
class Tru_Fetcher_Taxonomy_Category extends Tru_Fetcher_Taxonomy
{
    public const NAME = 'category';
    protected string $name = self::NAME;

}

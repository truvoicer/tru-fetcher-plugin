<?php

namespace TruFetcher\Includes\Admin\AdminTables;
use TruFetcher\Includes\Admin\AdminTables\Builders\Tru_Fetcher_Admin_Table_Builder;

require_once(ABSPATH . '/wp-includes/pluggable.php');
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
class Tru_Fetcher_Admin_Tables
{
    protected Tru_Fetcher_Admin_Table_Builder $adminTableBuilder;

    protected array $columnData = [];

    public function __construct()
    {
        $this->columnData = [
            [
                "name" => "featured_post",
                "label" => "Featured Post",
                "metaKey" => "featured_post",
                "hasQuickEdit" => true,
                "quickEditFieldType" => "checkbox",
                "columnContent" => function ($column_name, $id) {
                    $value = get_post_meta($id, 'featured_post', true);
                    if ($value) {
                        echo '<span class="dashicons dashicons-saved"></span>';
                    }
                    echo '<input type="hidden" name="featured_post_value" value="' . $value . '" />';
                }
            ]
        ];
        $this->loadDependencies();
    }

    public function loadDependencies()
    {
        $this->adminTableBuilder = new Tru_Fetcher_Admin_Table_Builder();
    }

    public function addQuickEditTableColumns()
    {
        $this->adminTableBuilder->setColumns($this->columnData);
        $this->adminTableBuilder->buildColumns();

        if (
            array_search(true, array_column($this->columnData, "hasQuickEdit")) !== false
        ) {
            $this->adminTableBuilder->buildQuickEditFields();
        }

    }

}

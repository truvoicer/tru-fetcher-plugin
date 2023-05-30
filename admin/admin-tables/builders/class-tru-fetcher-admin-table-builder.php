<?php

namespace TruFetcher\Includes\Admin\AdminTables\Builders;

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
class Tru_Fetcher_Admin_Table_Builder
{

    private array $columns = [];
    protected $trNonceAction = 'tr_admin_table_nonce';
    protected $trNonce;

    public function buildColumns()
    {
        add_filter('manage_post_posts_columns', [$this, 'addCustomColumns']);;
        add_action('manage_posts_custom_column', [$this, 'populateCustomColumns'], 10, 2);
    }

    function addCustomColumns($column_array)
    {
        foreach ($this->columns as $column) {
            $column_array[$column["name"]] = $column["label"];
        }
        return $column_array;
    }

    public function populateCustomColumns($column_name, $id)
    {
        foreach ($this->columns as $column) {
            call_user_func_array($column["columnContent"], [$column_name, $id]);
        }
    }

    public function buildQuickEditFields()
    {
        $this->trNonce = wp_create_nonce($this->trNonceAction);
        add_action('quick_edit_custom_box', [$this, 'addQuickEditFields'], 10, 2);
        add_action('save_post', [$this, 'quickEditSave']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueQuickEditPopulation']);
    }

    public function addQuickEditFields($column_name, $post_type)
    {
        foreach ($this->columns as $column):
            switch ($column["quickEditFieldType"]) {
                case "checkbox":
                    echo "
                    <label class='alignleft'>
                        <input type='checkbox' name='{$column["name"]}'>
                        <span class='checkbox-title'>{$column["label"]}</span>
                    </label>
				";
                    break;
                default:
                    break;
            }
            echo wp_nonce_field($this->trNonceAction, $this->trNonceAction);
            echo '</div></div></fieldset>';
        endforeach;
    }

    public function quickEditSave($post_id)
    {
        // check user capabilities
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // check nonce
        if (!array_key_exists($this->trNonceAction, $_POST)) {
            return;
        }

        // check nonce
        if (!wp_verify_nonce($_POST[$this->trNonceAction], $this->trNonceAction)) {
            return;
        }

        foreach ($this->columns as $column):

            switch ($column["quickEditFieldType"]):
                case "checkbox":
                    update_post_meta($post_id, $column["name"], (
                        isset($_POST[$column["name"]]) && $_POST[$column["name"]] === "on"
                    ));
                    break;
                default:
                    break;
            endswitch;
        endforeach;
    }

    public function enqueueQuickEditPopulation($pagehook)
    {
        // do nothing if we are not on the target pages
        if ('edit.php' != $pagehook) {
            return;
        }
        wp_localize_script(TRU_FETCHER_PLUGIN_NAME, 'tr_news_app_admin_table_columns', $this->columns);
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }
}

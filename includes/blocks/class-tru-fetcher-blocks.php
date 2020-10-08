<?php

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
class Tru_Fetcher_Blocks extends Tru_Fetcher_Base {

    const REPLACEABLE_POST_TYPES = [
        "fetcher_items_lists" => "items_data",
        "filter_lists" => "list_items"
    ];

    public function blocks_init() {
        $this->registerBlocks();
    }

    public function registerBlocks()
    {
		$this->directoryIncludes( 'blocks/register-blocks', 'acf-register.php' );
    }

    public function getBlockData($block) {
        acf_setup_meta( $block['data'], $block['id'], true );
        $fields = get_fields();
        if (!$fields) {
            return false;
        }
        return $this->replacePostTypes($fields);
    }

    public function getBlockDataJson($data) {
        $dataJson = json_encode($data);
        if (!$dataJson) {
            return false;
        }
        return htmlentities($dataJson, ENT_QUOTES, 'UTF-8');
    }

    public function replacePostTypes($fields) {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $fields[$key] = $this->replacePostTypes($field);
            }
            else if (!$field instanceof WP_Post) {
                $fields[$key] = $field;
            }
            else if (array_key_exists($field->post_type, self::REPLACEABLE_POST_TYPES)) {
                $getFields = get_fields($field->ID);
                if ($getFields && isset($getFields[self::REPLACEABLE_POST_TYPES[$field->post_type]])) {
                    $fields[$key] = $getFields[self::REPLACEABLE_POST_TYPES[$field->post_type]];
                } else {
                    $fields[$key] = $field;
                }
            }

        }
        return $fields;
    }

    private function directoryIncludes( $pathName, $fileName ) {
        $dir = new DirectoryIterator( plugin_dir_path( dirname( __FILE__ ) ) . $pathName );
        foreach ( $dir as $fileinfo ) {
            if ( $fileinfo->isDot() ) {
                continue;
            }
            $fileDir = $fileinfo->getRealPath() . '/' . $fileName;
            if (file_exists($fileDir)) {
                require_once( $fileDir );
            }
        }
    }
}

<?php
if (!array_key_exists("data", $block)) {
    echo "Listing block data is invalid.";
    return false;
}
if (!array_key_exists("listing_block_category", $block["data"]) || $block['data']['listing_block_category'] === "") {
    echo "Listings block category is invalid.";
    return false;
}
$getCategory = get_term($block['data']['listing_block_category'], "listings_categories");
if (!$getCategory || $getCategory === null || is_wp_error($getCategory)) {
	echo "Listings block category is invalid.";
	return false;
}
require_once (plugin_dir_path(__FILE__) . "../../../../listings/class-tru-fetcher-listings.php");
$blockClass = new Tru_Fetcher_Listings();

acf_setup_meta( $block['data'], $block['id'], true );
$fields = get_fields();
$fields["filters"]["listings_filters"] = $blockClass->buildListingFilters($fields["filters"]["listings_filters"]);
if (isset($fields["custom_items_list_position"]) && is_array($fields["custom_items_list_position"])) {
    if (in_array("list_start", $fields["custom_items_list_position"])) {
        if (isset($fields["list_start_items"]->ID)) {
            $fields["list_start_items"] = get_fields($fields["list_start_items"]->ID);
        }
    }
    if (in_array("list_end", $fields["custom_items_list_position"])) {
        if (isset($fields["list_end_items"]->ID)) {
            $fields["list_end_items"] = get_fields($fields["list_end_items"]->ID);
        }
    }
    if (in_array("custom_position", $fields["custom_items_list_position"])) {
        if (isset($fields["custom_position_items"]->ID)) {
            $fields["custom_position_items"] = get_fields($fields["custom_position_items"]->ID);
        }
    }
}
var_dump($fields["list_start_items"]);
$dataJson = json_encode($fields);
?>
<div id="listing_block"
     data-category="<?php echo $getCategory->slug; ?>"
     data='<?php echo htmlentities($dataJson, ENT_QUOTES, 'UTF-8'); ?>'
></div>

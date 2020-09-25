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
require_once (plugin_dir_path(__FILE__) . "../../../listings/class-tru-fetcher-listings.php");
$blockClass = new Tru_Fetcher_Listings();

acf_setup_meta( $block['data'], $block['id'], true );
$fields = get_fields();
$fields["filters"]["listings_filters"] = $blockClass->buildListingFilters($fields["filters"]["listings_filters"]);
$dataJson = json_encode($fields);
?>
<div id="listing_block"
     data-category="<?php echo $getCategory->slug; ?>"
     data='<?php echo $dataJson; ?>'
></div>

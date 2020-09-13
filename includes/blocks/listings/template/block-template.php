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
?>
<div id="listing_block" data-category="<?php echo $getCategory->slug; ?>"></div>

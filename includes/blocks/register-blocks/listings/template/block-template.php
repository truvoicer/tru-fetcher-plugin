<?php
$blocksManager = new Tru_Fetcher_Blocks();
$blockData = $blocksManager->getBlockData($block);
if (!$blockData) {
    return;
}
$blockJson = $blocksManager->getBlockDataJson($blockData);
if (!$blockJson) {
    return;
}
if (!array_key_exists("listing_block_category", $blockData) || $blockData['listing_block_category'] === "") {
    echo "Listings block category is invalid.";
    return;
}
$getCategory = get_term($blockData['listing_block_category'], "listings_categories");
if (!$getCategory || $getCategory === null || is_wp_error($getCategory)) {
	echo "Listings block category is invalid.";
	return;
}
?>
<div id="listing_block"
     data-category="<?php echo $getCategory->slug; ?>"
     data='<?php echo $blockJson; ?>'
></div>

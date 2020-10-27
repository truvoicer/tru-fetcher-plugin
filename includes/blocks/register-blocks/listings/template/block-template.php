<?php
$blocksManager = new Tru_Fetcher_Blocks();
$blockData = $blocksManager->getBlockData($block);
$blockJson = $blocksManager->getBlockDataJson($blockData);
//if (!array_key_exists("listing_block_category", $blockData) || $blockData['listing_block_category'] === "") {
//    echo "Listings block category is invalid.";
//    return;
//}
//$getCategory = get_term($blockData['listing_block_category'], "listings_categories");
//if (!$getCategory || $getCategory === null || is_wp_error($getCategory)) {
//	echo "Listings block category is invalid.";
//	return;
//}
?>
<div id="listing_block"
     data='<?php echo $blockJson; ?>'
></div>

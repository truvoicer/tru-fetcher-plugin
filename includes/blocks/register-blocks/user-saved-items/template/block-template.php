<?php
$blocksManager = new Tru_Fetcher_Blocks();
$blockData = $blocksManager->getBlockData($block);
$blockJson = $blocksManager->getBlockDataJson($blockData);
?>
<div id="user_saved_items_block"
     data='<?php echo $blockJson; ?>'></div>
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
?>
<div id="hero_block"
     data='<?php echo $blockJson; ?>'></div>
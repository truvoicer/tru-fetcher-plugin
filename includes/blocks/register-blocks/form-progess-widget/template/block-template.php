<?php
$blocksManager = new Tru_Fetcher_Blocks();
$blockData = $blocksManager->getBlockData($block);
$blockJson = $blocksManager->getBlockDataJson($blockData);
?>
<div id="form_progress_widget"
     data='<?php echo $blockJson; ?>'></div>
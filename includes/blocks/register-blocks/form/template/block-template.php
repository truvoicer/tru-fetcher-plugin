<?php
$blocksManager = new Tru_Fetcher_Blocks();
$blockData = $blocksManager->getBlockData($block);
//$buildFormData = $blocksManager->buildFormData($blockData);
$blockJson = $blocksManager->getBlockDataJson($blockData);
?>
<div id="form_block"
     data='<?php echo $blockJson; ?>'></div>
<?php

use TruFetcher\Includes\Blocks\Tru_Fetcher_Blocks;

$blocksManager = new Tru_Fetcher_Blocks();
$blockData = $blocksManager->getBlockData($block);
$blockJson = $blocksManager->getBlockDataJson($blockData);
?>
<div id="login_block"
     data='<?php echo $blockJson; ?>'></div>

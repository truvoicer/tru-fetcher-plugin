<?php
acf_setup_meta( $block['data'], $block['id'], true );
$fields = get_fields();
$dataJson = json_encode($fields);
?>
<div id="item_view_block"
     data='<?php echo $dataJson; ?>'></div>
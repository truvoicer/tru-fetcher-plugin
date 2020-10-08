<?php
acf_setup_meta( $block['data'], $block['id'], true );
$fields = get_fields();
if (array_key_exists("tabs_block_type", $fields) && $fields["tabs_block_type"] === "custom_tabs") {
    foreach ($fields["custom_tabs"] as $key => $custom_tab) {
        if (!isset($custom_tab["custom_carousel"]["carousel_data"]["item_list"]->ID)) {
            continue;
        }
        $id = $custom_tab["custom_carousel"]["carousel_data"]["item_list"]->ID;
        $getList = get_fields($id);
        if (!$getList || !isset($getList["items_data"])) {
            continue;
        }
        $fields["custom_tabs"][$key]["custom_carousel"]["carousel_data"]["item_list"] = $getList["items_data"];
    }
}
$dataJson = json_encode($fields);
?>
<div id="tabs_block"
     data='<?php echo htmlentities($dataJson, ENT_QUOTES, 'UTF-8'); ?>'></div>

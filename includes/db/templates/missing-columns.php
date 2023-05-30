<?php
//var_dump($data['missing_columns']);
$errors = [];
$results = [];

//var_dump($data['missing_columns']);
if (!$data['missing_columns']['success']) {
    $errors = $data['missing_columns']['errors'];
} elseif ($data['missing_columns']['success']) {
    $results = $data['missing_columns']['data'];
}
$linkClass = null;
switch ($data['notice']) {
    case 'network_admin_notices':
        $linkClass = 'tr_news_app_db_network_update_columns';
        break;
    case 'admin_notices':
        $linkClass = 'tr_news_app_db_update_columns';
        break;
}
?>
<div class="notice notice-error is-dismissible tr-news-app-admin-messages">
    <div class="tr-news-app--messages--display">
        <p><?php _e('Tr News App Database health check Failed. ', 'sample-text-domain'); ?></p>
        <p><?php _e('Database health check table update errors. ', 'sample-text-domain'); ?></p>
        <ul>
            <?php foreach ($errors as $item): ?>
                <li>
                    <p>Model: <strong><?php echo $item['model_name']; ?></strong></p>
                    <p>Errors:</p>
                    <ul>
                        <?php foreach ($item['errors'] as $error): ?>
                            <li>
                                <?php echo $error; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
            <?php if (!count($errors)):  ?>
                <?php foreach ($results as $item):   ?>
                    <li>
                        <p>Model: <strong><?php echo $item['model_name']; ?></strong></p>
                        <p>Columns:</p>
                        <?php if (is_array($item['data']['new_columns']) && count($item['data']['new_columns'])): ?>
                        <p>New Columns: <?php echo implode(', ', $item['data']['new_columns']); ?></p>
                        <?php endif; ?>
                        <?php if (is_array($item['data']['removed_columns']) && count($item['data']['removed_columns'])): ?>
                        <p>Removed Columns: <?php echo implode(', ', $item['data']['removed_columns']); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <?php if (!count($errors)):  ?>
            <a href="" class="<?php echo $linkClass; ?>">
                <?php _e('Click here update table columns', 'sample-text-domain'); ?>
            </a>
        <?php endif; ?>
    </div>
</div>

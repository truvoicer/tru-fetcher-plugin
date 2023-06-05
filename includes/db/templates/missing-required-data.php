<?php

$linkClass = null;
switch ($data['notice']) {
    case 'network_admin_notices':
        $linkClass = 'tru_fetcher_db_network_req_data_install';
        break;
    case 'admin_notices':
        $linkClass = 'tru_fetcher_db_req_data_install';
        break;
}
?>
<div class="notice notice-error is-dismissible tr-news-app-admin-messages">
    <div class="tr-news-app--messages--display">
        <p><?php _e('Tr News App Database health check Failed. ', 'sample-text-domain'); ?></p>
        <p><?php _e('Database health check errors. ', 'sample-text-domain'); ?></p>
        <ul>
            <?php foreach ($data['required_data'] as $item): ?>
                <li>
                    <p>Model: <strong><?php echo $item['model']; ?></strong></p>
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
        </ul>
        <a href="" class="<?php echo $linkClass; ?>">
            <?php _e('Click here install required data', 'sample-text-domain'); ?>
        </a>
    </div>
</div>

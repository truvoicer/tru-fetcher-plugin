<?php
    $linkClass = null;
    switch ($data['notice']) {
        case 'network_admin_notices':
            $linkClass = 'tru_fetcher_database_network_install';
            break;
        case 'admin_notices':
            $linkClass = 'tru_fetcher_database_install';
            break;
    }
?>
<div class="notice notice-error is-dismissible tru-fetcher-admin-messages">
    <div class="tru-fetcher--messages--display">
        <p><?php _e('Tr News App Error. ', 'sample-text-domain'); ?></p>
        <p><?php _e('The following database tables are missing. ', 'sample-text-domain'); ?></p>
        <ul>
            <?php foreach ($data['tables'] as $tableName): ?>
                <li>
                    <?php echo $tableName; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="" class="<?php echo $linkClass; ?>">
            <?php _e('Click here install them', 'sample-text-domain'); ?>
        </a>
    </div>
</div>

<?php
defined('ABSPATH') || die;
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/* Delete options */
$options = array(
    'wpuquiz__cron_hook_croninterval',
    'wpuquiz__cron_hook_lastexec',
    'wpuquiz_options',
    'wpuquiz_wpuquiz_version'
);
foreach ($options as $opt) {
    delete_option($opt);
    delete_site_option($opt);
}

/* Delete all posts */
$allposts = get_posts(array(
    'post_type' => 'quiz',
    'numberposts' => -1,
    'fields' => 'ids'
));
foreach ($allposts as $p) {
    wp_delete_post($p, true);
}

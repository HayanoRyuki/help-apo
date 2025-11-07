<?php
// テーマセットアップ
function helpbase_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'helpbase_setup');

// デフォルトの投稿メニューを非表示
function helpbase_remove_default_post_type_menu() {
remove_menu_page('edit.php');
}
add_action('admin_menu', 'helpbase_remove_default_post_type_menu');
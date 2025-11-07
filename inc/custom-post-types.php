<?php

// カスタム投稿タイプ：help（ヘルプ記事）
function helpbase_register_post_type_help() {
    register_post_type('help', [
        'labels' => [
            'name'          => 'ヘルプ記事',
            'singular_name' => 'ヘルプ',
        ],
        'public'              => true,
        'has_archive'         => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-sos',
        'supports'            => ['title', 'editor', 'thumbnail', 'revisions'],
        'rewrite'             => ['slug' => 'help'],
        'show_in_rest'        => true, // ブロックエディタ対応
        'taxonomies'          => ['post_tag', 'help_category'], // タグ＆独自カテゴリー追加
    ]);
}
add_action('init', 'helpbase_register_post_type_help');


// タクソノミー：カテゴリー（カスタム投稿タイプ「help」専用）
function helpbase_register_taxonomy_help_category() {
    register_taxonomy('help_category', 'help', [
        'labels' => [
            'name'              => 'カテゴリー',
            'singular_name'     => 'カテゴリー',
            'search_items'      => 'カテゴリーを検索',
            'all_items'         => 'すべてのカテゴリー',
            'edit_item'         => 'カテゴリーを編集',
            'update_item'       => 'カテゴリーを更新',
            'add_new_item'      => '新しいカテゴリーを追加',
            'new_item_name'     => '新しいカテゴリー名',
            'menu_name'         => 'カテゴリー',
        ],
        'public'            => true,
        'hierarchical'      => true, // true で親子関係のあるカテゴリに
        'show_admin_column' => true, // 管理画面一覧に表示
        'rewrite'           => ['slug' => 'help-category'], // URLスラッグ
    ]);
}
add_action('init', 'helpbase_register_taxonomy_help_category');




// ステータス
function register_status_post_type() {
    register_post_type('status', [
        'labels' => [
            'name' => 'ステータス',
            'singular_name' => 'ステータス',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 7,
        'menu_icon' => 'dashicons-megaphone', 
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'status'],
    ]);
}
add_action('init', 'register_status_post_type');

// リリース情報
function register_release_post_type() {
    register_post_type('release', [
        'labels' => [
            'name' => 'リリース情報',
            'singular_name' => 'リリース情報',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 8,
        'menu_icon' => 'dashicons-megaphone', 
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'release'],
    ]);
}
add_action('init', 'register_release_post_type');

// お知らせ
function register_notice_post_type() {
    register_post_type('notice', [
        'labels' => [
            'name' => 'お知らせ',
            'singular_name' => 'お知らせ',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_position' => 9,
        'menu_icon' => 'dashicons-megaphone', 
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'notice'],
    ]);
}
add_action('init', 'register_notice_post_type');

// タクソノミー：プロダクト名
function helpbase_register_taxonomy_help_product() {
    register_taxonomy('help_product', 'help', [
        'labels' => [
            'name' => 'プロダクト名',
            'singular_name' => 'プロダクト名',
            'search_items' => 'プロダクトを検索',
            'all_items' => 'すべてのプロダクト',
            'edit_item' => 'プロダクトを編集',
            'update_item' => 'プロダクトを更新',
            'add_new_item' => '新しいプロダクトを追加',
            'new_item_name' => '新しいプロダクト名',
            'menu_name' => 'プロダクト名',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'help-product'],
    ]);
}
add_action('init', 'helpbase_register_taxonomy_help_product');

// ========== helpで「順序(menu_order)」を使えるように ==========
add_action('init', function () {
    add_post_type_support('help', 'page-attributes'); // 編集画面に「順序」欄を表示
});

// ========== termsカテゴリだけ：番号順（1→2→3…）／未入力(0)は末尾 ==========
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) return;

    if ($q->is_tax('help_category', 'terms')) {
        // 一旦通常パラメータをセット（ページネーション等のため）
        $q->set('orderby', 'menu_order date');
        $q->set('order', 'ASC');
        $q->set('post_type', 'help');

        // このクエリに限り ORDER BY を上書きするフラグ
        $q->set('rcp_terms_force_order', true);
    }
});

add_filter('posts_orderby', function ($orderby, WP_Query $q) {
    if ($q->get('rcp_terms_force_order')) {
        global $wpdb;
        $p = $wpdb->posts;
        // menu_order = 0 を最後に送る → それ以外は昇順、同値なら日付降順
        $orderby = "CASE WHEN {$p}.menu_order = 0 THEN 999999 ELSE {$p}.menu_order END ASC, {$p}.post_date DESC";
    }
    return $orderby;
}, 10, 2);

// ========== 編集画面ラベル：terms 付き投稿だけ分かりやすく ==========
add_action('admin_head-post.php', 'rcp_terms_order_label');
add_action('admin_head-post-new.php', 'rcp_terms_order_label');
function rcp_terms_order_label() {
    global $post;
    if (!$post || $post->post_type !== 'help') return;

    $slugs = wp_get_post_terms($post->ID, 'help_category', ['fields' => 'slugs']);
    if (is_wp_error($slugs)) return;

    if (in_array('terms', $slugs, true)) {
        add_filter('gettext', function ($translation, $text, $domain) {
            // デフォルトの「Order（順序）」表示を差し替え
            if ($domain === 'default' && $text === 'Order') {
                return '規約カテゴリ（terms）内の表示順（1が一番上。未入力は自動で最後）';
            }
            return $translation;
        }, 10, 3);
    }
}

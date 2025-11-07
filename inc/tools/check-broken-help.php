<?php
defined('ABSPATH') || exit;

// 安全確認
if ( ! is_admin() || ! current_user_can('manage_options') ) {
    wp_die('権限がありません。');
}

// 大量記事対策
wp_raise_memory_limit('admin');
@set_time_limit(120);

// 対象の投稿タイプ（必要に応じて release, notice を追加）
$target_post_types = ['help'];

$args = [
    'post_type'      => $target_post_types,
    'post_status'    => ['publish', 'draft', 'pending', 'future', 'private'],
    'posts_per_page' => -1,
    'fields'         => 'ids',
];

$post_ids = get_posts($args);

$uploads        = wp_upload_dir();
$uploads_baseurl = $uploads['baseurl'];
$uploads_basedir = $uploads['basedir'];

echo '<div class="wrap"><h1>画像リンク切れの「すべての」' . esc_html(implode(',', $target_post_types)) . ' 記事</h1>';

$total_broken = 0;

foreach ($post_ids as $post_id) {
    $post    = get_post($post_id);
    $content = $post->post_content;

    // 記事内の <img src="..."> を全部拾う
    preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches);

    $broken_imgs = [];

    if (!empty($matches[1])) {
        foreach ($matches[1] as $img_url) {
            $img_url = html_entity_decode($img_url);

            // data: や blob: はスキップ
            if (preg_match('#^(data:|blob:)#i', $img_url)) {
                continue;
            }

            $local_path = '';

            // 絶対URL（サイトURL付き）
            if (strpos($img_url, $uploads_baseurl) === 0) {
                $local_path = str_replace($uploads_baseurl, $uploads_basedir, $img_url);

            // ルート相対 /wp-content/uploads/...
            } elseif (strpos($img_url, '/wp-content/uploads/') === 0) {
                $local_path = $uploads_basedir . str_replace('/wp-content/uploads', '', $img_url);

            // 外部ドメインはスキップ
            } else {
                continue;
            }

            // ?サイズ指定を除去
            $local_path = strtok($local_path, '?');

            if (!file_exists($local_path)) {
                $broken_imgs[] = $img_url;
            }
        }
    }

    if (!empty($broken_imgs)) {
        $total_broken += count($broken_imgs);
        echo '<h3><a href="' . esc_url(get_edit_post_link($post->ID)) . '" target="_blank" rel="noopener">'
           . esc_html(get_the_title($post)) . '</a>（ID: ' . intval($post->ID) . '）</h3>';
        echo '<ul>';
        foreach ($broken_imgs as $u) {
            echo '<li style="color:#c00;">✘ ' . esc_html($u) . '</li>';
        }
        echo '</ul>';
    }
}

if ($total_broken === 0) {
    echo '<p><strong>リンク切れは見つかりませんでした。</strong></p>';
}

echo '<p style="margin-top:1em;color:#555;">※赤文字の画像ファイルが存在していません（<code>file_exists</code> 判定）。外部ドメインは対象外です。</p>';
echo '</div>';

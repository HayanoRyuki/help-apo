<?php

function help_live_search() {
  $keyword = isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '';

  if (empty($keyword)) {
    wp_send_json([]);
    return;
  }

  // ★ 正規化処理を追加
  // 全角スペース・全角カンマ・半角カンマを半角スペースに統一
  $keyword = preg_replace('/[　、,]+/u', ' ', $keyword);
  $keyword = trim($keyword);

  // WP_Query に対して Relevanssi を使う
  $args = [
    'post_type'      => 'help',
    'posts_per_page' => 10,
    's'              => $keyword,
    'post_status'    => 'publish', // ← 追加
    'meta_query'     => [
        [
            'relation' => 'OR',
            [
                'key'     => '_mc_exclude_site_search',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_mc_exclude_site_search',
                'value'   => '1',
                'compare' => '!=',
            ],
        ],
    ],
  ];

  $query = new WP_Query($args);
  $results = [];

  if ($query->have_posts()) {
    foreach ($query->posts as $post) {
      $results[] = [
        'title' => get_the_title($post),
        'url'   => get_permalink($post),
      ];
    }
  }

  wp_send_json($results);
}


// add_action('pre_get_posts', function ($query) {
//     if (!is_admin() && $query->is_main_query() && defined('DOING_AJAX') && DOING_AJAX) {
//         $query->set('relevanssi', true);
//     }
// });


add_action('save_post', function($post_id) {
  $post_type = get_post_type($post_id);
  if (!in_array($post_type, ['release', 'notice', 'status'], true)) return;

  if (!isset($_POST['related_articles_nonce']) || !wp_verify_nonce($_POST['related_articles_nonce'], 'save_related_articles')) {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

  if (isset($_POST['related_articles']) && is_array($_POST['related_articles'])) {
    $sanitized = [];
    foreach ($_POST['related_articles'] as $article) {
      if (empty($article['url'])) continue;
      $sanitized[] = [
        'url'   => esc_url_raw($article['url']),
        'title' => sanitize_text_field($article['title']),
      ];
    }
    update_post_meta($post_id, '_related_articles', $sanitized);
  } else {
    delete_post_meta($post_id, '_related_articles');
  }
});

// ===========================
// ヘルプ記事一覧に help_category 絞り込み追加
// ===========================
add_action('restrict_manage_posts', function($post_type) {
    if ($post_type === 'help') {
        $taxonomy = 'help_category';
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);

        wp_dropdown_categories([
            'show_option_all' => $info_taxonomy->labels->all_items,
            'taxonomy'        => $taxonomy,
            'name'            => $taxonomy,
            'orderby'         => 'name',
            'selected'        => $selected,
            'hierarchical'    => true,
            'show_count'      => true,
            'hide_empty'      => false,
        ]);
    }
});

add_filter('parse_query', function($query) {
    global $pagenow;
    $taxonomy = 'help_category';
    $q_vars   = &$query->query_vars;
    if ($pagenow === 'edit.php'
        && isset($q_vars['post_type']) && $q_vars['post_type'] === 'help'
        && isset($_GET[$taxonomy]) && is_numeric($_GET[$taxonomy]) && $_GET[$taxonomy] != 0) {
        $term = get_term_by('id', $_GET[$taxonomy], $taxonomy);
        if ($term) {
            $q_vars[$taxonomy] = $term->slug;
        }
    }
});

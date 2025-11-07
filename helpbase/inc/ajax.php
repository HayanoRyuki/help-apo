<?php

// Ajax エンドポイント登録
add_action('wp_ajax_help_live_search', 'help_live_search'); // ログインユーザー用
add_action('wp_ajax_nopriv_help_live_search', 'help_live_search'); // 未ログインユーザー用


add_action('wp_ajax_fetch_title', function() {
  if (!current_user_can('edit_posts')) wp_die();

  $url = esc_url_raw($_GET['url'] ?? '');
  if (!$url) wp_send_json(['error' => 'No URL']);

  $response = wp_remote_get($url);
  if (is_wp_error($response)) {
    wp_send_json(['error' => 'Request failed']);
  }

  $html = wp_remote_retrieve_body($response);
  if (preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
    wp_send_json(['title' => trim($matches[1])]);
  } else {
    wp_send_json(['title' => '']);
  }
});

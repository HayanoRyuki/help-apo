<?php
// ======================================
// メインJS読込（search-result遷移用）
// ======================================
function helpbase_enqueue_scripts() {
  wp_enqueue_script(
    'helpbase-main',
    get_template_directory_uri() . '/assets/js/main.js',
    [],
    filemtime(get_template_directory() . '/assets/js/main.js'),
    true
  );
}
add_action('wp_enqueue_scripts', 'helpbase_enqueue_scripts');


// ======================================
// 各種CSS・JSの登録（メイン資産）
// ======================================
function helpbase_enqueue_assets() {
  $theme_uri = get_template_directory_uri();
  $theme_dir = get_template_directory();

  // ============================
  // 共通CSS
  // ============================
  wp_enqueue_style('helpbase-style', $theme_uri . '/assets/css/style.css');
  wp_enqueue_style('helpbase-common', $theme_uri . '/assets/css/common.css');
  wp_enqueue_style('helpbase-header', $theme_uri . '/assets/css/header.css', [], filemtime($theme_dir . '/assets/css/header.css'));
  wp_enqueue_style('helpbase-footer', $theme_uri . '/assets/css/footer.css', [], filemtime($theme_dir . '/assets/css/footer.css'));

  // ============================
  // トップページ専用
  // ============================
  if (is_front_page()) {
    wp_enqueue_style('helpbase-front-page', $theme_uri . '/assets/css/front-page.css');
  }

  // ============================
  // 固定ページ
  // ============================
  if (is_page()) {
    wp_enqueue_style('page-style', $theme_uri . '/assets/css/page.css');
  }

  // 共通：filemtime安全ラッパ
  $ver = function (string $path) use ($theme_dir) {
    $file = $theme_dir . $path;
    return file_exists($file) ? filemtime($file) : null;
  };

  // ============================
  // 固定ページテンプレートごとの個別CSS
  // ============================
  if (is_page_template('page-help-category-list.php')) {
    wp_enqueue_style(
      'help-category-list',
      $theme_uri . '/assets/css/page-help-category-list.css',
      ['helpbase-common', 'page-style'],
      $ver('/assets/css/page-help-category-list.css')
    );
  }

  if (is_page_template('page-help-category.php')) {
    wp_enqueue_style(
      'page-help-category',
      $theme_uri . '/assets/css/page-help-category.css',
      ['helpbase-common', 'page-style'],
      $ver('/assets/css/page-help-category.css')
    );
  }

  if (is_page_template('page-howtouse.php')) {
    wp_enqueue_style(
      'page-howtouse',
      $theme_uri . '/assets/css/page-howtouse.css',
      ['helpbase-common', 'page-style'],
      $ver('/assets/css/page-howtouse.css')
    );
  }

  if (is_page_template('page-how-to-contact.php')) {
    wp_enqueue_style(
      'page-how-to-contact',
      $theme_uri . '/assets/css/page-how-to-contact.css',
      ['helpbase-common', 'page-style'],
      $ver('/assets/css/page-how-to-contact.css')
    );
  }

  // ============================
  // カスタムタクソノミー
  // ============================
  if (is_tax('help_category')) {
    wp_enqueue_style(
      'taxonomy-help_category',
      $theme_uri . '/assets/css/taxonomy-help_category.css',
      [],
      filemtime($theme_dir . '/assets/css/taxonomy-help_category.css')
    );
  }

  // ============================
  // カスタム投稿タイプ：help
  // ============================
  if (is_singular('help')) {
    wp_enqueue_style('helpbase-single-help', $theme_uri . '/assets/css/single-help.css');
    wp_enqueue_script('help-toc', $theme_uri . '/js/help-toc.js', ['jquery'], null, true);

    // ▼ タブ用スクリプト
    wp_enqueue_script('help-tabs', $theme_uri . '/assets/js/help-tabs.js', [], null, true);

    // ▼ Lightbox2
    wp_enqueue_style('lightbox-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css');
    wp_enqueue_script('lightbox-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', [], null, true);
  }

  // ============================
  // カスタム投稿タイプ：status / release / notice
  // ============================
  if (is_singular('status')) {
    wp_enqueue_style('single-status', $theme_uri . '/assets/css/single-status.css');
  } elseif (is_post_type_archive('status')) {
    wp_enqueue_style('archive-status', $theme_uri . '/assets/css/archive-status.css');
  }

  if (is_singular('release')) {
    wp_enqueue_style('single-release', $theme_uri . '/assets/css/single-release.css');
  } elseif (is_post_type_archive('release')) {
    wp_enqueue_style('archive-release', $theme_uri . '/assets/css/archive-release.css');
  }

  if (is_singular('notice')) {
    wp_enqueue_style('single-notice', $theme_uri . '/assets/css/single-notice.css');
  } elseif (is_post_type_archive('notice')) {
    wp_enqueue_style('archive-notice', $theme_uri . '/assets/css/archive-notice.css');
  }

  // ============================
  // JS：検索機能（共通/Ajax）
  // ============================
  wp_enqueue_script('help-search', $theme_uri . '/assets/js/search.js', [], false, true);
  wp_localize_script('help-search', 'ajaxurl', admin_url('admin-ajax.php'));

  // ============================
  // 検索結果ページ専用CSS
  // ============================
  if (is_search()) {
    wp_enqueue_style(
      'helpbase-search',
      $theme_uri . '/assets/css/search.css',
      ['helpbase-common'],
      filemtime($theme_dir . '/assets/css/search.css')
    );
  }
}
add_action('wp_enqueue_scripts', 'helpbase_enqueue_assets', 99);


// 固定ページ「初期設定の流れ」（page_id=53325）専用CSS
add_action('wp_enqueue_scripts', function () {
  if (is_page(53325)) {
    $dir_uri = get_stylesheet_directory_uri();
    $dir     = get_stylesheet_directory();
    $path    = '/assets/css/initial-setup-flow.css';
    $ver     = file_exists($dir . $path) ? filemtime($dir . $path) : null;

    wp_enqueue_style(
      'initial-setup-flow',
      $dir_uri . $path,
      ['helpbase-common', 'page-style'],
      $ver
    );
  }
}, 120);

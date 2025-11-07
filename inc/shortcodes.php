<?php
// ===============================================
// カスタム投稿タイプ「help」専用：Quicktags ボタン追加（統一ルール準拠）
// ===============================================
function helpbase_add_quicktags_for_help() {
  global $post;

  if (isset($post) && $post->post_type === 'help') { ?>
    <script type="text/javascript">
      if (typeof QTags !== 'undefined') {
        // Check
        QTags.addButton(
          'qt_check',
          'Check',
          '<div class="check-box"><p><span class="label-tag label-check">Check</span> ',
          '</p></div>'
        );

        // Tips
        QTags.addButton(
          'qt_tips',
          'Tips',
          '<div class="tips-box"><p><span class="label-tag label-tips">Tips</span> ',
          '</p></div>'
        );

        // Ref（参考）
        QTags.addButton(
          'qt_ref',
          'Ref',
          '<div class="ref-box"><span class="label-tag label-ref">参考</span>\n  <p><a href="#" target="_blank" rel="noopener"><i class="fa fa-angle-right fa-fw"></i>リンクタイトル</a></p>\n</div>\n',
          ''
        );

        // Note
        QTags.addButton('qt_note', 'Note', '<div class="note-box">', '</div>');

        // 2カラム（画像＋文）
        QTags.addButton(
          'qt_two_column',
          '2カラム（画像＋文）',
`<div class="help-two-column">
  <div class="help-col">
    <p>ここに説明テキストを書いてください。</p>
  </div>
  <div class="help-col">
    <a href="画像URL" data-lightbox="image">
      <img src="画像URL" alt="説明画像" />
    </a>
    <p class="help-two-column__caption">(画像をクリックすると拡大表示します)</p>
  </div>
</div>\n`,
          ''
        );
      }
    </script>
  <?php }
}
add_action('admin_footer-post.php', 'helpbase_add_quicktags_for_help');
add_action('admin_footer-post-new.php', 'helpbase_add_quicktags_for_help');


// ================================
// ショートコード [help_card url="URL"]
// ================================
function helpbase_help_card_shortcode($atts) {
  $atts = shortcode_atts(['url' => ''], $atts);

  if (empty($atts['url'])) return '';
  $post_id = url_to_postid($atts['url']);
  if (!$post_id) return '';
  if (get_post_type($post_id) !== 'help') return '';

  $title   = get_the_title($post_id);
  $excerpt = get_the_excerpt($post_id);

  ob_start(); ?>
  <div class="help-related-card">
    <p class="help-related-label">関連リンク</p>
    <a href="<?php echo esc_url($atts['url']); ?>">
      <p class="help-related-title"><?php echo esc_html($title); ?></p>
      <?php if ($excerpt): ?>
        <p class="help-related-excerpt"><?php echo esc_html($excerpt); ?></p>
      <?php endif; ?>
    </a>
  </div>
  <?php
  return ob_get_clean();
}
add_shortcode('help_card', 'helpbase_help_card_shortcode');


// ================================
// [rcp_error_table]...行データ...[/rcp_error_table]
// 行は「コード|内容|対応」を1行1件、改行区切り。内容/対応はHTML可。改行は \n でも OK。
// ================================
add_action('init', function () {
  add_shortcode('rcp_error_table', function ($atts, $content = null) {
    if ($content === null) return '';

    $allowed_tags = wp_kses_allowed_html('post');
    $rows = array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $content)));

    $cell      = 'vertical-align:top;padding:10px;border:1px solid #ccc;';
    $multiline = $cell . 'white-space:pre-line;word-break:break-word;';

    ob_start(); ?>
    <table class="rcp-error-table" style="border-collapse: collapse; width: 100%; table-layout: fixed; border: 1px solid #ccc;">
      <thead>
        <tr>
          <th style="<?php echo esc_attr($cell); ?>text-align:left;width:15%;">エラーコード</th>
          <th style="<?php echo esc_attr($cell); ?>text-align:left;width:35%;">エラー内容</th>
          <th style="<?php echo esc_attr($cell); ?>text-align:left;width:50%;">対応方法</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $line):
          $parts  = explode('|', $line);
          $code   = isset($parts[0]) ? trim($parts[0]) : '';
          $desc   = isset($parts[1]) ? trim($parts[1]) : '';
          $action = count($parts) > 2 ? trim(implode('|', array_slice($parts, 2))) : '';

          $desc   = str_replace('\\n', "\n", $desc);
          $action = str_replace('\\n', "\n", $action);
        ?>
        <tr>
          <td style="<?php echo esc_attr($cell); ?>"><?php echo esc_html($code); ?></td>
          <td style="<?php echo esc_attr($multiline); ?>"><?php echo wp_kses($desc, $allowed_tags); ?></td>
          <td style="<?php echo esc_attr($multiline); ?>"><?php echo wp_kses($action, $allowed_tags); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php
    return ob_get_clean();
  });
});


// ================================
// Manual download shortcodes
// ================================
add_action('init', function () {
  add_shortcode('manual_file', 'rcp_sc_manual_file');   // 単体
  add_shortcode('manual_files', 'rcp_sc_manual_files'); // ラッパー
});

/** 拡張子からバッジ */
function rcp_manual_badge_from_ext($ext) {
  $ext = strtolower($ext);
  $map = [
    'ppt'  => ['PPT',  'ppt'],
    'pptx' => ['PPT',  'ppt'],
    'pdf'  => ['PDF',  'pdf'],
    'xls'  => ['XLS',  'xls'],
    'xlsx' => ['XLS',  'xls'],
    'doc'  => ['DOC',  'doc'],
    'docx' => ['DOC',  'doc'],
    'zip'  => ['ZIP',  'zip'],
  ];
  return $map[$ext] ?? ['FILE', 'file'];
}

/** ローカルURL→サイズ取得（uploads配下のみ） */
function rcp_manual_filesize_from_url($url) {
  $uploads = wp_upload_dir();
  if (strpos($url, $uploads['baseurl']) === 0) {
    $local = $uploads['basedir'] . str_replace($uploads['baseurl'], '', $url);
    if (is_readable($local)) return filesize($local);
  }
  return null;
}

/** bytes→人間向け表記 */
function rcp_manual_human_size($bytes) {
  if ($bytes === null) return '';
  $units = ['B','KB','MB','GB','TB'];
  $i = 0;
  while ($bytes >= 1024 && $i < count($units)-1) { $bytes /= 1024; $i++; }
  return sprintf('%.2f %s', $bytes, $units[$i]);
}

/**
 * [manual_file]
 * 例：
 *   [manual_file id="123" title="Microsoft Teams複数チーム"]
 *   [manual_file url="/wp-content/uploads/2025/08/sample.pptx" title="Google Chat" size="2.01 MB"]
 */
function rcp_sc_manual_file($atts) {
  $a = shortcode_atts([
    'id'     => '',
    'url'    => '',
    'title'  => '',
    'size'   => '',
    'btn'    => 'ダウンロード',
    'newtab' => '0',
  ], $atts, 'manual_file');

  $url = '';
  $title = trim($a['title']);
  $size_text = trim($a['size']);
  $ext = 'file';

  if ($a['id']) {
    $att_id = (int)$a['id'];
    $url = wp_get_attachment_url($att_id);
    if (!$title)  $title = get_the_title($att_id);
    $file = get_attached_file($att_id);
    if (is_readable($file)) {
      $size_text = rcp_manual_human_size(filesize($file));
    }
    $ext = pathinfo($file, PATHINFO_EXTENSION) ?: $ext;
  } else {
    $url = $a['url'];
    if (!$url) return '';
    if (!$size_text) {
      $bytes = rcp_manual_filesize_from_url($url);
      if ($bytes) $size_text = rcp_manual_human_size($bytes);
    }
    $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: $ext;
  }

  [$badge_text, $badge_mod] = rcp_manual_badge_from_ext($ext);
  $target = ($a['newtab'] === '1') ? ' target="_blank" rel="noopener"' : '';
  $url = esc_url($url);
  $title = esc_html($title);
  $size_html = $size_text ? '<span class="manual-size">'.esc_html($size_text).'</span>' : '';

  $html  = '<div class="manual-item">';
  $html .= '  <div class="manual-info">';
  $html .= '    <span class="manual-badge manual-badge--'.$badge_mod.'">'.$badge_text.'</span>';
  $html .= '    <span class="manual-title">'.$title.'</span>';
  $html .=      $size_html;
  $html .= '  </div>';
  $html .= '  <a class="manual-btn" href="'.$url.'" download'.$target.'> '.esc_html($a['btn']).' </a>';
  $html .= '</div>';

  return $html;
}

/** [manual_files] ラッパー */
function rcp_sc_manual_files($atts, $content = '') {
  $a = shortcode_atts([
    'title' => '',
    'icon'  => '', // 任意: "teams", "gchat" など
  ], $atts, 'manual_files');

  $title = trim($a['title']);
  $title_html = $title ? '<div class="manual-group-title">'.esc_html($title).'</div>' : '';
  $inner = do_shortcode($content);

  if (trim($inner) === '') return '';
  return '<div class="manual-group'.($a['icon'] ? ' manual-group--'.esc_attr($a['icon']) : '').'">'
       . $title_html
       . '<div class="manual-list">'.$inner.'</div>'
       . '</div>';
}

// ===============================================
// カスタム投稿タイプ「release」専用：Quicktags ボタン追加
// ===============================================
function release_add_quicktags() {
  global $post;

  if (isset($post) && $post->post_type === 'release') { ?>
    <script type="text/javascript">
      if (typeof QTags !== 'undefined') {
        // Check for release
        QTags.addButton(
          'qt_release_check',
          'Check',
          '<p class="release-label check">\n  <strong>Check</strong> ',
          '</p>'
        );

        // Tips for release
        QTags.addButton(
          'qt_release_tips',
          'Tips',
          '<p class="release-label tips">\n  <strong>Tips</strong> ',
          '</p>'
        );
      }
    </script>
  <?php }
}
add_action('admin_footer-post.php', 'release_add_quicktags');
add_action('admin_footer-post-new.php', 'release_add_quicktags');

// ===============================================
// カスタム投稿タイプ「status」専用：Quicktags ボタン追加
// ===============================================
function status_add_quicktags_update_box() {
  global $post;

  if (isset($post) && $post->post_type === 'status') { ?>
    <script type="text/javascript">
      if (typeof QTags !== 'undefined') {
        // 追記情報ボックス
        QTags.addButton(
          'qt_status_update_box',
          '追記情報',
`<div class="status-update-box">
  <h4>【追記情報 2025/9/1 12:00】</h4>
  <p>ここに追記情報を記入。</p>
</div>\n`,
          ''
        );
      }
    </script>
  <?php }
}
add_action('admin_footer-post.php', 'status_add_quicktags_update_box');
add_action('admin_footer-post-new.php', 'status_add_quicktags_update_box');

<?php



// アップデート日メタボックス追加
function add_release_update_date_meta_box() {
  add_meta_box(
    'release_update_date',
    'アップデート日',
    'display_release_update_date_meta_box',
    'release',
    'side',
    'default'
  );
}
add_action('add_meta_boxes', 'add_release_update_date_meta_box');

function display_release_update_date_meta_box($post) {
  $update_date = get_post_meta($post->ID, '_update_date', true);
  wp_nonce_field('save_release_update_date', 'release_update_date_nonce');
  echo '<label for="release_update_date">アップデート日（例：2025/06/27）</label>';
  echo '<input type="text" id="release_update_date" name="release_update_date" value="' . esc_attr($update_date) . '" style="width: 100%; margin-top: 6px;" />';
}

function save_release_update_date_meta_box($post_id) {
  if (!isset($_POST['release_update_date_nonce']) ||
      !wp_verify_nonce($_POST['release_update_date_nonce'], 'save_release_update_date')) {
    return;
  }

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

  if (!current_user_can('edit_post', $post_id)) return;

  if (isset($_POST['release_update_date'])) {
    $date = sanitize_text_field($_POST['release_update_date']);
    update_post_meta($post_id, '_update_date', $date);
  }
}
add_action('save_post_release', 'save_release_update_date_meta_box');

function remove_discussion_meta_box_for_release() {
    remove_meta_box('commentstatusdiv', 'release', 'normal'); // コメント許可のチェックボックス
    remove_meta_box('commentsdiv', 'release', 'normal');      // コメント一覧
    remove_meta_box('trackbacksdiv', 'release', 'normal');    // ピンバック／トラックバック
}
add_action('add_meta_boxes', 'remove_discussion_meta_box_for_release');

function add_lightbox_assets() {
    wp_enqueue_style('lightbox2-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css');
    wp_enqueue_script('lightbox2-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'add_lightbox_assets');

// 解決済みメタボックス追加
function add_status_meta_box() {
    add_meta_box(
        'status_resolved',
        '障害の状態',
        'status_resolved_meta_box_callback',
        'status', // ← カスタム投稿タイプ名
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_status_meta_box');

function status_resolved_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_status_resolved', true);
    echo '<label><input type="checkbox" name="status_resolved" value="1"' . checked($value, '1', false) . '> 解決済みにする</label>';
}

// 解決済みの保存処理
function save_status_meta_box($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['status_resolved'])) {
        update_post_meta($post_id, '_status_resolved', '1');
    } else {
        delete_post_meta($post_id, '_status_resolved');
    }
}
add_action('save_post', 'save_status_meta_box');

// ステータス一覧に「状態」列を追加
function add_status_column($columns) {
    $columns['resolved'] = '状態';
    return $columns;
}
add_filter('manage_edit-status_columns', 'add_status_column');

// カラムの中身を出力
function show_status_column($column_name, $post_id) {
    if ($column_name === 'resolved') {
        $resolved = get_post_meta($post_id, '_status_resolved', true);
        if ($resolved === '1') {
            echo '<span style="color: green; font-weight: bold;">✅ 解決済み</span>';
        } else {
            echo '<span style="color: red; font-weight: bold;">⚠ 未解決</span>';
        }
    }
}
add_action('manage_status_posts_custom_column', 'show_status_column', 10, 2);



// 投稿タイプ制限付きでメタボックス追加
foreach (['release', 'notice', 'status'] as $post_type) {
  add_action("add_meta_boxes_{$post_type}", function() use ($post_type) {
    add_meta_box(
      'related_articles_box',
      '関連記事',
      'render_related_articles_box',
      $post_type,
      'normal',
      'default'
    );
  });
}

// メタボックスの表示内容
function render_related_articles_box($post) {
  $related_articles = get_post_meta($post->ID, '_related_articles', true);
  if (!is_array($related_articles)) $related_articles = [];

  echo '<div id="related-articles-wrapper">';
  foreach ($related_articles as $index => $article) {
    render_related_article_fields($index, $article['url'] ?? '', $article['title'] ?? '');
  }
  if (empty($related_articles)) {
    render_related_article_fields(0, '', '');
  }
  echo '</div>';

  echo '<button type="button" class="button" id="add-related-article">関連記事を追加</button>';

  wp_nonce_field('save_related_articles', 'related_articles_nonce');

  // JS（軽めなので inline でOK）
  echo <<<HTML
<script>
document.addEventListener("DOMContentLoaded", function() {
  let wrapper = document.getElementById('related-articles-wrapper');
  let addBtn = document.getElementById('add-related-article');

  addBtn.addEventListener('click', () => {
    const count = wrapper.children.length;
    const template = `
      <div class="related-article" style="margin-bottom: 1em;">
        <label>URL:<br>
          <input type="url" name="related_articles[\${count}][url]" class="related-url" style="width:100%" />
        </label><br>
        <label>タイトル:<br>
          <input type="text" name="related_articles[\${count}][title]" class="related-title" style="width:100%" />
        </label><br>
        <button type="button" class="remove-related-article button">削除</button>
      </div>`;
    wrapper.insertAdjacentHTML('beforeend', template);
  });

  wrapper.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-related-article')) {
      e.target.parentElement.remove();
    }
  });

  wrapper.addEventListener('change', function(e) {
    if (e.target.classList.contains('related-url')) {
      const rawUrl = e.target.value;
const cleanUrl = rawUrl.split("#")[0];  // #以降を削除

// 入力欄の値も cleanUrl に上書き（保存されるのはこちら）
e.target.value = cleanUrl;

const fetchUrl = cleanUrl + "#gsc.tab=0";  // タイトル取得用には付加
const titleInput = e.target.closest('.related-article').querySelector('.related-title');

fetch(ajaxurl + "?action=fetch_title&url=" + encodeURIComponent(fetchUrl))
  .then(res => res.json())
  .then(data => {
    if (data.title) titleInput.value = data.title;
  });

    }
  });
});
</script>
HTML;
}

// フィールドのHTML出力（別関数で管理）
function render_related_article_fields($index, $url, $title) {
  echo '<div class="related-article" style="margin-bottom: 1em;">';
  echo '<label>URL:<br>';
  echo '<input type="url" name="related_articles[' . $index . '][url]" value="' . esc_attr($url) . '" class="related-url" style="width:100%" />';
  echo '</label><br>';
  echo '<label>タイトル:<br>';
  echo '<input type="text" name="related_articles[' . $index . '][title]" value="' . esc_attr($title) . '" class="related-title" style="width:100%" />';
  echo '</label><br>';
  echo '<button type="button" class="remove-related-article button">削除</button>';
  echo '</div>';
}

/* =========================================================
 * 関連記事メタ 保存処理（release / notice / status）
 * =======================================================*/
function save_related_articles_meta( $post_id ) {
  // 対象外：自動保存・リビジョン
  if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) return;

  // 対象の投稿タイプのみ処理
  $pt = get_post_type( $post_id );
  $allowed = array( 'release', 'notice', 'status' );
  if ( ! in_array( $pt, $allowed, true ) ) return;

  // 権限チェック
  if ( ! current_user_can( 'edit_post', $post_id ) ) return;

  // nonce チェック
  if ( ! isset( $_POST['related_articles_nonce'] ) ||
       ! wp_verify_nonce( $_POST['related_articles_nonce'], 'save_related_articles' ) ) {
    return;
  }

  // 送信値が無ければメタ削除
  if ( ! isset( $_POST['related_articles'] ) || ! is_array( $_POST['related_articles'] ) ) {
    delete_post_meta( $post_id, '_related_articles' );
    return;
  }

  // サニタイズ & 空行除去
  $input = $_POST['related_articles'];
  $sanitized = array();
  foreach ( $input as $row ) {
    $url   = isset( $row['url'] ) ? esc_url_raw( trim( (string) $row['url'] ) ) : '';
    $title = isset( $row['title'] ) ? sanitize_text_field( (string) $row['title'] ) : '';

    // URLの # 以降はサーバ側でも削る（JS側と二重保険）
    if ( $url !== '' ) {
      $hash_pos = strpos( $url, '#' );
      if ( $hash_pos !== false ) {
        $url = substr( $url, 0, $hash_pos );
      }
    }

    // URLもタイトルも空ならスキップ
    if ( $url === '' && $title === '' ) {
      continue;
    }

    $sanitized[] = array(
      'url'   => $url,
      'title' => $title,
    );
  }

  // 1件も無ければ削除、あれば更新
  if ( empty( $sanitized ) ) {
    delete_post_meta( $post_id, '_related_articles' );
  } else {
    update_post_meta( $post_id, '_related_articles', $sanitized );
  }
}
add_action( 'save_post', 'save_related_articles_meta' );

/* =========================================================
 * タイトル自動取得 AJAX
 * =======================================================*/
function ajax_fetch_title_for_related_articles() {
  // ログインユーザーのみ（編集画面からの呼び出し想定）
  if ( ! is_user_logged_in() ) {
    wp_send_json( array( 'title' => '' ) );
  }

  $url = isset( $_GET['url'] ) ? esc_url_raw( (string) $_GET['url'] ) : '';
  if ( empty( $url ) ) {
    wp_send_json( array( 'title' => '' ) );
  }

  // 取得（タイムアウト短め）
  $res = wp_remote_get( $url, array( 'timeout' => 6 ) );
  if ( is_wp_error( $res ) ) {
    wp_send_json( array( 'title' => '' ) );
  }

  $code = wp_remote_retrieve_response_code( $res );
  $body = wp_remote_retrieve_body( $res );
  if ( $code !== 200 || empty( $body ) ) {
    wp_send_json( array( 'title' => '' ) );
  }

  // <title>抽出
  $title = '';
  if ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $body, $m ) ) {
    $title = html_entity_decode( wp_strip_all_tags( $m[1] ), ENT_QUOTES, get_bloginfo( 'charset' ) );
    $title = trim( $title );
  }

  wp_send_json( array( 'title' => $title ) );
}
add_action( 'wp_ajax_fetch_title', 'ajax_fetch_title_for_related_articles' );


/**
 * Robotsメタ設定メタボックス（全投稿タイプ）
 * ファイル: inc/meta-box.php
 */

// ------------------------------
// 1) 定数・ユーティリティ
// ------------------------------
if (!defined('MC_ROBOTS_META_KEY')) {
    define('MC_ROBOTS_META_KEY', '_mc_robots_meta'); // まとめて保存
}

/**
 * 既存値の取得（配列で返す）
 */
function mc_get_robots_meta_values($post_id) {
    $defaults = [
        'noindex'         => 0,
        'nofollow'        => 0,
        'noarchive'       => 0,
        'notranslate'     => 0,
        'noimageindex'    => 0,
        'nosnippet'       => 0,
        'noodp'           => 0, // 互換用（現在は非推奨だが旧サイト互換で残す）
        'max_snippet'     => '-1', // -1 = 制限なし
        'max_video'       => '-1',
        'max_image'       => 'large', // none | standard | large
    ];
    $saved = get_post_meta($post_id, MC_ROBOTS_META_KEY, true);
    return wp_parse_args(is_array($saved) ? $saved : [], $defaults);
}

// ------------------------------
// 2) メタボックス登録（全投稿タイプ）
// ------------------------------
add_action('add_meta_boxes', function () {
    // すべての投稿タイプ（リビジョンやメニュー等の内部は除外）
    $types = get_post_types(['_builtin' => false], 'names'); // カスタム投稿
    $builtin = get_post_types(['_builtin' => true, 'public' => true], 'names'); // post / page など
    $post_types = array_unique(array_merge($builtin, $types));

    foreach ($post_types as $pt) {
        add_meta_box(
            'mc-robots-meta',
            'Robots 設定',
            'mc_render_robots_meta_box',
            $pt,
            'normal',
            'default'
        );
    }
});

// ------------------------------
// 3) メタボックス描画
// ------------------------------
function mc_render_robots_meta_box($post) {
    $v = mc_get_robots_meta_values($post->ID);
    $exclude = get_post_meta($post->ID, '_mc_exclude_site_search', true) ? 1 : 0; // ←追加
    wp_nonce_field('mc_robots_meta_save', 'mc_robots_meta_nonce');
    ?>
    <style>
      .mc-robots-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px 20px;margin:8px 0 14px}
      .mc-robots-grid label{display:flex;align-items:center;gap:8px}
      .mc-robots-row{display:flex;gap:16px;align-items:center;margin:8px 0}
      .mc-robots-row input[type="number"]{width:110px}
      .mc-robots-row select{min-width:140px}
      @media (max-width:800px){.mc-robots-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    </style>

    <p>検索エンジン向けの <code>robots</code> 指示を投稿ごとに設定できます（チェックしない場合は出力されません）。</p>

    <div class="mc-robots-grid">
        <label><input type="checkbox" name="mc_robots[noindex]" <?php checked($v['noindex']); ?> value="1">インデックスさせない</label>
        <label><input type="checkbox" name="mc_robots[nofollow]" <?php checked($v['nofollow']); ?> value="1">リンクを追跡させない</label>
        <label><input type="checkbox" name="mc_robots[noarchive]" <?php checked($v['noarchive']); ?> value="1">キャッシュを保存させない</label>

        <label><input type="checkbox" name="mc_robots[notranslate]" <?php checked($v['notranslate']); ?> value="1">自動翻訳を許可しない</label>
        <label><input type="checkbox" name="mc_robots[noimageindex]" <?php checked($v['noimageindex']); ?> value="1">画像をインデックスさせない</label>
        <label><input type="checkbox" name="mc_robots[nosnippet]" <?php checked($v['nosnippet']); ?> value="1">検索結果にスニペットを表示しない</label>

        <label><input type="checkbox" name="mc_robots[noodp]" <?php checked($v['noodp']); ?> value="1">ODPを使用しない（互換用）</label>
    </div>

    <div class="mc-robots-row">
        <label>スニペットの最大文字数
            <input type="number" name="mc_robots[max_snippet]" value="<?php echo esc_attr($v['max_snippet']); ?>" step="1">
        </label>
        <label>動画プレビューの最大秒数
            <input type="number" name="mc_robots[max_video]" value="<?php echo esc_attr($v['max_video']); ?>" step="1">
        </label>
        <label>画像プレビューのサイズ
            <select name="mc_robots[max_image]">
                <?php
                $opts = ['none' => 'なし', 'standard' => '標準', 'large' => '大'];
                foreach ($opts as $key => $label) {
                    printf('<option value="%s"%s>%s</option>',
                        esc_attr($key),
                        selected($v['max_image'], $key, false),
                        esc_html($label)
                    );
                }
                ?>
            </select>
        </label>
    </div>

    <div class="mc-robots-row" style="margin-top:14px;border-top:1px solid #eee;padding-top:12px">
        <label style="display:flex;align-items:center;gap:8px;">
            <input type="checkbox" name="mc_exclude_site_search" value="1" <?php checked($exclude); ?>>
            サイト内検索に表示しない
        </label>
    </div>

    <p style="color:#666">※ 数値は <code>-1</code> を指定すると「制限なし」（Google推奨値）になります。</p>
    <?php
}


// ------------------------------
// 4) 保存処理
// ------------------------------
add_action('save_post', function ($post_id) {
    if (!isset($_POST['mc_robots_meta_nonce']) || !wp_verify_nonce($_POST['mc_robots_meta_nonce'], 'mc_robots_meta_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['post_type']) && !current_user_can('edit_post', $post_id)) return;

    $in = isset($_POST['mc_robots']) && is_array($_POST['mc_robots']) ? $_POST['mc_robots'] : [];

    $out = [
        'noindex'      => empty($in['noindex']) ? 0 : 1,
        'nofollow'     => empty($in['nofollow']) ? 0 : 1,
        'noarchive'    => empty($in['noarchive']) ? 0 : 1,
        'notranslate'  => empty($in['notranslate']) ? 0 : 1,
        'noimageindex' => empty($in['noimageindex']) ? 0 : 1,
        'nosnippet'    => empty($in['nosnippet']) ? 0 : 1,
        'noodp'        => empty($in['noodp']) ? 0 : 1,
        'max_snippet'  => isset($in['max_snippet']) ? (string)intval($in['max_snippet']) : '-1',
        'max_video'    => isset($in['max_video'])   ? (string)intval($in['max_video'])   : '-1',
        'max_image'    => (isset($in['max_image']) && in_array($in['max_image'], ['none','standard','large'], true)) ? $in['max_image'] : 'large',
    ];

    update_post_meta($post_id, MC_ROBOTS_META_KEY, $out);

    // サイト内検索除外フラグの保存
    $exclude = isset($_POST['mc_exclude_site_search']) ? 1 : 0;
    if ($exclude) {
        update_post_meta($post_id, '_mc_exclude_site_search', 1);
    } else {
        delete_post_meta($post_id, '_mc_exclude_site_search');
    }
});


// ------------------------------
// 5) <meta name="robots"> 出力
// ------------------------------
add_action('wp_head', function () {
    if (!is_singular()) return;

    $post_id = get_queried_object_id();
    $v = mc_get_robots_meta_values($post_id);

    // ディレクティブを組み立て（チェックされているものだけ）
    $directives = [];

    // noindex / nofollow は明示されたときのみ出力
    if ($v['noindex'])  $directives[] = 'noindex';
    if ($v['nofollow']) $directives[] = 'nofollow';

    if ($v['noarchive'])    $directives[] = 'noarchive';
    if ($v['notranslate'])  $directives[] = 'notranslate';
    if ($v['noimageindex']) $directives[] = 'noimageindex';
    if ($v['nosnippet'])    $directives[] = 'nosnippet';
    if ($v['noodp'])        $directives[] = 'noodp'; // 互換

    // 追加の制限系（-1 以外のとき出力）
    if ($v['max_snippet'] !== '-1') $directives[] = 'max-snippet:' . (int)$v['max_snippet'];
    if ($v['max_video']   !== '-1') $directives[] = 'max-video-preview:' . (int)$v['max_video'];
    if (!empty($v['max_image']))     $directives[] = 'max-image-preview:' . $v['max_image'];

    if (!empty($directives)) {
        printf("\n<meta name=\"robots\" content=\"%s\" />\n", esc_attr(implode(', ', $directives)));
        // Googlebot にも同一内容を出したい場合は以下を有効化
        // printf("<meta name=\"googlebot\" content=\"%s\" />\n", esc_attr(implode(', ', $directives)));
    }
}, 5);

// ------------------------------
// 6) サイト内検索から除外する処理
// ------------------------------
add_action('pre_get_posts', function (\WP_Query $q) {
    if (is_admin()) return;
    if (!$q->is_main_query() || !$q->is_search()) return;

    $meta_query = $q->get('meta_query') ?: [];
    $meta_query[] = [
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
    ];
    $q->set('meta_query', $meta_query);
});
<?php
// ===========================
// テーマ初期設定・サポート機能
// ===========================
require_once get_template_directory() . '/inc/setup.php';

// ===========================
// CSS・JSの読み込み
// ===========================
require_once get_template_directory() . '/inc/assets.php';

// ===========================
// カスタム投稿タイプ・タクソノミー
// ===========================
require_once get_template_directory() . '/inc/custom-post-types.php';

// ===========================
// メタボックス
// ===========================
require_once get_template_directory() . '/inc/meta-boxes.php';

// ===========================
// ショートコード
// ===========================
require_once get_template_directory() . '/inc/shortcodes.php';

// ===========================
// カスタムアクション・フック
// ===========================
require_once get_template_directory() . '/inc/actions.php';

// ===========================
// Ajax処理
// ===========================
require_once get_template_directory() . '/inc/ajax.php';



// ===========================
// 公開済み help記事の画像リンク切れチェック（管理者限定・一時スクリプト）
// ===========================
if (is_admin() && isset($_GET['check_help_images']) && current_user_can('manage_options')) {
    require get_template_directory() . '/inc/tools/check-broken-help.php';
    exit;
}

<?php get_header(); ?>

<main class="front-hero">
<main class="front-hero">

<section class="hero-section">
  <div class="hero-inner two-column">

        <!-- 左カラム：検索主体 -->
    <div class="hero-left">
      <h1 class="hero-title">何かお困りですか？</h1>

      <div class="tab-content active" id="tab-search">
        <form role="search" method="get" class="hero-search" action="<?php echo home_url( '/' ); ?>">
  <input type="search" id="help-search-input" name="s" placeholder="キーワードで検索" autocomplete="off">
  <button type="submit" id="help-search-button">検索</button>
</form>
<div id="search-results" class="search-results"></div>
      </div>
    </div>

    <!-- 右カラム：案内主体 -->
    <div class="hero-right">
      <p class="hero-subtext">※当サイトは「調整アポ」のヘルプサイトです。
      </p>
      <div class="adjust-link-box">
        「受付システム」と「予約ルームズ」のヘルプサイトは
		  <a href="https://help.receptionist.jp" target="_blank" rel="noopener"><strong>こちら</strong></a>
      </div>
    </div>

  </div>

  <!-- ▼ HERO内に新着ボックスを移動 ▼ -->
<?php
function has_recent_post($post_type, $days = 3) {
  $args = array(
    'post_type'      => $post_type,
    'posts_per_page' => 1,
    'date_query'     => array(
      array(
        'after' => "$days days ago",
      ),
    ),
    'post_status'    => 'publish',
  );
  $query = new WP_Query($args);
  return $query->have_posts();
}
?>

<div class="hero-links">
  <ul class="hero-link-list">

    <li class="link-notice">
      <a href="<?php echo get_post_type_archive_link('notice'); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/notice.svg" alt="notice" class="icon">
        お知らせ
      </a>
      <?php if (has_recent_post('notice')): ?>
        <span class="badge-new">新着あり</span>
      <?php endif; ?>
    </li>

    <li class="link-release">
      <a href="<?php echo get_post_type_archive_link('release'); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/release.svg" alt="release" class="icon">
        プロダクトアップデート
      </a>
      <?php if (has_recent_post('release')): ?>
        <span class="badge-new">新着あり</span>
      <?php endif; ?>
    </li>

    <li class="link-status">
      <a href="<?php echo get_post_type_archive_link('status'); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/status.svg" alt="status" class="icon">
        メンテナンス/障害情報
      </a>
      <?php if (has_recent_post('status')): ?>
        <span class="badge-new">新着あり</span>
      <?php endif; ?>
    </li>

  </ul>
</div>

</section>
	
<!-- おすすめ記事まとめ -->
<section class="recommended-section">
  <div class="l-container">
    <div class="recommended-grid">

<!-- ブロック1：調整アポ -->
<div class="recommended-block">
  <div class="recommended-header">
    <h3 class="recommended-title">調整アポ</h3>
  </div>
  <div class="recommended-columns">
    <div class="recommended-group">
      <h4><a href="/help-category/scheduling-user/">利用者向けガイド</a></h4>
      <ul>
        <li><a href="/how-to-create-pages/#gsc.tab=0">予約ページの作成・編集方法</a></li>
        <li><a href="/list-of-appointments/#gsc.tab=0">予約一覧の確認・CSV出力</a></li>
        <li><a href="/cancel/#gsc.tab=0">予約の変更・キャンセル</a></li>
        <li><a href="/host-operating-procedures/#gsc.tab=0">予約ページから予約する流れ</a></li>
      </ul>
    </div>
    <div class="recommended-group">
      <h4><a href="/help-category/scheduling-admin/">管理者向けガイド</a></h4>
      <ul>
        <li><a href="/web-registration/#gsc.tab=0">WEB登録（管理者）</a></li>
      </ul>
    </div>
  </div>
  <div class="recommended-footer">
    <a href="/help-category/scheduling/" class="recommended-link">すべて表示する</a>
  </div>
</div>

<!-- ブロック2：企業設定 -->
<div class="recommended-block">
  <div class="recommended-header">
    <h3 class="recommended-title">企業設定</h3>
  </div>
  <div class="recommended-columns">
    <div class="recommended-group">
      <h4><a href="/help-category/company-admin/">管理者向けガイド</a></h4>
      <ul>
        <li><a href="/handover-documents/#gsc.tab=0">調整アポの管理者用の引継ぎ資料</a></li>
        <li><a href="/administrator-authority/#gsc.tab=0">ユーザー権限の設定方法</a></li>
        <li><a href="/company-name/#gsc.tab=0">会社名の変更方法</a></li>
      </ul>
    </div>
    <div class="recommended-group">
      <h4><a href="/help-category/company-security/">セキュリティ</a></h4>
      <ul>
        <li><a href="/ip-restrictions/#gsc.tab=0">IPアドレス制限の設定方法</a></li>
        <li><a href="/own-subdomain/#gsc.tab=0">WEB管理画面URLに独自サブドメインを設定</a></li>
      </ul>
    </div>
  </div>
  <div class="recommended-footer">
    <a href="/help-category/company-settings/" class="recommended-link">すべて表示する</a>
  </div>
</div>

<!-- ブロック3：トラブルシューティング -->
<div class="recommended-block">
  <div class="recommended-header">
    <h3 class="recommended-title">トラブルシューティング</h3>
  </div>
  <div class="recommended-columns">
    <div class="recommended-group">
      <h4><a href="/help-category/web-errors/">WEB画面の不具合</a></h4>
      <ul>
        <li><a href="/unable-to-log-in/#gsc.tab=0">WEB管理画面にログインできない</a></li>
        <li><a href="/screen-does-not-display-properly/#gsc.tab=0">画面が正常に表示されない</a></li>
        <li><a href="/dont-get-mail/#gsc.tab=0">メールが届かない</a></li>
      </ul>
    </div>
    <div class="recommended-group">
      <h4><a href="/help-category/booking-errors/">予約ページの不具合</a></h4>
      <ul>
        <li><a href="/cannot-make-a-reservation/#gsc.tab=0">予約ページが予約できない・エラーが表示される</a></li>
        <li><a href="/available-times-for-reservation-trouble/#gsc.tab=0">時間枠や予約可能にならない</a></li>
        <li><a href="/delete-creator/#gsc.tab=0">作成者を削除できない</a></li>
      </ul>
    </div>
  </div>
  <div class="recommended-footer">
    <a href="/help-category/troubleshooting/" class="recommended-link">すべて表示する</a>
  </div>
</div>

<!-- ブロック4：契約 -->
<div class="recommended-block">
  <div class="recommended-header">
    <h3 class="recommended-title">契約</h3>
  </div>
  <div class="recommended-columns">
    <div class="recommended-group">
      <h4><a href="/help-category/pricing/">料金プラン</a></h4>
      <ul>
        <li><a href="/plans/#gsc.tab=0">調整アポのプラン別の機能比較</a></li>
        <li><a href="/plan_change/#gsc.tab=0">ご利用プランの変更方法</a></li>
        <li><a href="/payment/#gsc.tab=0">お支払い方法について</a></li>
      </ul>
    </div>
    <div class="recommended-group">
      <h4><a href="/help-category/terms/">規約・規定</a></h4>
      <ul>
        <li><a href="/terms-and-conditions/#gsc.tab=0">規約・規定・方針一覧</a></li>
      </ul>
    </div>
  </div>
  <div class="recommended-footer">
    <a href="/help-category/contract/" class="recommended-link">すべて表示する</a>
  </div>
</div>

<!-- ブロック5：その他 -->
<div class="recommended-block">
  <div class="recommended-header">
    <h3 class="recommended-title">その他</h3>
  </div>
  <div class="recommended-columns">
    <div class="recommended-group">
      <h4><a href="/help-category/environment/">推奨環境</a></h4>
      <ul>
        <li><a href="/recommended-environment/#gsc.tab=0">調整アポの推奨環境・事前準備</a></li>
      </ul>
      <h4><a href="/help-category/support/">サポート対応</a></h4>
      <ul>
        <li><a href="/support/#gsc.tab=0">お問い合わせ・サポート体制について</a></li>
      </ul>
    </div>
  </div>
  <div class="recommended-footer">
    <a href="/help-category/others/" class="recommended-link">すべて表示する</a>
  </div>
</div>

    </div>
  </div>
</section>



	
</main>

<?php get_footer(); ?>

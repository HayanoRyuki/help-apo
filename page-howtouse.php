<?php
/* Template Name: 使い方ガイド */
get_header();
?>

<main class="site-main howtouse-page">

  <!-- イントロ：囲まない -->
  <section class="section-hero">
    <div class="container">
      <h1 class="page-title">使い方ガイド</h1>
      <p class="lead-text">当ヘルプサイトの活用方法をすべてご紹介します。ログイン、検索、フォーム送信まで、これ一つで迷わず使えます。</p>
    </div>
  </section>

  <!-- ログイン方法 -->
  <section class="howtouse-box">
    <div class="container">
      <h2 class="section-title">1. ログイン方法</h2>
      <p>マイページへは、右上の「ログイン」ボタンからアクセスしてください。ログインには登録済みのメールアドレスとパスワードが必要です。</p>
      <img src="<?php echo get_template_directory_uri(); ?>/assets/img/howtouse/login.png" alt="ログイン画面">
    </div>
  </section>

  <!-- ヘルプ記事の探し方 -->
  <section class="howtouse-box">
    <div class="container">
      <h2 class="section-title">2. ヘルプ記事の探し方</h2>
      <p>トップページの検索ボックスにキーワードを入力することで、関連する記事をすばやく探せます。「カテゴリ別」「人気順」などのタブも活用してください。</p>
    </div>
  </section>

  <!-- フォームでのお問い合わせ -->
  <section class="howtouse-box">
    <div class="container">
      <h2 class="section-title">3. フォームでのお問い合わせ</h2>
      <p>記事を読んでも解決しない場合は、フォームからお気軽にお問い合わせください。必要事項を入力して送信いただければ、担当者より折り返しご連絡します。</p>
      <a href="/contact" class="button">お問い合わせフォームへ</a>
    </div>
  </section>

  <!-- FAQ -->
  <section class="howtouse-box">
    <div class="container">
      <h2 class="section-title">4. よくある質問（FAQ）</h2>
      <ul class="faq-list">
        <li><strong>Q:</strong> パスワードを忘れた場合は？<br><strong>A:</strong> ログイン画面の「パスワードをお忘れですか？」から再発行できます。</li>
        <li><strong>Q:</strong> ユーザー登録に費用はかかりますか？<br><strong>A:</strong> いいえ、登録は無料です。</li>
      </ul>
    </div>
  </section>

</main>

<?php get_footer(); ?>

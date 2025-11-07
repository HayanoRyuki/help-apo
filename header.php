<?php
/**
 * ヘッダーテンプレート
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PWZVTMZ');</script>
<!-- End Google Tag Manager -->
	
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PWZVTMZ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

  <header class="site-header">
    <div class="header-inner">

      <div class="site-branding">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="branding-link">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/img/scheduling_logo_help.png" alt="調整アポロゴ" class="header-logo">
        </a>
      </div>

      <div class="header-actions">
        <a href="<?php echo esc_url(home_url('/howtouse')); ?>" class="btn-outline">お問い合わせ方法</a>
      </div>

    </div>
  </header>

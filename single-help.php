<?php get_header(); ?>

<div class="l-container l-container--2col" role="region" aria-label="<?php esc_attr_e( 'ヘルプ本文', 'your-textdomain' ); ?>">

  <!-- 左コラム：目次 -->
  <aside class="l-container__col l-container__col--sub">
    <div class="p-toc">
      <p class="p-toc__title"><?php esc_html_e( '目次', 'your-textdomain' ); ?></p>
      <nav id="toc-list" class="p-toc__nav" aria-label="<?php esc_attr_e( '目次', 'your-textdomain' ); ?>"></nav>
    </div>
  </aside>

  <!-- 右コラム：本文 -->
  <div class="l-container__col l-container__col--main">
    <main class="help-article-main" role="main" aria-labelledby="page-title">
      <article id="post-<?php the_ID(); ?>" <?php post_class('help-article'); ?> itemscope itemtype="https://schema.org/TechArticle">

        <header class="help-article__header">
          <h1 class="c-title--page" id="page-title" itemprop="headline"><?php the_title(); ?></h1>
        </header>

        <div id="post-content" class="help-article-body" itemprop="articleBody">
          <?php the_content(); ?>

          <?php
          // ページ分割（<!--nextpage-->）対応
          wp_link_pages( [
            'before' => '<div class="page-links">',
            'after'  => '</div>',
          ] );
          ?>
        </div>

        <!-- 文末CTA -->
        <section class="help-cta-box" aria-labelledby="help-cta-heading">
          <div class="help-cta-visual">
            <div class="help-cta-box-on-image">
              <p class="cta-heading" id="help-cta-heading">
                <?php esc_html_e( '不明点がある場合は、お気軽にお問い合わせください。', 'your-textdomain' ); ?>
              </p>
              <p>
                <?php
                // 強調は <strong> に統一
                echo wp_kses_post(
                  __( '右上の<strong>「お問い合わせ方法」</strong>ボタンから、チャット・メール・フォームなどの各種お問い合わせが可能です。', 'your-textdomain' )
                );
                ?>
              </p>
            </div>
          </div>
        </section>

         <!-- パンくず（カテゴリ系） -->
        <?php
        $terms = get_the_terms( get_the_ID(), 'help_category' );

        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) :
          // 優先度：親子深い順 or 最初のターム
          usort( $terms, function( $a, $b ) {
            return substr_count( $b->slug, '-' ) <=> substr_count( $a->slug, '-' );
          });
          $primary = $terms[0];

          // 親系統を上から順に整列
          $trail = [];
          $current = $primary;
          while ( $current && $current->parent ) {
            $current = get_term( $current->parent, 'help_category' );
            if ( $current && ! is_wp_error( $current ) ) {
              array_unshift( $trail, $current );
            } else {
              break;
            }
          }
          $trail[] = $primary;
          ?>
          <nav class="help-breadcrumbs" aria-label="<?php esc_attr_e('この記事のカテゴリ', 'your-textdomain'); ?>">
            <span class="help-breadcrumbs-label"><?php esc_html_e('この記事のカテゴリ：', 'your-textdomain'); ?></span>
            <ul class="help-breadcrumbs-list">
              <li>
                <span class="help-breadcrumbs-path">
                  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">TOP</a>
                  <?php foreach ( $trail as $crumb ) : ?>
                    <span class="separator" aria-hidden="true">›</span>
                    <a href="<?php echo esc_url( get_term_link( $crumb ) ); ?>">
                      <?php echo esc_html( $crumb->name ); ?>
                    </a>
                  <?php endforeach; ?>
                </span>
              </li>
            </ul>
          </nav>
        <?php endif; ?>

      </article>
    </main>
  </div>
</div>

<?php get_footer(); ?>
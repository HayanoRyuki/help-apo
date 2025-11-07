<?php get_header(); ?>

<main class="single-release two-columns">

  <!-- 左カラム：メイン記事 -->
  <div class="main-column">
    <article class="release-article">

      <header class="release-header">
        <h1 class="release-title">
          <?php
            $date = get_the_date('Y/m/d');
            echo '[' . $date . '] ' . esc_html(get_the_title());
          ?>
        </h1>
        <time class="release-date" datetime="<?php echo get_the_date('c'); ?>">
          <?php echo get_the_date('Y年n月j日'); ?>
        </time>
      </header>

      <div class="release-content">
        <?php
          $update_date = get_post_meta(get_the_ID(), '_update_date', true);
          if ($update_date) :
        ?>
          <div class="release-update-box">
            <strong>アップデート日：</strong><?php echo esc_html($update_date); ?>
          </div>
        <?php endif; ?>

        <?php the_content(); ?>
  <?php
          $related_articles = get_post_meta(get_the_ID(), '_related_articles', true);
          if (!empty($related_articles) && is_array($related_articles)) :
        ?>
          <section class="related-articles-block">
            <h3>関連記事</h3>
            <ul class="related-articles">
              <?php foreach ($related_articles as $article) :
                $url = esc_url($article['url'] ?? '');
                $title = esc_html($article['title'] ?? '');
                if ($url && $title): ?>
                  <li><a href="<?php echo $url; ?>" target="_blank" rel="noopener"><?php echo $title; ?></a></li>
                <?php endif;
              endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>

      </div>

    </article>
  </div>

  <!-- 右カラム：最新リリース一覧 -->
  <aside class="sidebar-column">
    <section class="release-latest">
      <h2 class="sidebar-heading">最新のリリース情報</h2>
      <ul class="latest-release-list">
        <?php
          $latest_releases = new WP_Query([
            'post_type'      => 'release',
            'posts_per_page' => 10,
            'post__not_in'   => [get_the_ID()],
          ]);

          if ($latest_releases->have_posts()) :
            while ($latest_releases->have_posts()) : $latest_releases->the_post(); ?>
              <li>
                <a href="<?php the_permalink(); ?>">
                  <?php
                    $date = get_the_date('Y/m/d');
                    echo '[' . $date . '] ' . esc_html(get_the_title());
                  ?>
                </a>
              </li>
            <?php endwhile;
            wp_reset_postdata();
          endif;
        ?>
      </ul>

      <div class="release-archive-link" style="margin-top: 1.5rem;">
        <a href="<?php echo get_post_type_archive_link('release'); ?>" class="archive-link">
          すべてのリリース情報を見る
        </a>
      </div>
    </section>
  </aside>

</main>

<?php get_footer(); ?>

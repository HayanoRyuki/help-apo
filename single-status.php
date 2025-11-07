<?php get_header(); ?>

<main class="single-status">

  <!-- コンテンツ全体を内包 -->
  <div class="single-status-inner two-columns">

    <!-- 左カラム：メイン記事 -->
    <div class="main-column">
      <article class="status-article">
        <header class="status-header">
          <h1 class="status-title"><?php the_title(); ?></h1>

          <?php
            $resolved = get_post_meta(get_the_ID(), '_status_resolved', true);
            if ($resolved === '1') {
              echo '<div class="status-label resolved">この障害は解決済みです</div>';
            } else {
              echo '<div class="status-label unresolved">現在も対応中です</div>';
            }
          ?>

          <time class="status-date" datetime="<?php echo get_the_date('c'); ?>">
            <?php echo get_the_date('Y年n月j日'); ?>
          </time>
        </header>

        <div class="status-content">
          <?php the_content(); ?>
        </div>
      </article>
    </div>

    <!-- 右カラム：過去記事 -->
    <aside class="sidebar-column">
      <div class="status-sidebar">
        <h2 class="sidebar-heading">過去の障害情報</h2>
        <ul class="status-list">
          <?php
          $args = [
            'post_type' => 'status',
            'post__not_in' => [get_the_ID()],
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC',
          ];
          $recent_posts = new WP_Query($args);
          if ($recent_posts->have_posts()) :
            while ($recent_posts->have_posts()) : $recent_posts->the_post();
          ?>
            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
          <?php
            endwhile;
            wp_reset_postdata();
          endif;
          ?>
        </ul>
      </div>
    </aside>

  </div><!-- /.single-status-inner -->

</main>

<?php get_footer(); ?>

<?php get_header(); ?>

<main class="archive-release">

  <section class="release-archive">
    <h1 class="archive-title">プロダクトアップデート</h1>

    <?php if (have_posts()) : ?>
      <ul class="release-list">
        <?php while (have_posts()) : the_post(); ?>
          <li class="release-item">
            <a href="<?php the_permalink(); ?>">
              <span class="release-date"><?php echo get_the_date('Y/m/d'); ?></span>
              <span class="release-title"><?php the_title(); ?></span>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>

<div class="pagination">
  <?php
    the_posts_pagination(array(
      'mid_size' => 1,
      'prev_text' => '&laquo; 前へ',
      'next_text' => '次へ &raquo;',
      'screen_reader_text' => 'ページ送り',
    ));
  ?>
</div>

    <?php else : ?>
      <p>現在、リリース情報はありません。</p>
    <?php endif; ?>
  </section>

</main>

<?php get_footer(); ?>

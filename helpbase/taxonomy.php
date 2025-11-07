<?php get_header(); ?>

<main class="taxonomy-archive">
  <section class="section-inner">
    <h1 class="archive-title">
      <?php echo single_term_title('「', false) . '」に関する記事一覧'; ?>
    </h1>

    <?php if (have_posts()) : ?>
      <ul class="taxonomy-post-list">
        <?php while (have_posts()) : the_post(); ?>
          <li class="taxonomy-post-item">
            <a href="<?php the_permalink(); ?>">
              <span class="taxonomy-post-title"><?php the_title(); ?></span>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else : ?>
      <p>該当する記事が見つかりませんでした。</p>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>

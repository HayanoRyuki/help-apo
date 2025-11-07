<?php get_header(); ?>

<main class="site-main">
  <section class="page-section">
    <div class="container">

      <!-- タイトル -->
      <header class="page-header">
        <h1 class="page-title"><?php the_title(); ?></h1>
      </header>

      <!-- 本文 -->
      <div class="page-content">
        <?php
        while (have_posts()) :
          the_post();
          the_content();
        endwhile;
        ?>
      </div>

    </div>
  </section>
</main>

<?php get_footer(); ?>

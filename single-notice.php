<?php get_header(); ?>

<main class="single-notice">
  <div class="single-notice-inner">
    <article class="notice-article">
      <header class="notice-header">
        <h1 class="notice-title"><?php the_title(); ?></h1>
        <time class="notice-date" datetime="<?php echo get_the_date('c'); ?>">
          <?php echo get_the_date('Y年n月j日'); ?>
        </time>
      </header>

      <div class="notice-content">
        <?php the_content(); ?>
      </div>
    </article>
  </div><!-- /.single-notice-inner -->
</main>

<?php get_footer(); ?>

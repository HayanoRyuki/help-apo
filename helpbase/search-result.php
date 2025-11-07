<?php
/* Template Name: 検索結果 */
get_header();
?>

<main class="search-result-page">
  <div class="container">
    <h1 class="search-title">検索結果</h1>

    <?php
    $keyword = get_search_query();
    if ($keyword) :
      $args = array(
        's' => $keyword,
        'post_status' => 'publish',
        'posts_per_page' => -1
      );
      $query = new WP_Query($args);
    ?>

    <p class="search-keyword">「<?php echo esc_html($keyword); ?>」の検索結果：</p>

    <?php if ($query->have_posts()) : ?>
      <ul class="search-result-list">
        <?php while ($query->have_posts()) : $query->the_post(); ?>
          <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
        <?php endwhile; ?>
      </ul>
    <?php else : ?>
      <p class="no-result">該当する記事は見つかりませんでした。</p>
    <?php endif; wp_reset_postdata(); ?>

    <?php else : ?>
      <p>キーワードを入力してください。</p>
    <?php endif; ?>
  </div>
</main>

<?php get_footer(); ?>

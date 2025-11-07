<?php
get_header();

$s = get_search_query();
$args = [
  'post_type'      => ['help'],
  'posts_per_page' => 150,
  's'              => $s,
  'orderby'        => 'relevance',
];

$query = new WP_Query($args);

if (function_exists('relevanssi_do_query') && $query instanceof WP_Query) {
  $query->is_search = true;
  $query->is_home   = false;
  relevanssi_do_query($query);
}
?>

<main class="l-main search-result">
  <div class="container">

    <!-- ==============================
         検索フォーム（上部）
    =============================== -->
    <form role="search" method="get" class="search-form-top" action="<?php echo esc_url(home_url('/')); ?>">
      <input
        type="search"
        class="search-field"
        placeholder="キーワードを入力"
        value="<?php echo esc_attr($s); ?>"
        name="s"
      />
      <button type="submit" class="search-submit">検索</button>
    </form>

    <!-- ==============================
         検索結果タイトル・件数
    =============================== -->
    <h1 class="search-title">
      <?php echo esc_html($s); ?> の検索結果
      <span class="search-count">計 <?php echo $query->found_posts; ?>件</span>
    </h1>

    <!-- ==============================
         検索結果一覧
    =============================== -->
    <?php if ($query->have_posts()): ?>
      <ul class="search-results">
        <?php while ($query->have_posts()): $query->the_post(); ?>
          <li class="search-item">
            <h2 class="search-item__title">
              <a href="<?php the_permalink(); ?>">
                <?php function_exists('relevanssi_the_title') ? relevanssi_the_title() : the_title(); ?>
              </a>
            </h2>
            <p class="search-item__excerpt">
              <?php
                if (function_exists('relevanssi_the_excerpt')) {
                  relevanssi_the_excerpt();
                } else {
                  echo wp_trim_words(get_the_excerpt(), 30);
                }
              ?>
            </p>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p class="no-results">該当する記事が見つかりませんでした。</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
  </div>
</main>

<?php get_footer(); ?>


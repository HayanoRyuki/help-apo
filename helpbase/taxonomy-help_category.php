<?php
/**
 * taxonomy-help_category.php
 * help_category タクソノミー一覧
 * - terms のときだけ：menu_order 昇順（0は末尾）→ 同値は post_date DESC
 * - それ以外：タイトル昇順
 */
get_header();
?>

<main class="site-main taxonomy-help-category-page">
  <section class="container">
    <?php
    // ターム取得
    $term = get_queried_object();

    // 子孫ID配列を初期化（自分も含める）
    $descendants = [];
    if ($term && isset($term->term_id)) {
      $descendants = get_term_children($term->term_id, 'help_category');
      $descendants[] = $term->term_id;
    }
    ?>

    <?php if ($term): ?>
      <h1 class="page-title">「<?php echo esc_html($term->name); ?>」に関する記事一覧</h1>
    <?php endif; ?>

    <?php
    // 並び設定（デフォルト：タイトル順）
    $args_order = [
      'orderby' => 'title',
      'order'   => 'ASC',
    ];

    // terms のときだけ menu_order（0は末尾）→ 日付降順に強制
    $remove_orderby_filter = false;
    if ($term && isset($term->slug) && $term->slug === 'terms') {
      // 形式上セット（SQLは posts_orderby で差し替え）
      $args_order = [
        'orderby' => 'menu_order date',
        'order'   => 'ASC',
      ];
      $orderby_filter = function ($orderby, WP_Query $q) {
        global $wpdb;
        $p = $wpdb->posts;
        // menu_order=0 を末尾へ、それ以外は昇順 → 同値は post_date DESC
        return "CASE WHEN {$p}.menu_order = 0 THEN 999999 ELSE {$p}.menu_order END ASC, {$p}.post_date DESC";
      };
      add_filter('posts_orderby', $orderby_filter, 10, 2);
      $remove_orderby_filter = true;
    }

    // クエリ
    $query = new WP_Query(array_merge([
      'post_type' => 'help',
      'tax_query' => [[
        'taxonomy'         => 'help_category',
        'field'            => 'term_id',
        'terms'            => $descendants,
        'operator'         => 'IN',
        'include_children' => false,
      ]],
      'posts_per_page' => -1,
    ], $args_order));
    ?>

    <?php if ($query->have_posts()) : ?>
      <?php
        // terms のときだけクラスを付与（CSSで1カラム化などに使える）
        $list_classes = 'help-article-list';
        if ($term && $term->slug === 'terms') {
          $list_classes .= ' help-article-list--terms';
        }
      ?>
      <ul class="<?php echo esc_attr($list_classes); ?>">
  <?php while ($query->have_posts()) : $query->the_post(); ?>
    <li>
      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </li>
  <?php endwhile; ?>
</ul>

      <?php wp_reset_postdata(); ?>
    <?php else : ?>
      <p>現在、該当する記事はありません。</p>
    <?php endif; ?>

    <?php
    // フィルター解除（terms のときだけ）
    if ($remove_orderby_filter) {
      remove_filter('posts_orderby', $orderby_filter, 10);
    }
    ?>
  </section>
</main>

<?php get_footer(); ?>

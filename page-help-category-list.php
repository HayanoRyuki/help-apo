<?php
/*
Template Name: ヘルプカテゴリ一覧
*/
get_header();
?>

<main class="site-main help-category-list-page">
  <section class="section-help-category-list">
    <div class="container">
      <h1 class="page-title">ヘルプカテゴリ一覧</h1>
      <p class="lead-text">カテゴリから探す場合はこちらからどうぞ。</p>

      <?php
      // すべての親カテゴリ取得（並び順は後で制御）
      $all_terms = get_terms(array(
        'taxonomy' => 'help_category',
        'parent' => 0,
        'hide_empty' => false,
      ));

      // 並び順のカスタム指定（slugで）
// 表示したい親カテゴリの順番（slug指定）
$custom_order = array(
  'scheduling',
  'company-settings',
  'troubleshooting',
  'contract',
  'others',
);

// 対象スラッグに該当する親カテゴリのみ抽出＆順序づけ
$ordered_terms = array();
foreach ($custom_order as $slug) {
  foreach ($all_terms as $term) {
    if ($term->slug === $slug) {
      $ordered_terms[] = $term;
      break;
    }
  }
}


      if (!empty($ordered_terms)) {
        echo '<div class="help-category-groups">';

        foreach ($ordered_terms as $parent) {
          $parent_link = get_term_link($parent);

          echo '<div class="help-category-group">';
          echo '<h2 class="help-category-title">
                  <a href="' . esc_url($parent_link) . '">' . esc_html($parent->name) . '</a>
                </h2>';

          if (!empty($parent->description)) {
            echo '<p class="term-description">' . esc_html($parent->description) . '</p>';
          }

          // 子カテゴリの取得
          $child_terms = get_terms(array(
            'taxonomy' => 'help_category',
            'parent' => $parent->term_id,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
          ));

          if (!empty($child_terms)) {
            echo '<ul class="help-subcategory-list">';
            foreach ($child_terms as $child) {
              $child_link = get_term_link($child);
              echo '<li>';
              echo '<a href="' . esc_url($child_link) . '">' . esc_html($child->name) . '（' . $child->count . '件）</a>';

              if (!empty($child->description)) {
                echo '<span class="term-description"> – ' . esc_html($child->description) . '</span>';
              }

              echo '</li>';
            }
            echo '</ul>';
          }

          echo '</div>';
        }

        echo '</div>';
      } else {
        echo '<p>現在、カテゴリーは登録されていません。</p>';
      }
      ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>

<?php get_header(); ?>

<main id="main" class="help-archive">
    <header class="archive-header">
        <h1 class="archive-title">ヘルプ一覧</h1>
    </header>

    <?php if (have_posts()) : ?>
        <div class="help-list">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('help-item'); ?>>
                    <h2 class="help-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

                    <div class="help-meta">
                        <?php
                        $stage_terms = get_the_terms(get_the_ID(), 'help_stage');
                        $user_terms  = get_the_terms(get_the_ID(), 'help_user_type');
                        $purpose_terms = get_the_terms(get_the_ID(), 'help_purpose');

                        function print_terms_list($terms) {
                            if ($terms && !is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    echo '<span class="term">' . esc_html($term->name) . '</span> ';
                                }
                            }
                        }

                        print_terms_list($stage_terms);
                        print_terms_list($user_terms);
                        print_terms_list($purpose_terms);
                        ?>
                    </div>

                    <div class="help-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="help-pagination">
            <?php the_posts_pagination(); ?>
        </div>

    <?php else : ?>
        <p>ヘルプ記事が見つかりませんでした。</p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>

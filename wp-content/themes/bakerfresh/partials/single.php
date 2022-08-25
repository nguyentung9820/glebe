<?php
/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package Bakerfresh
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<?php
while (have_posts()) :
    the_post();
    ?>

    <main <?php post_class('site-main'); ?> role="main">
        <?php if (apply_filters('bakerfresh/filter/enable_page_title', true)) : ?>
            <header class="page-header page-header--default">
                <div class="container page-header-inner">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </div>
            </header>
        <?php endif; ?>

        <div id="site-content-wrap" class="container">

            <?php get_sidebar(); ?>

            <div class="site-content--default">

                <div class="page-content<?php if(is_singular('post')) {
                    echo ' post-tpl-content post-tpl-content--' . get_post_format();
                } ?>">

                    <?php

                    the_content();

                    ?>

                </div>

                <div class="clearfix"></div>

                <?php

                if(is_singular('post')){
                    the_tags('<div class="post-tags"><span class="tag-label">'.__('Tagged: ', 'bakerfresh').'</span><span class="tag-links">', '<span class="tag-sep">, </span>', '</span></div>');
                }

                wp_link_pages( array(
                    'before' => '<div class="clearfix"></div><div class="page-links">' . esc_html__( 'Pages:', 'bakerfresh' ),
                    'after'  => '</div>',
                ) );

                wp_reset_postdata();

                if(comments_open() || get_comments_number()){
                    comments_template();
                }
                ?>

            </div>
        </div>

    </main>

<?php
endwhile;

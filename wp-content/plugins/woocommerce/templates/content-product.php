<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li id="single-product" <?php wc_product_class( '', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );
    ?>
    <div id="information">
        <?php
	/**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * Hook: woocommerce_after_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_rating - 5
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );
    ?>
        <?php
            echo '<div class="item">';
            echo wp_trim_words( get_the_excerpt(), 20 ).'...';
            echo '</div>';
        ?>
        <div class="tags">
            <?php if ( count( $product->get_tag_ids() ) ) : ?>
                 <?php foreach(explode(',',get_the_term_list( $product->get_id(), 'product_tag', '', ',' )) as $tag): ?>
                    <span id="tag" class="tagged_as detail-container">
                        <span class="detail-content"><?php echo $tag; ?></span>
                    </span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <?php
    /**
     * Hook: woocommerce_before_shop_loop_item_title.
     *
     * @hooked woocommerce_show_product_loop_sale_flash - 10
     * @hooked woocommerce_template_loop_product_thumbnail - 10
     */
    do_action( 'woocommerce_before_shop_loop_item_title' );
    ?>
        <?php
        printf(
            '<a class="%s" href="%s" data-href="%s" title="%s"><button class="button-buy-now labtn-text" style="display: none">%s</button></a>',
            'quickview la-quickview-button',
            esc_url(get_the_permalink($product->get_id())),
            esc_url(add_query_arg('product_quickview', $product->get_id(), get_the_permalink($product->get_id()))),
            esc_attr_x('Buy Now', 'front-view', 'bakerfresh'),
            esc_attr_x('Buy Now', 'front-view', 'bakerfresh')
        );
        ?>
    </div>
    <?php
	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>

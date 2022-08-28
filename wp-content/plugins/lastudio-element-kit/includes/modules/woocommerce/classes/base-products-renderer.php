<?php
namespace LaStudioKitThemeBuilder\Modules\Woocommerce\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Base_Products_Renderer extends \WC_Shortcode_Products {

    protected $settings = [];

    private static $has_init = false;

	/**
	 * Override original `get_content` that returns an HTML wrapper even if no results found.
	 *
	 * @return string Products HTML
	 */
	public function get_content() {
		$result = $this->get_query_results();

		if ( empty( $result->total ) && $this->get_type() !== 'current_query' ) {
			return '';
		}

		$layout = !empty($this->settings['layout']) ? $this->settings['layout'] : 'grid';
		$preset = !empty($this->settings[ $layout.'_style']) ? $this->settings[ $layout.'_style'] : '1';

		$classes = [
            'products',
            'ul_products',
            'lakit-products__list',
            'products-' . $layout,
            'products-' . $layout . '-' . $preset,
        ];
        if(!empty($this->settings['enable_carousel']) && filter_var($this->settings['enable_carousel'], FILTER_VALIDATE_BOOLEAN)){
            $classes[] = 'swiper-wrapper';
        }
        else{
            $classes[] = 'col-row';
        }

		$content = parent::get_content();

        $content = str_replace( '<ul class="products', '<ul class="'.esc_attr(join(' ', $classes)) , $content );

		return $content;
	}

    /**
     * Get wrapper classes.
     *
     * @since  3.2.0
     * @param  int $columns Number of columns.
     * @return array
     */
    protected function get_wrapper_classes( $columns ) {
        $classes = array( 'woocommerce' );

        $classes[] = $this->attributes['class'];

        if(!empty($this->settings['unique_id'])){
            $classes[] = 'lakit_wc_widget_' . $this->settings['unique_id'];
        }
        if( $this->type === 'current_query' ){
            $classes[] = 'lakit_wc_widget_current_query';
        }

        return $classes;
    }

    protected function override_hook_to_init(){
        add_action('lastudio-kit/products/before_render', [ $this, 'override_hook' ] );
        add_action( "woocommerce_shortcode_before_{$this->type}_loop", [ $this, 'setup_before_loop' ]);
    }

    public function setup_before_loop(){
        $layout = !empty($this->settings['layout']) ? $this->settings['layout'] : 'grid';
        $preset = !empty($this->settings[ $layout.'_style']) ? $this->settings[ $layout.'_style'] : '1';

        $allow_extra_filters = false;

        if( !empty($this->settings['allow_order']) && !empty($this->settings['show_result_count']) && $this->settings['allow_order'] === 'yes' && $this->settings['show_result_count'] === 'yes' ) {
            $allow_extra_filters = true;
        }
        wc_set_loop_prop('lakit_loop_allow_extra_filters', $allow_extra_filters );


        $enable_carousel = false;
        if(!empty($this->settings['enable_carousel']) && filter_var($this->settings['enable_carousel'], FILTER_VALIDATE_BOOLEAN)){
            $enable_carousel = true;
        }

        $before = '';

        if(!empty($this->settings['heading'])){
            $html_tag = !empty($this->settings['html_tag']) ? $this->settings['html_tag'] : 'div';
            $html_tag = lastudio_kit_helper()->validate_html_tag($html_tag);
            $before .= sprintf('<div class="clear"></div><%1$s class="lakit-heading"><span>%2$s</span></%1$s>', $html_tag, $this->settings['heading']);
        }

        $container_attributes = [];
        $container_classes = ['lakit-products'];
        $wrapper_classes = ['lakit-products__list_wrapper'];
        $loop_item_classes = [];
        $loop_item_classes[] = 'lakit-product';
        $loop_item_classes[] = 'product_item';

        if($enable_carousel){
            $container_classes[] = 'lakit-carousel';
            $carousel_settings = [];
            if(!empty($this->settings['lakit_extra_settings']['carousel_settings'])){
                $carousel_settings = $this->settings['lakit_extra_settings']['carousel_settings'];
            }
            $container_attributes[] = 'data-slider_options="'. esc_attr( json_encode($carousel_settings) ) .'"';
            $container_attributes[] = 'dir="'. (is_rtl() ? 'rtl' : 'ltr') .'"';
            $loop_item_classes[] = 'swiper-slide';
        }
        elseif(!empty($this->settings['lakit_extra_settings']['masonry_settings'])){
            $container_classes[] = 'lakit-masonry-wrapper';
            $container_attributes[] = $this->settings['lakit_extra_settings']['masonry_settings'];
        }
        if(!$enable_carousel){
            $loop_item_classes[] = lastudio_kit_helper()->col_new_classes('columns', $this->settings);
        }

        if( $this->type === 'current_query' ){
            $container_attributes[] = 'data-widget_current_query="yes"';
        }

        $before .= '<div class="'.esc_attr( join(' ', $container_classes) ).'" '. join(' ', $container_attributes) .'>';
        if($enable_carousel){
            $before .= '<div class="lakit-carousel-inner">';
            $wrapper_classes[] = 'swiper-container';
        }

        wc_set_loop_prop('lakit_loop_item_classes', $loop_item_classes );

        $has_masonry_filter = false;

        if(!empty($this->settings['lakit_extra_settings']['masonry_filter'])){
            $before .= $this->settings['lakit_extra_settings']['masonry_filter'];

            $has_masonry_filter = true;
        }

        $before .= '<div class="'.esc_attr(join(' ', $wrapper_classes)).'">';

        wc_set_loop_prop('lakit_loop_before', $before );
        wc_set_loop_prop('lakit_has_masonry_filter', $has_masonry_filter );

        $after = '</div>';
        if($enable_carousel){
            $after .= '</div>';
            if(!empty($this->settings['lakit_extra_settings']['carousel_dot_html'])){
                $after .= $this->settings['lakit_extra_settings']['carousel_dot_html'];
            }
            if(!empty($this->settings['lakit_extra_settings']['carousel_arrow_html'])){
                $after .= $this->settings['lakit_extra_settings']['carousel_arrow_html'];
            }
            if(!empty($this->settings['lakit_extra_settings']['carousel_scrollbar_html'])){
                $after .= $this->settings['lakit_extra_settings']['carousel_scrollbar_html'];
            }
        }
        $after .= '</div>';

        wc_set_loop_prop('lakit_loop_after', $after );

        wc_set_loop_prop('lakit_layout', $layout);
        wc_set_loop_prop('lakit_preset', $preset);
        wc_set_loop_prop('lakit_type', $this->type );
        wc_set_loop_prop('lakit_enable_carousel', $enable_carousel );

        $item_html_tag = !empty($this->settings['item_html_tag']) ? $this->settings['item_html_tag'] : 'h2';
        wc_set_loop_prop('lakit_item_html_tag', $item_html_tag );

        $image_size = 'woocommerce_thumbnail';
        $enable_alt_image = false;
        $enable_custom_image_size = !empty($this->settings['enable_custom_image_size']) && filter_var($this->settings['enable_custom_image_size'], FILTER_VALIDATE_BOOLEAN);

        if($enable_custom_image_size && !empty($this->settings['image_size'])){
            $image_size = $this->settings['image_size'];
        }
        if(!empty($this->settings['enable_alt_image']) && filter_var( $this->settings['enable_alt_image'], FILTER_VALIDATE_BOOLEAN )){
            $enable_alt_image = true;
        }

        wc_set_loop_prop('lakit_enable_alt_image', $enable_alt_image );
        wc_set_loop_prop('lakit_image_size', $image_size );

    }

    public function override_hook(){

        add_filter( 'woocommerce_pagination_args', [ $this, 'woocommerce_pagination_args' ], 1001  );
        if( !self::$has_init ){
            self::$has_init = true;
            $this->override_loop_hook();
        }
    }

    public function woocommerce_pagination_args( $args ){
        if( $this->type == 'products' && !empty($this->settings['unique_id']) ){
            $page_key = 'product-page-' . $this->settings['unique_id'];
            $args['base'] = esc_url_raw( add_query_arg( $page_key, '%#%', false ) );
            $args['format'] = '?'.$page_key.'=%#%';
        }
        return $args;
    }

    private function override_loop_hook(){
        if( ! lastudio_kit()->get_theme_support('lastudio-kit-woo::product-loop') ){

            add_action('lastudio-kit/products/action/shop_loop_item_action_top', 'woocommerce_template_loop_add_to_cart', 10);
            add_action('lastudio-kit/products/action/shop_loop_item_action', 'woocommerce_template_loop_add_to_cart', 10);

            remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open');
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
            remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
            remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
            remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

            add_action( 'woocommerce_before_shop_loop_item', [ $this, 'loop_item_open' ], -1001 );
            add_action( 'woocommerce_after_shop_loop_item', [ $this, 'loop_item_close' ], 1001 );

            add_action('woocommerce_before_shop_loop_item_title', [ $this, 'loop_item_thumbnail_open' ], -1001 );
            add_action('woocommerce_before_shop_loop_item_title', [ $this, 'loop_item_thumbnail_close' ], 1001 );

            add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', -101 );
            add_action('woocommerce_before_shop_loop_item_title', [ $this, 'add_product_thumbnails_to_loop' ], 15 );
            add_action('woocommerce_before_shop_loop_item_title', [ $this, 'loop_item_thumbnail_overlay' ], 100 );
            add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 101 );

            add_action('woocommerce_shop_loop_item_title', [ $this, 'loop_item_info_open' ], -101 );
            add_action('woocommerce_shop_loop_item_title', [ $this, 'loop_item_add_product_title' ], 10 );
            add_action('woocommerce_after_shop_loop_item', [ $this, 'loop_item_info_close' ], 101 );

            remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
            add_action( 'woocommerce_after_shop_loop', [ $this, 'woocommerce_pagination' ], 10 );
        }
    }

    public function loop_item_open(){
        echo '<div id="item" class="product_item--inner">';
    }
    public function loop_item_close(){
        echo '</div>';
    }
    public function loop_item_thumbnail_open(){
        echo '<div id="product-image" class="product_item--thumbnail">';
            echo '<div class="product_item--thumbnail-holder">';
    }
    public function loop_item_thumbnail_close(){
            echo '</div>';
            echo '<div class="product_item_thumbnail_action product_item--action">';
                echo '<div class="wrap-addto">';
                    do_action('lastudio-kit/products/action/shop_loop_item_action_top');
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }
    public function loop_item_thumbnail_overlay(){
        echo '<span class="item--overlay"></span>';
    }

    public function loop_item_info_open(){
        echo '<div id="product_item_info" class="product_item--info">';
            echo '<div id="product_item_info_inner" class="product_item--info-inner">';
    }
    public function loop_item_info_close(){
            echo '</div>';
            echo '<div class="product_item--info-action product_item--action">';
                echo '<div class="wrap-addto">';
                    do_action('lastudio-kit/products/action/shop_loop_item_action');
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }
    public function loop_item_add_product_title(){
        $html_tag = wc_get_loop_prop('lakit_item_html_tag', 'h2');
        $html_tag = lastudio_kit_helper()->validate_html_tag($html_tag);
        the_title( sprintf( '<%2$s class="product_item--title"><a href="%1s">', esc_url( get_the_permalink() ), $html_tag ), sprintf('</a></%1$s>', $html_tag) );
    }

    public function add_product_thumbnails_to_loop(){
        $image_size = wc_get_loop_prop('lakit_image_size', 'woocommerce_thumbnail');
        $enable_alt_image = wc_get_loop_prop('lakit_enable_alt_image', false);

        global $product;

        $output = '<div class="figure__object_fit p_img-first">'.woocommerce_get_product_thumbnail( $image_size ).'</div>';
        if($enable_alt_image){
            $gallery_image_ids = $product->get_gallery_image_ids();
            if(!empty($gallery_image_ids[0])){
                $image_url = wp_get_attachment_image_url($gallery_image_ids[0], $image_size);
                $output .= '<div class="figure__object_fit p_img-second">'. sprintf('<div style="background-image: url(\'%1$s\')"></div>', esc_url( $image_url )) .'</div>';
            }
        }
        echo $output;
    }

    public function woocommerce_pagination(){
        ob_start();
        woocommerce_pagination();
        $output = ob_get_clean();

        $output = str_replace('woocommerce-pagination', 'woocommerce-pagination lakit-pagination clearfix lakit-ajax-pagination', $output);

        echo $output;
    }

}

<?php

/**
 * Class: LaStudioKit_Woo_Pages
 * Name: WooCommerce Pages
 * Slug: lakit-woopages
 */

namespace Elementor;

if (!defined('WPINC')) {
    die;
}

/**
 * Woo_Pages Widget
 */
class LaStudioKit_Woo_Pages extends LaStudioKit_Base {

    protected function enqueue_addon_resources(){
        $this->add_style_depends( 'lastudio-kit-woocommerce' );
        $this->add_script_depends('lastudio-kit-base' );
    }

    public function get_name() {
        return 'lakit-woopages';
    }

    public function get_categories() {
        return [ 'lastudiokit-woocommerce' ];
    }

    public function get_keywords() {
        return [
            'woocommerce',
            'shop',
            'store',
            'cart',
            'checkout',
            'account',
            'order tracking',
            'shortcode',
            'product',
        ];
    }

    protected function get_widget_title() {
        return esc_html__( 'WooCommerce Pages', 'lastudio-kit' );
    }

    public function get_icon() {
        return 'eicon-product-pages';
        return 'lastudio-kit-icon-woocommerce-pages';
    }

    public function add_product_post_class( $classes ) {
        $classes[] = 'product';

        return $classes;
    }

    public function add_products_post_class_filter() {
        add_filter( 'post_class', [ $this, 'add_product_post_class' ] );
    }

    public function remove_products_post_class_filter() {
        remove_filter( 'post_class', [ $this, 'add_product_post_class' ] );
    }

    protected function register_controls() {
        $this->_start_controls_section(
            'section_product',
            [
                'label' => esc_html__( 'Element', 'lastudio-kit' ),
            ]
        );

        $page_options = [
            '' => '— ' . esc_html__( 'Select', 'lastudio-kit' ) . ' —',
            'woocommerce_cart' => esc_html__( 'Cart Page', 'lastudio-kit' ),
            'product_page' => esc_html__( 'Single Product Page', 'lastudio-kit' ),
            'woocommerce_checkout' => esc_html__( 'Checkout Page', 'lastudio-kit' ),
            'woocommerce_order_tracking' => esc_html__( 'Order Tracking Form', 'lastudio-kit' ),
            'woocommerce_my_account' => esc_html__( 'My Account', 'lastudio-kit' ),
        ];

        if(lastudio_kit()->get_theme_support('lastudio-kit')){
            $page_options['la_wishlist'] = esc_html__( 'LaStudio Wishlist', 'lastudio-kit' );
            $page_options['la_compare'] = esc_html__( 'LaStudio Compare', 'lastudio-kit' );
        }

        $this->_add_control(
            'element',
            [
                'label' => esc_html__( 'Page', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'options' => $page_options,
            ]
        );

        $this->_add_control(
            'product_id',
            [
                'label' => esc_html__( 'Product', 'lastudio-kit' ),
                'type' => 'lastudiokit-query',
                'options' => [],
                'label_block' => true,
                'autocomplete' => [
                    'object' => 'post',
                    'query' => [
                        'post_type' => [ 'product' ],
                    ],
                ],
                'condition' => [
                    'element' => [ 'product_page' ],
                ],
            ]
        );

        $this->_end_controls_section();
    }
    
    private function get_shortcode() {
        $settings = $this->get_settings();

        switch ( $settings['element'] ) {
            case '':
                return '';
                break;

            case 'product_page':
                if ( ! empty( $settings['product_id'] ) ) {
                    $product_data = get_post( $settings['product_id'] );
                    $product = ! empty( $product_data ) && in_array( $product_data->post_type, [ 'product', 'product_variation' ] ) ? wc_setup_product_data( $product_data ) : false;
                }

                if ( empty( $product ) && current_user_can( 'manage_options' ) ) {
                    return esc_html__( 'Please set a valid product', 'lastudio-kit' );
                }

                $this->add_render_attribute( 'shortcode', 'id', $settings['product_id'] );
                break;

            case 'woocommerce_cart':
            case 'woocommerce_checkout':
            case 'woocommerce_order_tracking':
                break;
        }

        $shortcode = sprintf( '[%s %s]', $settings['element'], $this->get_render_attribute_string( 'shortcode' ) );

        return $shortcode;
    }

    protected function render() {
        $shortcode = $this->get_shortcode();

        if ( empty( $shortcode ) ) {
            return;
        }

        $element = $this->get_settings_for_display('element');

        if( $element == 'woocommerce_cart' ) {
            $this->add_render_attribute( '_wrapper', 'class', 'woocommerce-cart' );
        }

        $this->add_products_post_class_filter();

        $html = do_shortcode( $shortcode );

        if ( 'woocommerce_checkout' === $this->get_settings( 'element' ) && ('<div class="woocommerce"></div>' === $html || '<div class="woocommerce"><div class="woocommerce-notices-wrapper"></div></div>' === $html) ) {
            $html = '<div class="woocommerce">';
            ob_start();
            wc_get_template( 'cart/cart-empty.php' );
            $html .= ob_get_clean();
            $html .= '</div>';
        }

        echo $html;

        $this->remove_products_post_class_filter();
    }

    public function render_plain_content() {
        echo $this->get_shortcode();
    }

}
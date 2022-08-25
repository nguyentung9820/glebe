<?php

/**
 * Class: LaStudioKit_Woo_Single_Product_Stock
 * Name: Product Stock
 * Slug: lakit-wooproduct-stock
 */

namespace Elementor;

if (!defined('WPINC')) {
    die;
}

/**
 * Woo Widget
 */
class LaStudioKit_Woo_Single_Product_Stock extends LaStudioKit_Base {

    protected function enqueue_addon_resources(){
        $this->add_style_depends( 'lastudio-kit-woocommerce' );
        $this->add_script_depends('lastudio-kit-base' );
    }

    public function get_name() {
        return 'lakit-wooproduct-stock';
    }

    public function get_categories() {
        return [ 'lastudiokit-woo-product' ];
    }

    public function get_keywords() {
        return [ 'woocommerce', 'shop', 'store', 'stock', 'quantity', 'product' ];
    }

    public function get_widget_title() {
        return esc_html__( 'Product Stock', 'lastudio-kit' );
    }

    public function get_icon() {
        return 'eicon-product-stock';
        return 'lastudio-kit-icon-woocommerce-pages';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_product_stock_style',
            [
                'label' => esc_html__( 'Style', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'wc_style_warning',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'lastudio-kit' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.woocommerce {{WRAPPER}} .stock' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => esc_html__( 'Typography', 'lastudio-kit' ),
                'selector' => '.woocommerce {{WRAPPER}} .stock',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $product;
        $product = wc_get_product();

        if ( empty( $product ) ) {
            return;
        }

        echo wc_get_stock_html( $product );
    }

    public function render_plain_content() {}

}
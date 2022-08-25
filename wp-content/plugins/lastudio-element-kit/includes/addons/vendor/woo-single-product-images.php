<?php

/**
 * Class: LaStudioKit_Woo_Single_Product_Images
 * Name: Product Images
 * Slug: lakit-wooproduct-images
 */

namespace Elementor;

if (!defined('WPINC')) {
    die;
}

/**
 * Woo Widget
 */
class LaStudioKit_Woo_Single_Product_Images extends LaStudioKit_Base {

    protected function enqueue_addon_resources(){
        $this->add_style_depends( 'lastudio-kit-woocommerce' );
        $this->add_script_depends('lastudio-kit-base' );
    }

    public function get_name() {
        return 'lakit-wooproduct-images';
    }

    public function get_categories() {
        return [ 'lastudiokit-woo-product' ];
    }

    public function get_keywords() {
        return [ 'woocommerce', 'shop', 'store', 'image', 'product', 'gallery', 'lightbox' ];
    }

    public function get_widget_title() {
        return esc_html__( 'Product Images', 'lastudio-kit' );
    }

    public function get_icon() {
        return 'eicon-product-images';
        return 'lastudio-kit-icon-woocommerce-pages';
    }

    protected function register_controls() {

        if( lastudio_kit()->get_theme_support('lastudio-kit') ){
            $this->register_lastudio_theme_controls();
        }
        else{
            $this->start_controls_section(
                'section_product_gallery_style',
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
                'layout_type',
                [
                    'label' => esc_html__( 'Gallery Layout', 'lastudio-kit' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'wc' => esc_html__('Default', 'lastudio-kit')
                    ],
                    'default' => 'wc',
                ]
            );

            $this->add_control(
                'sale_flash',
                [
                    'label' => esc_html__( 'Sale Flash', 'lastudio-kit' ),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__( 'Show', 'lastudio-kit' ),
                    'label_off' => esc_html__( 'Hide', 'lastudio-kit' ),
                    'render_type' => 'template',
                    'return_value' => 'yes',
                    'default' => 'yes',
                    'prefix_class' => '',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'image_border',
                    'selector' => '.woocommerce {{WRAPPER}} .woocommerce-product-gallery__trigger + .woocommerce-product-gallery__wrapper,
				.woocommerce {{WRAPPER}} .flex-viewport, .woocommerce {{WRAPPER}} .flex-control-thumbs img',
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'image_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'lastudio-kit' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '.woocommerce {{WRAPPER}} .woocommerce-product-gallery__trigger + .woocommerce-product-gallery__wrapper,
					.woocommerce {{WRAPPER}} .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                    ],
                ]
            );

            $this->add_control(
                'spacing',
                [
                    'label' => esc_html__( 'Spacing', 'lastudio-kit' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', 'em' ],
                    'selectors' => [
                        '.woocommerce {{WRAPPER}} .flex-viewport:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            $this->add_control(
                'heading_thumbs_style',
                [
                    'label' => esc_html__( 'Thumbnails', 'lastudio-kit' ),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'thumbs_border',
                    'selector' => '.woocommerce {{WRAPPER}} .flex-control-thumbs img',
                ]
            );

            $this->add_responsive_control(
                'thumbs_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'lastudio-kit' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '.woocommerce {{WRAPPER}} .flex-control-thumbs img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                    ],
                ]
            );

            $this->add_control(
                'spacing_thumbs',
                [
                    'label' => esc_html__( 'Spacing', 'lastudio-kit' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', 'em' ],
                    'selectors' => [
                        '.woocommerce {{WRAPPER}} .flex-control-thumbs li' => 'padding-right: calc({{SIZE}}{{UNIT}} / 2); padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-bottom: {{SIZE}}{{UNIT}}',
                        '.woocommerce {{WRAPPER}} .flex-control-thumbs' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2)',
                    ],
                ]
            );

            $this->end_controls_section();

            do_action('lastudiokit/woocommerce/single/setting/product-images', $this);
        }
    }

    protected function register_lastudio_theme_controls()
    {
        $this->start_controls_section(
            'section_product_gallery_layout',
            [
                'label' => esc_html__( 'Setting', 'lastudio-kit' ),
            ]
        );
        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Gallery Layout', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '1' => esc_html__('Thumbnail at bottom', 'lastudio-kit'),
                    '2' => esc_html__('Thumbnail at left', 'lastudio-kit'),
                    '3' => esc_html__('Thumbnail at right', 'lastudio-kit'),
                    '4' => esc_html__('No thumbnail', 'lastudio-kit'),
                    '5' => esc_html__('Metro', 'lastudio-kit'),
                    '6' => esc_html__('Flat', 'lastudio-kit'),
                    'wc' => esc_html__('Default from WooCommerce', 'lastudio-kit'),
                ],
                'default' => '1',
            ]
        );
        $this->add_responsive_control(
            'gallery_column',
            [
                'label' => esc_html__( 'Gallery Column', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 1,
                        'max' => 10,
                        'step' => 1
                    ),
                ),
                'selectors' => [
                    '{{WRAPPER}}' => '--singleproduct-image-column: {{SIZE}}',
                ],
                'condition' => [
                    'layout_type' => ['1', '4']
                ]
            ]
        );
        $this->add_responsive_control(
            'thumb_width',
            [
                'label' => esc_html__( 'Thumbnail Width', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vh', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}}' => '--singleproduct-thumbs-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'layout_type' => ['2','3']
                ]
            ]
        );
        $this->add_control(
            'sale_flash',
            [
                'label' => esc_html__( 'Sale Flash', 'lastudio-kit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'lastudio-kit' ),
                'label_off' => esc_html__( 'Hide', 'lastudio-kit' ),
                'render_type' => 'template',
                'return_value' => 'yes',
                'default' => 'yes'
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
            'section_product_image_style',
            [
                'label' => esc_html__( 'Main gallery', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'main_image_spacing',
            [
                'label' => esc_html__( 'Main image spacing', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}}' => '--singleproduct-image-spacing: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'thumb_image_spacing',
            [
                'label' => esc_html__( 'Thumbnail spacing', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}}' => '--singleproduct-thumb-spacing: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_big_height',
            [
                'label' => esc_html__( 'Image big height', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vh', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}}' => '--singleproduct-image-height2: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'layout_type' => ['5']
                ]
            ]
        );
        $this->add_responsive_control(
            'image_small_height',
            [
                'label' => esc_html__( 'Image small height', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vh', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}}' => '--singleproduct-image-height: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'layout_type' => ['5']
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'        => 'gallery_border',
                'label'       => esc_html__( 'Border', 'lastudio-kit' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .zoominner',
            )
        );

        $this->add_responsive_control(
            'gallery_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'lastudio-kit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .zoominner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'gallery_shadow',
                'selector' => '{{WRAPPER}} .zoominner',
            )
        );

        $this->add_responsive_control(
            'gallery_padding',
            array(
                'label'      => esc_html__( 'Padding', 'lastudio-kit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .zoominner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_thumbnail',
            [
                'label' => esc_html__( 'Thumbnail', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'        => 'thumb_border',
                'label'       => esc_html__( 'Border', 'lastudio-kit' ),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .flex-control-thumbs li img',
            )
        );

        $this->add_responsive_control(
            'thumb_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'lastudio-kit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .flex-control-thumbs li img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'thumb_shadow',
                'selector' => '{{WRAPPER}} .flex-control-thumbs li img',
            )
        );

        $this->add_responsive_control(
            'thumb_padding',
            array(
                'label'      => esc_html__( 'Padding', 'lastudio-kit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .zoominner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );
        $this->end_controls_section();
    }

    public function render() {
        global $product;

        $product = wc_get_product();

        if ( empty( $product ) ) {
            return;
        }

        $layout_type = $this->get_settings_for_display('layout_type');

        echo '<div class="lakit-product-images layout-type-'.esc_attr($layout_type).'">';

        if ( 'yes' === $this->get_settings_for_display('sale_flash') ) {
            wc_get_template( 'loop/sale-flash.php' );
        }

        wc_get_template( 'single-product/product-image.php' );

        // On render widget from Editor - trigger the init manually.
        if ( wp_doing_ajax() ) {
            ?>
            <script>
                jQuery( '.woocommerce-product-gallery' ).each( function() {
                    jQuery( this ).wc_product_gallery();
                } );
                jQuery(document).trigger('lastudiokit/woocommerce/single/product-images');
            </script>
            <?php
        }

        echo '</div>';
    }

}
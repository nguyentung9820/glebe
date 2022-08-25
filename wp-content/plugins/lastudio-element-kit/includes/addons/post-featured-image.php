<?php

/**
 * Class: LaStudioKit_Post_Featured_Image
 * Name: Post Featured Image
 * Slug: lakit-post-featured-image
 */

namespace Elementor;

if (!defined('WPINC')) {
    die;
}


/**
 * Post Featured Image Widget
 */
class LaStudioKit_Post_Featured_Image extends LaStudioKit_Base {

    protected function enqueue_addon_resources(){
        $this->add_style_depends( 'lastudio-kit-base' );
    }

    public function get_name() {
        return 'lakit-post-featured-image';
    }

    protected function get_widget_title() {
        return esc_html__( 'Post Featured Image', 'lastudio-kit' );
    }

    public function get_icon() {
        return 'eicon-featured-image';
    }

    public function get_categories() {
        return [ 'lastudiokit-builder' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Post Featured Image', 'lastudio-kit' ),
            ]
        );

        $this->add_control(
            'preview',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => get_the_post_thumbnail(),
                'separator' => 'none',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'size',
                'label' => __( 'Image Size', 'lastudio-kit' ),
                'default' => 'large',
                'exclude' => [ 'custom' ],
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __( 'Alignment', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'lastudio-kit' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'lastudio-kit' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'lastudio-kit' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'lastudio-kit' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'link_to',
            [
                'label' => __( 'Link to', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __( 'None', 'lastudio-kit' ),
                    'home' => __( 'Home URL', 'lastudio-kit' ),
                    'post' => esc_html__( 'Post URL', 'lastudio-kit' ),
                    'file' => __( 'Media File URL', 'lastudio-kit' ),
                    'custom' => __( 'Custom URL', 'lastudio-kit' ),
                ],
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __( 'Link to', 'lastudio-kit' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'lastudio-kit' ),
                'condition' => [
                    'link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );

        $this->add_control(
            'enable_equal_height',
            [
                'label'     => esc_html__( 'Equal Height?', 'lastudio-kit' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'enable' => esc_html__( 'Enable', 'lastudio-kit' ),
                    'disable' => esc_html__( 'Disable', 'lastudio-kit' ),
                ],
                'default'   => 'disable',
                'prefix_class'  => 'lakit-equal-height-',
                'selectors' => [
                    '{{WRAPPER}}.lakit-equal-height-enable .elementor-widget-container, {{WRAPPER}}.lakit-equal-height-enable .lakit-post-featured-image, {{WRAPPER}}.lakit-equal-height-enable .lakit-post-featured-image img' => 'height: 100%;',
                ]
            ]
        );
        $this->add_responsive_control(
            'image_pos',
            array(
                'type'       => 'select',
                'label'      => esc_html__( 'Images Position', 'lastudio-kit' ),
                'default'    => 'center',
                'options'    => [
                    'center'    => esc_html__( 'Center', 'lastudio-kit' ),
                    'top'       => esc_html__( 'Top', 'lastudio-kit' ),
                    'bottom'    => esc_html__( 'Bottom', 'lastudio-kit' ),
                ],
                'condition' => [
                    'enable_equal_height' => 'enable'
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-post-featured-image img' => 'object-position: {{VALUE}}; background-position: {{VALUE}}'
                ],
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Post Featured Image', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'space',
            [
                'label' => __( 'Size (%)', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-post-featured-image img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'custom_height',
            array(
                'label'        => esc_html__( 'Enable Custom Image Height', 'lastudio' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'lastudio-kit' ),
                'label_off'    => esc_html__( 'No', 'lastudio-kit' ),
                'return_value' => 'true',
                'default'      => ''
            )
        );

        $this->add_responsive_control(
            'height',
            array(
                'label' => esc_html__( 'Image Height', 'lastudio-kit' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 100,
                        'max' => 1000,
                    ),
                    '%' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                    'vh' => array(
                        'min' => 0,
                        'max' => 100,
                    )
                ),
                'size_units' => array( 'px', '%', 'em', 'vw', 'vh' ),
                'default' => [
                    'size' => 300,
                    'unit' => 'px'
                ],
                'selectors' => array(
                    '{{WRAPPER}} .lakit-post-featured-image' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .lakit-post-featured-image img' => 'position: absolute; width: 100%; height: 100%; left: 0; top: 0; object-fit: cover'
                ),
                'render_type' => 'template',
                'condition' => [
                    'custom_height!' => ''
                ]
            )
        );

        $this->add_responsive_control(
            'opacity',
            [
                'label' => __( 'Opacity (%)', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-post-featured-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'angle',
            [
                'label' => __( 'Angle (deg)', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'deg' ],
                'default' => [
                    'unit' => 'deg',
                    'size' => 0,
                ],
                'range' => [
                    'deg' => [
                        'max' => 360,
                        'min' => -360,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-post-featured-image img' => '-webkit-transform: rotate({{SIZE}}deg); -moz-transform: rotate({{SIZE}}deg); -ms-transform: rotate({{SIZE}}deg); -o-transform: rotate({{SIZE}}deg); transform: rotate({{SIZE}}deg);',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => __( 'Hover Animation', 'lastudio-kit' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'label' => __( 'Image Border', 'lastudio-kit' ),
                'selector' => '{{WRAPPER}} .lakit-post-featured-image img',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __( 'Border Radius', 'lastudio-kit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-post-featured-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .lakit-post-featured-image img',
            ]
        );

        $this->add_control(
            'img_zidex',
            [
                'label' => __( 'Wrap z-Index', 'lastudio-kit' ),
                'type' => Controls_Manager::NUMBER,
                'min'     => -10,
                'max'     => 100000,
                'selectors' => [
                    '{{WRAPPER}}' => 'z-index: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();

        $image_size = $settings['size_size'];
        $featured_image = get_the_post_thumbnail( null, $image_size );

        if ( empty( $featured_image ) )
            return;

        switch ( $settings['link_to'] ) {
            case 'custom' :
                if ( ! empty( $settings['link']['url'] ) ) {
                    $link = esc_url( $settings['link']['url'] );
                } else {
                    $link = false;
                }
                break;

            case 'file' :
                $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), $image_size );
                $link = esc_url( $image_url[0] );
                break;

            case 'post' :
                $link = esc_url( get_the_permalink() );
                break;

            case 'home' :
                $link = esc_url( get_home_url() );
                break;

            case 'none' :
            default:
                $link = false;
                break;
        }
        $target = $settings['link']['is_external'] ? 'target="_blank"' : '';

        $animation_class = ! empty( $settings['hover_animation'] ) ? 'elementor-animation-' . $settings['hover_animation'] : '';

        $html = '<div class="lakit-post-featured-image ' . $animation_class . '">';
        if ( $link ) {
            $html .= sprintf( '<a href="%1$s" %2$s>%3$s</a>', $link, $target, $featured_image );
        } else {
            $html .= $featured_image;
        }
        $html .= '</div>';

        echo $html;

    }

    protected function content_template() {

        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        ?>
        <#
        var featured_images = [];
        <?php
        $all_image_sizes = Group_Control_Image_Size::get_all_image_sizes();
        foreach ( $all_image_sizes as $key => $value ) {
            printf( 'featured_images[ "%1$s" ] = \'%2$s\';', $key, get_the_post_thumbnail( null, $key ) );
        }
        printf( 'featured_images[ "full" ] = \'%2$s\';', $key, get_the_post_thumbnail( null, 'full' ) );
        ?>
        var featured_image = featured_images[ settings.size_size ];

        var link_url;
        switch( settings.link_to ) {
        case 'custom':
        link_url = settings.link.url;
        break;
        case 'file':
        link_url = '<?php echo esc_url( !empty($image_url[0]) ? $image_url[0] : '' ); ?>';
        break;
        case 'post':
        link_url = '<?php echo esc_url( get_the_permalink() ); ?>';
        break;
        case 'home':
        link_url = '<?php echo esc_url( get_home_url() ); ?>';
        break;
        case 'none':
        default:
        link_url = false;
        }

        var animation_class = '';
        if ( '' !== settings.hover_animation ) {
        animation_class = 'elementor-animation-' + settings.hover_animation;
        }

        var html = '<div class="lakit-post-featured-image ' + animation_class + '">';
            if ( link_url ) {
            html += '<a href="' + link_url + '">' + featured_image + '</a>';
            } else {
            html += featured_image;
            }
            html += '</div>';

        print( html );
        #>
        <?php

    }
    
}
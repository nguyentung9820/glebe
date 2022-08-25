<?php
/**
 * Class: LaStudioKit_Slides
 * Name: Slides
 * Slug: lakit-slides
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class LaStudioKit_Slides extends LaStudioKit_Base {

    protected function enqueue_addon_resources(){

        wp_register_style( $this->get_name(), lastudio_kit()->plugin_url('assets/css/addons/slides.css'), ['lastudio-kit-base'], lastudio_kit()->get_version());

        $this->add_style_depends( $this->get_name() );
        $this->add_script_depends( 'lastudio-kit-base' );
    }

    public function get_name() {
        return 'lakit-slides';
    }

    public function get_widget_title() {
        return esc_html__( 'Slides', 'lastudio-kit' );
    }

    public function get_icon() {
        return 'lastudio-kit-icon-slides';
    }

    public function get_keywords() {
        return [ 'slides', 'carousel', 'image', 'title', 'slider' ];
    }

    protected function register_controls() {
        $this->_start_controls_section(
            'section_slides',
            [
                'label' => __( 'Slides', 'lastudio-kit' ),
            ]
        );

        $repeater = new Repeater();

        $repeater->start_controls_tabs( 'slides_repeater' );

        $repeater->start_controls_tab( 'background', [ 'label' => __( 'Background', 'lastudio-kit' ) ] );

        $repeater->add_control(
            'background_color',
            [
                'label' => __( 'Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#bbbbbb',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-bg' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'background_image',
            [
                'label' => _x( 'Image', 'Background Control', 'lastudio-kit' ),
                'type' => Controls_Manager::MEDIA,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-bg' => 'background-image: url({{URL}})',
                ],
            ]
        );

        $repeater->add_control(
            'background_size',
            [
                'label' => _x( 'Size', 'Background Control', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => _x( 'Cover', 'Background Control', 'lastudio-kit' ),
                    'contain' => _x( 'Contain', 'Background Control', 'lastudio-kit' ),
                    'auto' => _x( 'Auto', 'Background Control', 'lastudio-kit' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-bg' => 'background-size: {{VALUE}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_image[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'background_ken_burns',
            [
                'label' => __( 'Ken Burns Effect', 'lastudio-kit' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'separator' => 'before',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_image[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'zoom_direction',
            [
                'label' => __( 'Zoom Direction', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'in',
                'options' => [
                    'in' => __( 'In', 'lastudio-kit' ),
                    'out' => __( 'Out', 'lastudio-kit' ),
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_ken_burns',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'background_overlay',
            [
                'label' => __( 'Background Overlay', 'lastudio-kit' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'separator' => 'before',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_image[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'background_overlay_color',
            [
                'label' => __( 'Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.5)',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_overlay',
                            'value' => 'yes',
                        ],
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner .elementor-background-overlay' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'background_overlay_blend_mode',
            [
                'label' => __( 'Blend Mode', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'Normal', 'lastudio-kit' ),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'color-burn' => 'Color Burn',
                    'hue' => 'Hue',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'exclusion' => 'Exclusion',
                    'luminosity' => 'Luminosity',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'background_overlay',
                            'value' => 'yes',
                        ],
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner .elementor-background-overlay' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab( 'content', [ 'label' => __( 'Content', 'lastudio-kit' ) ] );

        $repeater->add_control(
            'subheading',
            [
                'label' => __( 'Sub Title', 'lastudio-kit' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Slide Sub-Heading', 'lastudio-kit' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'heading',
            [
                'label' => __( 'Title', 'lastudio-kit' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Slide Heading', 'lastudio-kit' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __( 'Description', 'lastudio-kit' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'lastudio-kit' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'subdescription1',
            [
                'label' => __( 'Sub-Description 1', 'lastudio-kit' ),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'button_text',
            [
                'label' => __( 'Button Text', 'lastudio-kit' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Click Here', 'lastudio-kit' ),
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => __( 'Link', 'lastudio-kit' ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'lastudio-kit' ),
            ]
        );

        $repeater->add_control(
            'link_click',
            [
                'label' => __( 'Apply Link On', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'slide' => __( 'Whole Slide', 'lastudio-kit' ),
                    'button' => __( 'Button Only', 'lastudio-kit' ),
                ],
                'default' => 'slide',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'link[url]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'subdescription2',
            [
                'label' => __( 'Sub-Description 2', 'lastudio-kit' ),
                'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
            ]
        );

        $repeater->end_controls_tab();

        $repeater->start_controls_tab( 'style', [ 'label' => __( 'Style', 'lastudio-kit' ) ] );

        $repeater->add_control(
            'el_class',
            [
                'label' => __( 'Item CSS Class', 'lastudio-kit' ),
                'type' => Controls_Manager::TEXT
            ]
        );

        $repeater->add_control(
            'custom_style',
            [
                'label' => __( 'Custom', 'lastudio-kit' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => __( 'Set custom style that will only affect this specific slide.', 'lastudio-kit' ),
            ]
        );

        $repeater->add_control(
            'bg_h_position',
            [
                'label' => __( 'Background Horizontal Position', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Left', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __( 'Right', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.swiper-slide' => 'justify-content: {{VALUE}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'horizontal_position',
            [
                'label' => __( 'Horizontal Position', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner .lakit-slide-content' => '{{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'left' => 'margin-right: auto',
                    'center' => 'margin: 0 auto',
                    'right' => 'margin-left: auto',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'vertical_position',
            [
                'label' => __( 'Vertical Position', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'top' => [
                        'title' => __( 'Top', 'lastudio-kit' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => __( 'Middle', 'lastudio-kit' ),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __( 'Bottom', 'lastudio-kit' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner' => 'align-items: {{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'text_align',
            [
                'label' => __( 'Text Align', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
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
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner' => 'text-align: {{VALUE}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'content_color',
            [
                'label' => __( 'Content Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner .lakit-slide-heading' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner .lakit-slide-description' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .lakit-slide-inner .lakit-slide-button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'custom_style',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->_add_control(
            'slides',
            [
                'label' => __( 'Slides', 'lastudio-kit' ),
                'type' => Controls_Manager::REPEATER,
                'show_label' => true,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'heading' => __( 'Slide 1 Heading', 'lastudio-kit' ),
                        'description' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'lastudio-kit' ),
                        'button_text' => __( 'Click Here', 'lastudio-kit' ),
                        'background_color' => '#833ca3',
                    ],
                    [
                        'heading' => __( 'Slide 2 Heading', 'lastudio-kit' ),
                        'description' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'lastudio-kit' ),
                        'button_text' => __( 'Click Here', 'lastudio-kit' ),
                        'background_color' => '#4054b2',
                    ],
                    [
                        'heading' => __( 'Slide 3 Heading', 'lastudio-kit' ),
                        'description' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'lastudio-kit' ),
                        'button_text' => __( 'Click Here', 'lastudio-kit' ),
                        'background_color' => '#1abc9c',
                    ],
                ],
                'title_field' => '{{{ heading }}}',
            ]
        );

        $this->_add_responsive_control(
            'slides_height',
            [
                'label' => __( 'Height', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 2000,
                    ]
                ],
                'default' => [
                    'size' => 400,
                ],
                'size_units' => [ 'px', 'vh', 'vw', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'content_animation',
            [
                'label' => __( 'Content Animation', 'lastudio-kit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'fadeInUp',
                'options' => [
                    '' => __( 'None', 'lastudio-kit' ),
                    'fadeInDown' => __( 'Down', 'lastudio-kit' ),
                    'fadeInUp' => __( 'Up', 'lastudio-kit' ),
                    'fadeInRight' => __( 'Right', 'lastudio-kit' ),
                    'fadeInLeft' => __( 'Left', 'lastudio-kit' ),
                    'zoomIn' => __( 'Zoom', 'lastudio-kit' ),
                ],
            ]
        );

        $this->add_control(
            'carousel_columns',
            [
                'type' => Controls_Manager::HIDDEN,
                'default' => '1',
            ]
        );

        $this->_end_controls_section();

        $this->register_carousel_section([], 'carousel_columns', false);

        $this->_start_controls_section(
            'section_style_slides',
            [
                'label' => __( 'Slides', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_responsive_control(
            'slidebg_width',
            [
                'label' => __( 'Background Width', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 2560,
                    ]
                ],
                'size_units' => [ '%', 'px' ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slides' => '--slide-bg-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .lakit-slides .swiper-cube-shadow' => 'display: none',
                    '{{WRAPPER}} .lakit-slides .swiper-slide-shadow-left' => 'display: none',
                    '{{WRAPPER}} .lakit-slides .swiper-slide-shadow-right' => 'display: none',
                ]
            ]
        );
        $this->_add_control(
            'slide_h_position',
            [
                'label' => __( 'Horizontal Position', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Left', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'flex-end' => [
                        'title' => __( 'Right', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-right',
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide' => 'justify-content: {{VALUE}}'
                ]
            ]
        );


        $this->_end_controls_section();

        $this->_start_controls_section(
            'section_style_content',
            [
                'label' => __( 'Content', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_responsive_control(
            'content_max_width',
            [
                'label' => __( 'Content Width', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1920,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ '%', 'px' ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slides' => '--slide-content-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .lakit-slide-content' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->_add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'content_bg',
                'selector' => '{{WRAPPER}} .lakit-slide-content',
            )
        );

        $this->_add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'        => 'content_border',
                'label'       => esc_html__( 'Border', 'lastudio-kit'),
                'placeholder' => '1px',
                'selector'    => '{{WRAPPER}} .lakit-slide-content',
            )
        );

        $this->_add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'content_shadow',
                'selector' => '{{WRAPPER}} .lakit-slide-content',
            )
        );

        $this->_add_responsive_control(
            'slides_padding',
            [
                'label' => __( 'Content Padding', 'lastudio-kit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%', 'vw', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->_add_responsive_control(
            'slides_margin',
            [
                'label' => __( 'Content Margin', 'lastudio-kit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%', 'vw', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->_add_control(
            'slides_horizontal_position',
            [
                'label' => __( 'Horizontal Position', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'lastudio-kit' ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'elementor--h-position-',
            ]
        );

        $this->_add_control(
            'slides_vertical_position',
            [
                'label' => __( 'Vertical Position', 'lastudio-kit' ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'top' => [
                        'title' => __( 'Top', 'lastudio-kit' ),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => __( 'Middle', 'lastudio-kit' ),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => __( 'Bottom', 'lastudio-kit' ),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'prefix_class' => 'elementor--v-position-',
            ]
        );

        $this->_add_control(
            'slides_text_align',
            [
                'label' => __( 'Text Align', 'lastudio-kit' ),
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
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-content' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->_end_controls_section();

        $this->_start_controls_section(
            'section_style_subtitle',
            [
                'label' => __( 'Sub Title', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_responsive_control(
            'subheading_spacing',
            [
                'label' => __( 'Spacing', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vw', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-inner .lakit-slide-subheading:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->_add_control(
            'subheading_color',
            [
                'label' => __( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-subheading' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->_add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subheading_typography',
                'selector' => '{{WRAPPER}} .lakit-slide-subheading',
            ]
        );

        $this->_end_controls_section();

        $this->_start_controls_section(
            'section_style_title',
            [
                'label' => __( 'Title', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_responsive_control(
            'heading_spacing',
            [
                'label' => __( 'Spacing', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vw', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-inner .lakit-slide-heading:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->_add_control(
            'heading_color',
            [
                'label' => __( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-heading' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->_add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .lakit-slide-heading',
            ]
        );

        $this->_end_controls_section();

        $this->_start_controls_section(
            'section_style_description',
            [
                'label' => __( 'Description', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_responsive_control(
            'description_spacing',
            [
                'label' => __( 'Spacing', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vw', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-inner .lakit-slide-description:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->_add_control(
            'description_color',
            [
                'label' => __( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-description' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->_add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .lakit-slide-description',
            ]
        );

        $this->_end_controls_section();

        $this->_start_controls_section(
            'section_style_subdescription1',
            [
                'label' => __( 'Sub Description 1', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_responsive_control(
            'subdescription1_spacing',
            [
                'label' => __( 'Spacing', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vw', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-inner .lakit-slide-subdescription1:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->_add_control(
            'subdescription1_color',
            [
                'label' => __( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-subdescription1' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->_add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subdescription1_typography',
                'selector' => '{{WRAPPER}} .lakit-slide-subdescription1',
            ]
        );

        $this->_end_controls_section();


        $this->_start_controls_section(
            'section_style_subdescription2',
            [
                'label' => __( 'Sub Description 2', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_responsive_control(
            'subdescription2_spacing',
            [
                'label' => __( 'Spacing', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', '%', 'vw', 'vh' ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-inner .lakit-slide-subdescription2' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->_add_control(
            'subdescription2_color',
            [
                'label' => __( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-subdescription2' => 'color: {{VALUE}}',

                ],
            ]
        );

        $this->_add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subdescription2_typography',
                'selector' => '{{WRAPPER}} .lakit-slide-subdescription2',
            ]
        );

        $this->_end_controls_section();

        $this->_start_controls_section(
            'section_style_button',
            [
                'label' => __( 'Button', 'lastudio-kit' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->_add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .lakit-slide-button'
            ]
        );

        $this->_add_responsive_control(
            'button_border_width',
            [
                'label' => __( 'Border Width', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->_add_responsive_control(
            'button_border_radius',
            [
                'label' => __( 'Border Radius', 'lastudio-kit' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'after',
            ]
        );
        $this->_add_responsive_control(
            'button_pd',
            array(
                'label'      => esc_html__( 'Padding', 'lastudio-kit'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .lakit-slide-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'separator' => 'before',
            )
        );

        $this->_start_controls_tabs( 'button_tabs' );

        $this->_start_controls_tab( 'normal', [ 'label' => __( 'Normal', 'lastudio-kit' ) ] );

        $this->_add_control(
            'button_text_color',
            [
                'label' => __( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->_add_control(
            'button_background_color',
            [
                'label' => __( 'Background Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->_add_control(
            'button_border_color',
            [
                'label' => __( 'Border Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->_end_controls_tab();

        $this->_start_controls_tab( 'hover', [ 'label' => __( 'Hover', 'lastudio-kit' ) ] );

        $this->_add_control(
            'button_hover_text_color',
            [
                'label' => __( 'Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->_add_control(
            'button_hover_background_color',
            [
                'label' => __( 'Background Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->_add_control(
            'button_hover_border_color',
            [
                'label' => __( 'Border Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lakit-slide-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->_end_controls_tab();

        $this->_end_controls_tabs();

        $this->_end_controls_section();

        $this->register_carousel_arrows_dots_style_section();
    }

    public function get_advanced_carousel_options( $carousel_columns = false, $widget_id = '', $settings = null ) {
        $opts = parent::get_advanced_carousel_options($carousel_columns, $widget_id, $settings);
        $content_animation = $this->get_settings_for_display('content_animation');
        $opts = array_merge([
            'content_selector' => '.lakit-slide-content',
            'content_effect_in' => $content_animation,
            'content_effect_out' => str_replace('In','Out', $content_animation),
        ], $opts);
        return $opts;
    }

    protected function render() {
        $settings = $this->get_settings();

        if ( empty( $settings['slides'] ) ) {
            return;
        }

        $this->add_render_attribute( 'button', 'class', [ 'elementor-button', 'lakit-slide-button' ] );

        $slides = [];
        $slide_count = 0;

        foreach ( $settings['slides'] as $slide ) {
            $slide_html = '';
            $btn_attributes = '';
            $slide_attributes = '';
            $slide_element = 'div';
            $btn_element = 'div';
            $slide_url = $slide['link']['url'];

            $tmp_html = '';

            if ( ! empty( $slide_url ) ) {
                $this->add_render_attribute( 'slide_link' . $slide_count, 'href', $slide_url );

                if ( $slide['link']['is_external'] ) {
                    $this->add_render_attribute( 'slide_link' . $slide_count, 'target', '_blank' );
                }

                if ( 'button' === $slide['link_click'] ) {
                    $btn_element = 'a';
                    $btn_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
                    $tmp_html = '<a '.$btn_attributes.'>'.$slide['heading'].'</a>';
                } else {
                    $slide_element = 'a';
                    $slide_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
                }
            }

            if ( 'yes' === $slide['background_overlay'] ) {
                $slide_html .= '<div class="elementor-background-overlay"></div>';
            }

            $slide_html .= '<div class="lakit-slide-content">';

            if ( $slide['subheading'] ) {
                $slide_html .= '<div class="lakit-slide-subheading">' . $slide['subheading'] . '</div>';
            }

            if ( $slide['heading'] ) {
                $slide_html .= '<div class="lakit-slide-heading">' . $slide['heading'] . '</div>';
            }

            if ( $slide['description'] ) {
                $slide_html .= '<div class="lakit-slide-description">' . $slide['description'] . '</div>';
            }

            if ( $slide['subdescription1'] ) {
                $slide_html .= '<div class="lakit-slide-subdescription lakit-slide-subdescription1">' . $slide['subdescription1'] . '</div>';
            }

            if ( $slide['button_text'] ) {
                $slide_html .= '<' . $btn_element . ' ' . $btn_attributes . ' ' . $this->get_render_attribute_string( 'button' ) . '>' . $slide['button_text'] . '</' . $btn_element . '>';
            }

            if ( $slide['subdescription2'] ) {
                $slide_html .= '<div class="lakit-slide-subdescription lakit-slide-subdescription2">' . $slide['subdescription2'] . '</div>';
            }

            $ken_class = '';

            if ( '' != $slide['background_ken_burns'] ) {
                $ken_class = ' elementor-ken-' . $slide['zoom_direction'];
            }

            $slide_html .= '</div>';
            $slide_bg = '<div class="lakit-slide-bg' . $ken_class . '">'.$tmp_html.'</div>';
            $slide_bg = '<div class="lakit-slide-wrapbg">'.$slide_bg.'</div>';
            $slide_html = $slide_bg . '<' . $slide_element . ' ' . $slide_attributes . ' class="lakit-slide-inner">' . $slide_html . '</' . $slide_element . '>';
            $slides[] = '<div class="elementor-repeater-item-' . $slide['_id'] . (isset($slide['el_class']) ? ' ' . $slide['el_class'] : '')  . ' swiper-slide">' . $slide_html . '</div>';
            $slide_count++;
        }

        $is_rtl = is_rtl();
        $direction = $is_rtl ? 'rtl' : 'ltr';

        $carousel_classes = [ 'lakit-carousel lakit-slides' ];

        $this->add_render_attribute( 'slides', [
            'class' => $carousel_classes,
            'data-slider_options' => htmlspecialchars( json_encode( $this->get_advanced_carousel_options() ) ),
            'dir' => $direction
        ] );

        $carousel_id = $this->get_settings_for_display('carousel_id');
        if(empty($carousel_id)){
            $carousel_id = 'lakit_carousel_' . $this->get_id();
        }

        ?>
        <div <?php echo $this->get_render_attribute_string( 'slides' ); ?>>
            <div class="lakit-carousel-inner">
                <div class="swiper-container" id="<?php echo esc_attr($carousel_id); ?>">
                    <div class="swiper-wrapper">
                        <?php echo implode( '', $slides ); ?>
                    </div>
                </div>
            </div>
            <?php
            if ( filter_var(  $this->get_settings_for_display( 'carousel_dots' ), FILTER_VALIDATE_BOOLEAN ) ) {
                echo '<div class="lakit-carousel__dots lakit-carousel__dots_'.$this->get_id().' swiper-pagination"></div>';
            }
            if ( filter_var(  $this->get_settings_for_display( 'carousel_arrows' ), FILTER_VALIDATE_BOOLEAN ) ) {
                echo sprintf( '<div class="lakit-carousel__prev-arrow-%s lakit-arrow prev-arrow">%s</div>', $this->get_id(), $this->_render_icon( 'carousel_prev_arrow', '%s', '', false ) );
                echo sprintf( '<div class="lakit-carousel__next-arrow-%s lakit-arrow next-arrow">%s</div>', $this->get_id(), $this->_render_icon( 'carousel_next_arrow', '%s', '', false ) );
            }
            if ( filter_var(  $this->get_settings_for_display( 'carousel_scrollbar' ), FILTER_VALIDATE_BOOLEAN ) ) {
                echo '<div class="lakit-carousel__scrollbar swiper-scrollbar"></div>';
            }
            ?>
        </div>
        <?php
    }

}

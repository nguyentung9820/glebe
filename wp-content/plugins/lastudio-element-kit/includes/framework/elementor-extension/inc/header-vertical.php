<?php
namespace LaStudioKit_Extension\Modules;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Controls_Manager;

class Header_Vertical {
    public function __construct() {
        add_action('elementor/theme/before_do_header', [ $this, 'add_open_wrap' ], 0 );
        add_action('wp_footer', [ $this, 'add_close_wrap' ], -1001 );
        add_action('elementor/element/header/document_settings/before_section_end', [ $this, 'register_control_settings' ]);

        add_action('elementor/element/section/section_advanced/after_section_end', [ $this, 'add_transparency_controls' ]);
    }

    public function add_transparency_controls( $stack ){
        $stack->start_controls_section('section_transparency_style', [
            'label' => esc_html__('LA-Kit Transparency Style', 'lastudio-kit'),
            'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
        ]);
        $stack->add_control(
            'lakit_section_transparency_enable',
            [
                'label' => __( 'Enable transparency style ?', 'lastudio-kit' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'prefix_class' => 'lakit--transparency-',
            ]
        );
        $stack->add_control(
            'lakit_section_transparency_note',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( 'Note: This option may not work properly in some cases', 'lastudio-kit' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'condition' => array(
                    'lakit_section_transparency_enable' => 'yes',
                ),
            ]
        );
        $stack->add_control(
            'lakit_section_bg_color',
            array(
                'label' => esc_html__( 'Section Background Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}}' => '--lakit-section-bg-color: {{VALUE}}',
                ),
                'condition' => array(
                    'lakit_section_transparency_enable' => 'yes',
                ),
            )
        );
        $stack->add_control(
            'lakit_section_text_color',
            array(
                'label' => esc_html__( 'Section Text Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}}' => '--lakit-section-text-color: {{VALUE}}',
                ),
                'condition' => array(
                    'lakit_section_transparency_enable' => 'yes',
                ),
            )
        );
        $stack->add_control(
            'lakit_section_link_color',
            array(
                'label' => esc_html__( 'Section Link Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}}' => '--lakit-section-link-color: {{VALUE}}',
                ),
                'condition' => array(
                    'lakit_section_transparency_enable' => 'yes',
                ),
            )
        );
        $stack->add_control(
            'lakit_section_link_hover_color',
            array(
                'label' => esc_html__( 'Section Hover Color', 'lastudio-kit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}}' => '--lakit-section-link-hover-color: {{VALUE}}',
                ),
                'condition' => array(
                    'lakit_section_transparency_enable' => 'yes',
                ),
            )
        );
        $stack->end_controls_section();
    }

    public function register_control_settings( $stack ){
        $stack->add_control(
            'lakit_header_vertical',
            [
                'label' => __( 'Vertical Header Layout ?', 'lastudio-kit' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes'
            ]
        );
        $stack->add_responsive_control(
            'lakit_header_vertical_width',
            array(
                'label'      => esc_html__( 'Header Width', 'lastudio-kit' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em', 'vw', 'vh', '%' ),
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors'  => array(
                    '{{WRAPPER}}lakit.lakit--is-vheader' => '--lakit-vheader-width: {{SIZE}}{{UNIT}}',
                ),
                'condition' => array(
                    'lakit_header_vertical' => 'yes',
                ),
            )
        );

        $stack->add_control(
            'lakit_header_vertical_alignment',
            array(
                'label'   => esc_html__( 'Header Alignment', 'lastudio-kit' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => array(
                    'left'    => array(
                        'title' => esc_html__( 'Left', 'lastudio-kit' ),
                        'icon'  => 'eicon-h-align-left',
                    ),
                    'right' => array(
                        'title' => esc_html__( 'Right', 'lastudio-kit' ),
                        'icon'  => 'eicon-h-align-right',
                    ),
                ),
                'condition' => array(
                    'lakit_header_vertical' => 'yes',
                ),
            )
        );
        $stack->add_control(
            'lakit_header_vertical_disable_on',
            array(
                'label'   => esc_html__( 'Disable Vertical Header On', 'lastudio-kit' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'tablet',
                'options' => lastudio_kit_helper()->get_active_breakpoints(false, true),
                'condition' => array(
                    'lakit_header_vertical' => 'yes',
                ),
            )
        );
    }

    public function add_open_wrap(){
        global $lakit_site_wrapper_open;

        if(lastudio_kit()->has_elementor_pro()){
            $documents_by_conditions = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location( 'header' );
        }
        else{
            $documents_by_conditions = \LaStudioKitThemeBuilder\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location( 'header' );
        }

        $document_id = key($documents_by_conditions);
        $settings = get_post_meta( $document_id, '_elementor_page_settings', true );
        $lakit_site_wrapper_open = true;
        $classes = ['lakit-site-wrapper'];
        $classes[] = 'elementor-' . $document_id . 'lakit';
        if(isset($settings['lakit_header_vertical']) && filter_var($settings['lakit_header_vertical'], FILTER_VALIDATE_BOOLEAN)){
            $alignment = !empty($settings['lakit_header_vertical_alignment']) ? $settings['lakit_header_vertical_alignment'] : 'left';
            $disable_on = !empty($settings['lakit_header_vertical_disable_on']) ? $settings['lakit_header_vertical_disable_on'] : 'tablet';
            if($alignment !== 'right'){
                $alignment = 'left';
            }
            $classes[] = 'lakit--is-vheader';
            $classes[] = 'lakit-vheader-p' . $alignment;
            $classes[] = 'lakit-vheader--hide' . $disable_on;
            wp_enqueue_script('lastudio-kit-header-vertical');
            wp_enqueue_style('lastudio-kit-base');
        }
        echo sprintf('<div class="%s">', esc_attr(join(' ', $classes)));
    }

    public function add_close_wrap(){
        global $lakit_site_wrapper_open;
        if( $lakit_site_wrapper_open ){
            echo '</div><!-- .lakit-site-wrapper -->';
        }
        $lakit_site_wrapper_open = false;
    }
}

new Header_Vertical();
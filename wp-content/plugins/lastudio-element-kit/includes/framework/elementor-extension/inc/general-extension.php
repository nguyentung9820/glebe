<?php
namespace LaStudioKit_Extension\Modules;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Controls_Manager;

class General_Extensions {

    public function __construct() {
        add_action('elementor/element/column/layout/before_section_end', [ $this , 'add_order_into_column' ], 99);
        add_action('elementor/element/before_section_end', [ $this , 'add_order_into_widget' ], 99, 2);
        if ( version_compare( ELEMENTOR_VERSION, '3.4.0', '<' ) ) {
            add_action('elementor/element/before_section_end', [ $this , 'add_breakpoint_visible_control' ], 99, 2);
        }
    }

    public function add_order_into_column( $stack ){

	    $active_breakpoints = [
	    	'no' => esc_html__('None', 'lastudio-kit')
	    ];
	    $active_breakpoints = array_merge($active_breakpoints, lastudio_kit_helper()->get_active_breakpoints(false, true));

        $stack->add_responsive_control(
            '_c_order',
            array(
                'label'   => esc_html__( 'Column Order', 'lastudio-kit' ),
                'type'    => Controls_Manager::NUMBER,
                'min'     => -5,
                'max'     => 10,
                'step'    => 1,
                'selectors'  => array(
                    '{{WRAPPER}}' => 'order: {{VALUE}};-webkit-order: {{VALUE}};',
                ),
            )
        );
	    $stack->add_control(
		    'lakit_col_width_auto',
		    [
			    'label' => __( 'Enable Column AutoWidth', 'lastudio-kit' ),
			    'type'    => Controls_Manager::SELECT,
			    'default' => 'no',
			    'prefix_class' => 'lakit-col-width-auto-',
			    'options' => $active_breakpoints,
		    ]
	    );
	    $stack->add_control(
		    'lakit_col_align',
		    array(
			    'label'   => esc_html__( 'Column Align', 'lastudio-kit' ),
			    'type'    => Controls_Manager::SELECT,
			    'default' => 'none',
			    'options' => array(
				    'none'      => esc_html__( 'None', 'lastudio-kit' ),
				    'left'      => esc_html__( 'Left', 'lastudio-kit' ),
				    'center'    => esc_html__( 'Center', 'lastudio-kit' ),
				    'right'     => esc_html__( 'Right', 'lastudio-kit' ),
			    ),
			    'prefix_class' => 'lakit-col-align-',
			    'condition' => [
				    'lakit_col_width_auto!' => 'no'
			    ]
		    )
	    );
    }

    public function add_order_into_widget( $stack, $section_id ) {
        if($section_id == '_section_style'){
            $stack->add_responsive_control(
                '_w_order',
                array(
                    'label'   => esc_html__( 'Widget Order', 'lastudio-kit' ),
                    'type'    => Controls_Manager::NUMBER,
                    'min'     => -5,
                    'max'     => 100,
                    'step'    => 1,
                    'selectors'  => array(
                        '{{WRAPPER}}' => 'order: {{VALUE}};-webkit-order: {{VALUE}};',
                    ),
                )
            );
	        $stack->add_control(
		        '_w_fullright',
		        [
			        'label' => __( 'Wide widget on the right', 'lastudio-kit' ),
			        'type' => Controls_Manager::SWITCHER,
			        'default' => '',
			        'prefix_class' => '',
			        'label_on' => __('Yes', 'lastudio-kit'),
			        'label_off' => __('No', 'lastudio-kit'),
			        'return_value' => 'widget_full_right',
		        ]
	        );
        }
    }

    public function add_breakpoint_visible_control( $stack, $section_id ){
        if( '_section_responsive' === $section_id ) {
            if(lastudio_kit()->elementor()->breakpoints->get_active_breakpoints('laptop')){
                $stack->add_control(
                    'hide_laptop',
                    [
                        'label' => __( 'Hide On Laptop', 'lastudio-kit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'default' => '',
                        'prefix_class' => 'elementor-',
                        'label_on' => 'Hide',
                        'label_off' => 'Show',
                        'return_value' => 'hidden-laptop',
                    ]
                );
            }
            if(lastudio_kit()->elementor()->breakpoints->get_active_breakpoints('mobile_extra')){
                $stack->add_control(
                    'hide_mobile_extra',
                    [
                        'label' => __( 'Hide On Mobile Extra', 'lastudio-kit' ),
                        'type' => Controls_Manager::SWITCHER,
                        'default' => '',
                        'prefix_class' => 'elementor-',
                        'label_on' => 'Hide',
                        'label_off' => 'Show',
                        'return_value' => 'hidden-mobile-extra',
                    ]
                );
            }
        }
    }
}

new General_Extensions();
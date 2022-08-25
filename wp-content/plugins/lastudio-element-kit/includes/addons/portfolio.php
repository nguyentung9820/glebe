<?php

/**
 * Class: LaStudioKit_Portfolio
 * Name: Portfolio
 * Slug: lakit-portfolio
 */


namespace Elementor;

use LaStudioKit_Extension\Controls\Group_Control_Related as Group_Control_Related;

if (!defined('WPINC')) {
	die;
}

if(!class_exists('LaStudioKit_Posts')){
	require_once lastudio_kit()->plugin_path('includes/addons/posts.php');
}

/**
 * Posts Widget
 */
class LaStudioKit_Portfolio extends LaStudioKit_Posts {

	protected function enqueue_addon_resources(){
		wp_register_style( 'lakit-posts', lastudio_kit()->plugin_url('assets/css/addons/posts.css'), ['lastudio-kit-base'], lastudio_kit()->get_version());
		wp_register_style( $this->get_name(), lastudio_kit()->plugin_url('assets/css/addons/portfolio.css'), ['lakit-posts'], lastudio_kit()->get_version());
		$this->add_style_depends( $this->get_name() );
		$this->add_script_depends( 'jquery-isotope' );
		$this->add_script_depends( 'lastudio-kit-base' );
	}

	private $_query = null;

	public $item_counter = 0;

	public function get_name() {
		return 'lakit-portfolio';
	}

	protected function get_widget_title() {
		return esc_html__( 'Portfolio', 'lastudio-kit' );
	}

	public function get_keywords() {
		return [ 'portfolio' ];
	}

	protected function _register_section_meta( $css_scheme ){
		$this->_start_controls_section(
			'section_meta',
			[
				'label' => __( 'Meta Data', 'lastudio-kit' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->_add_control(
			'floating_date',
			[
				'label'     => esc_html__( 'Show Floating Date', 'lastudio-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'lastudio-kit' ),
				'label_off' => esc_html__( 'No', 'lastudio-kit' ),
				'default'   => 'no'
			]
		);

		$this->_add_control(
			'floating_date_style',
			[
				'label'     => esc_html__( 'Floating Date Style', 'lastudio-kit' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'circle' => esc_html__( 'Circle', 'lastudio-kit' )
				],
				'condition' => [
					'floating_date' => 'yes',
				]
			]
		);

		$this->_add_control(
			'floating_category',
			[
				'label'     => esc_html__( 'Show Floating Category', 'lastudio-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'lastudio-kit' ),
				'label_off' => esc_html__( 'No', 'lastudio-kit' ),
				'default'   => 'no'
			]
		);

		$this->_add_control(
			'show_meta',
			array(
				'type'         => 'switcher',
				'label'        => esc_html__( 'Show Meta Data', 'lastudio-kit' ),
				'label_on'     => esc_html__( 'Yes', 'lastudio-kit' ),
				'label_off'    => esc_html__( 'No', 'lastudio-kit' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_label',
			array(
				'label' => esc_html__( 'Label', 'lastudio-kit' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$repeater->add_control(
			'item_icon',
			[
				'label'            => __( 'Icon', 'lastudio-kit' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin'             => 'inline',
				'label_block'      => false,
			]
		);

		$repeater->add_control(
			'item_type',
			[
				'label'   => esc_html__( 'Type', 'lastudio-kit' ),
				'type'    => Controls_Manager::SELECT2,
				'options' => apply_filters( 'lastudio-kit/'.$this->get_lakit_name().'/metadata', [
					'category' => esc_html__( 'Category', 'lastudio-kit' ),
					'author'   => esc_html__( 'Author', 'lastudio-kit' ),
					'date'     => esc_html__( 'Posted Date', 'lastudio-kit' ),
					'tag'      => esc_html__( 'Tags', 'lastudio-kit' ),
				] )
			]
		);

		$this->_add_control(
			'metadata1',
			array(
				'label'         => esc_html__( 'MetaData 1', 'lastudio-kit' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'title_field'   => '{{{ item_label }}}',
				'prevent_empty' => false,
				'condition'     => array(
					'show_meta' => 'yes'
				)
			)
		);

		$this->_add_control(
			'meta_position1',
			[
				'label'     => esc_html__( 'MetaData 1 Position', 'lastudio-kit' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'before_title'  => esc_html__( 'Before Title', 'lastudio-kit' ),
					'after_title'   => esc_html__( 'After Title', 'lastudio-kit' ),
					'after_content' => esc_html__( 'After Content', 'lastudio-kit' ),
				],
				'default'   => 'before_title',
				'condition' => [
					'show_meta' => 'yes',
				]
			]
		);

		$this->_add_control(
			'metadata2',
			array(
				'label'         => esc_html__( 'MetaData 2', 'lastudio-kit' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'title_field'   => '{{{ item_label }}}',
				'prevent_empty' => false,
				'condition'     => array(
					'show_meta' => 'yes'
				)
			)
		);
		$this->_add_control(
			'meta_position2',
			[
				'label'     => esc_html__( 'MetaData 2 Position', 'lastudio-kit' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'before_title'  => esc_html__( 'Before Title', 'lastudio-kit' ),
					'after_title'   => esc_html__( 'After Title', 'lastudio-kit' ),
					'after_content' => esc_html__( 'After Content', 'lastudio-kit' ),
				],
				'default'   => 'after_title',
				'condition' => [
					'show_meta' => 'yes',
				]
			]
		);

		$this->_end_controls_section();
	}

	protected function _register_section_query( $css_scheme ) {
		/** Query section */
		$this->_start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'lastudio-kit' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->_add_group_control(
			Group_Control_Related::get_type(),
			[
				'name'        => 'query',
				'object_type' => 'la_portfolio',
				'post_type' => 'la_portfolio',
				'presets'     => [ 'full' ],
				'fields_options' => [
					'post_type' => [
						'default' => 'la_portfolio',
						'options' => [
							'current_query' => __( 'Current Query', 'lastudio-kit' ),
							'la_portfolio' => __( 'Latest Portfolio', 'lastudio-kit' ),
							'by_id' => _x( 'Manual Selection', 'Posts Query Control', 'lastudio-kit' ),
							'related' => _x( 'Related', 'Posts Query Control', 'lastudio-kit' ),
						],
					],
					'orderby' => [
						'default' => 'date',
						'options' => [
							'date'          => __( 'Date', 'lastudio-kit' ),
							'title'         => __( 'Title', 'lastudio-kit' ),
							'rand'          => __( 'Random', 'lastudio-kit' ),
							'menu_order'    => __( 'Menu Order', 'lastudio-kit' ),
							'post__in'      => __( 'Manual Selection', 'lastudio-kit' ),
						],
					],
					'exclude' => [
						'options' => [
							'current_post' => __( 'Current Post', 'lastudio-kit' ),
							'manual_selection' => __( 'Manual Selection', 'lastudio-kit' ),
							'terms' => __( 'Portfolio Category', 'lastudio-kit' ),
						],
					],
					'exclude_ids' => [
						'object_type' => 'la_portfolio',
					],
					'include_ids' => [
						'object_type' => 'la_portfolio',
					],
				],
				'exclude' => [
					'exclude_authors',
					'authors',
					'offset',
					'query_id',
					'ignore_sticky_posts',
				],
			]
		);

		$this->_add_control(
			'paginate',
			[
				'label'   => __( 'Pagination', 'lastudio-kit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => ''
			]
		);

		$this->_add_control(
			'paginate_as_loadmore',
			[
				'label'     => __( 'Use Load More', 'lastudio-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => [
					'paginate' => 'yes',
				],
			]
		);

		$this->_add_control(
			'loadmore_text',
			[
				'label'     => __( 'Load More Text', 'lastudio-kit' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Load More',
				'condition' => [
					'paginate'             => 'yes',
					'paginate_as_loadmore' => 'yes',
				]
			]
		);

		$this->_end_controls_section();
	}

	protected function _register_section_style_content_inner( $css_scheme ){

		$this->_start_controls_section(
			'section_inner_content_style',
			array(
				'label'      => esc_html__( 'Item Content Inner', 'lastudio-kit' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => [
					'preset' => ['grid-2'],
				]
			)
		);

		$this->_add_responsive_control(
			'content_width',
			array(
				'label'      => esc_html__( 'Width', 'lastudio' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inner-content'] => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->_add_responsive_control(
			'inner_content_alignment',
			array(
				'label'     => esc_html__( 'Horizontal Alignment', 'lastudio-kit' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'lastudio-kit' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'lastudio-kit' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'lastudio-kit' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'text-align: {{VALUE}};',
				),
			)
		);
		$this->_add_responsive_control(
			'inner_content_v_alignment',
			array(
				'label'     => esc_html__( 'Vertical Alignment', 'lastudio-kit' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'lastudio-kit' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'lastudio-kit' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'lastudio-kit' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->_add_control(
			'enable_right_btn',
			[
				'label'     => esc_html__( 'Enable Right Button', 'lastudio-kit' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'lastudio-kit' ),
				'label_off' => esc_html__( 'No', 'lastudio-kit' ),
				'default'   => '',
				'prefix_class' => 'lakit--portfolio-btn-right-',
				'condition' => [
					'show_more' => 'yes',
				]
			]
		);

		$this->_add_responsive_control(
			'inner_content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'lastudio-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inner-content'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->_add_responsive_control(
			'inner_content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'lastudio-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inner-content'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				)
			)
		);
		$this->_add_responsive_control(
			'inner_content_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'lastudio-kit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inner-content'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->_start_controls_tabs( 'inner_content_style_tabs' );
		$this->_start_controls_tab( 'inner_content_normal',
			[
				'label' => __( 'Normal', 'lastudio-kit' ),
			]
		);
		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'inner_content_bg',
				'selector' => '{{WRAPPER}} ' . $css_scheme['inner-content'],
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'inner_content_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['inner-content'],
			)
		);

		$this->_end_controls_tab();
		$this->_start_controls_tab( 'inner_content_hover',
			[
				'label' => __( 'Hover', 'lastudio-kit' ),
			]
		);
		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'inner_content_bg_hover',
				'selector' => '{{WRAPPER}} .lakit-posts__outer-box:hover .lakit-posts__inner-content-inner',
			),
			25
		);
		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'inner_content_shadow_hover',
				'selector' => '{{WRAPPER}} .lakit-posts__inner-box:hover .lakit-posts__inner-content-inner',
			)
		);

		$this->_end_controls_tab();
		$this->_end_controls_tabs();

		$this->_end_controls_section();
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'lastudio-kit/'.$this->get_lakit_name().'/css-scheme',
			array(
				'wrap_outer'    => '.lakit-posts',
				'wrap'          => '.lakit-posts .lakit-posts__list_wrapper',
				'column'        => '.lakit-posts .lakit-posts__outer-box',
				'inner-box'     => '.lakit-posts .lakit-posts__inner-box',
				'content'       => '.lakit-posts .lakit-posts__inner-content',
				'inner-content' => '.lakit-posts .lakit-posts__inner-content-inner',
				'link'          => '.lakit-posts .lakit-posts__thumbnail-link',
				'thumb'         => '.lakit-posts .lakit-posts__thumbnail',
				'title'         => '.lakit-posts .lakit-posts__title',
				'excerpt'       => '.lakit-posts .lakit-posts__excerpt',
				'button'        => '.lakit-posts .lakit-posts__btn-more',
				'button_icon'   => '.lakit-posts .lakit-btn-more-icon',
				'meta1'         => '.lakit-posts .lakit-posts__meta1',
				'meta1-item'    => '.lakit-posts .lakit-posts__meta1 .lakit-posts__meta__item',
				'meta2'         => '.lakit-posts .lakit-posts__meta2',
				'meta2-item'    => '.lakit-posts .lakit-posts__meta2 .lakit-posts__meta__item',
			)
		);

		$this->_register_section_layout( $css_scheme );

		$this->_register_section_meta( $css_scheme );

		$this->_register_section_query( $css_scheme );

		$this->register_masonry_setting_section( [ 'enable_masonry' => 'yes' ] );

		$this->register_carousel_section( [ 'enable_masonry!' => 'yes' ], 'columns' );

		$this->_register_section_style_general( $css_scheme );

		$this->_register_section_style_meta( $css_scheme );

		$this->_register_section_style_floating_date( $css_scheme );

		$this->_register_section_style_floating_category( $css_scheme );

		$this->_register_section_style_pagination( $css_scheme );

		$this->register_carousel_arrows_dots_style_section( [ 'enable_masonry!' => 'yes' ] );

	}

}
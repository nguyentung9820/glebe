<?php

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

function la_bakerfresh_get_demo_array($dir_url, $dir_path){

    $demo_items = array(
        'cake-shop-01' => array(
            'link'          => 'https://baker.la-studioweb.com/cake-shop-01/',
            'title'         => 'Cake Shop 01',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_slider'   => 'baker-cake-shop-01.zip',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-01' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-01' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'cake-shop-02' => array(
            'link'          => 'https://baker.la-studioweb.com/cake-shop-02/',
            'title'         => 'Cake Shop 02',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-02' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-02' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'cake-shop-03' => array(
            'link'          => 'https://baker.la-studioweb.com/cake-shop-03/',
            'title'         => 'Cake Shop 03',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_slider'   => 'baker-cake-shop-03.zip',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-01' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-01' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'cake-shop-04' => array(
            'link'          => 'https://baker.la-studioweb.com/cake-shop-04/',
            'title'         => 'Cake Shop 04',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_slider'   => 'baker-cake-shop-04.zip',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-02' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-04' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'cake-shop-05' => array(
            'link'          => 'https://baker.la-studioweb.com/cake-shop-05/',
            'title'         => 'Cake Shop 05',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_slider'   => 'baker-cake-shop-05.zip',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-06' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-04' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'cake-shop-06' => array(
            'link'          => 'https://baker.la-studioweb.com/cake-shop-06/',
            'title'         => 'Cake Shop 06',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_slider'   => 'baker-cake-new-shop-06.zip',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-07' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-01' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'bread-shop' => array(
            'link'          => 'https://baker.la-studioweb.com/bread-shop/',
            'title'         => 'Bread Shop',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_slider'   => 'baker-cake-shop-06.zip',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-03' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-03' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'bread-shop-02' => array(
            'link'          => 'https://baker.la-studioweb.com/bread-shop-02/',
            'title'         => 'Bread Shop 02',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_slider'   => 'bread-shop-02.zip',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-01' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-01' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
        'cake-shop-fullscreen' => array(
            'link'          => 'https://baker.la-studioweb.com/cake-shop-fullscreen/',
            'title'         => 'Cake Shop Fullscreen',
            'data_sample'   => 'demo-data.json',
            'data_product'  => 'products.csv',
            'data_widget'   => 'widget-data.json',
            'data_elementor'=> [
                'header'       => [
                    'location' => 'header',
                    'value' => [
                        'baker-header-01' => 'include/general',
                    ],
                ],
                'footer'       => [
                    'location' => 'footer',
                    'value' => [
                        'baker-footer-01' => 'include/general',
                    ],
                ],
            ],
            'category'      => array(
                'Demo',
            )
        ),
    );

    $default_image_setting = array(
        'woocommerce_single_image_width' => 800,
        'woocommerce_thumbnail_image_width' => 400,
        'woocommerce_thumbnail_cropping' => 'custom',
        'woocommerce_thumbnail_cropping_custom_width' => 4,
        'woocommerce_thumbnail_cropping_custom_height' => 5,
        'thumbnail_size_w' => 370,
        'thumbnail_size_h' => 350,
        'medium_size_w' => 0,
        'medium_size_h' => 0,
        'medium_large_size_w' => 0,
        'medium_large_size_h' => 0,
        'large_size_w' => 0,
        'large_size_h' => 0,
    );

    $default_menu = array(
        'main-nav'              => 'Primary Navigation'
    );

    $default_page = array(
        'page_for_posts' 	            => 'Blog',
        'woocommerce_shop_page_id'      => 'Shop',
        'woocommerce_cart_page_id'      => 'Cart',
        'woocommerce_checkout_page_id'  => 'Checkout',
        'woocommerce_myaccount_page_id' => 'My Account'
    );

    $slider = $dir_path . 'Slider/';
    $content = $dir_path . 'Content/';
    $product = $dir_path . 'Product/';
    $widget = $dir_path . 'Widget/';
    $setting = $dir_path . 'Setting/';
    $preview = $dir_url;

    $default_elementor = [
        'single-post'       => [
            'location' => 'single',
            'value' => [
                'baker-single-post' => 'include/singular/post',
            ],
        ],
        'single-page'       => [
            'location' => 'single',
            'value' => [
                'baker-woocommerce-pages' => [
                    'include/singular/page/wishlist',
                    'include/singular/page/compare',
                    'include/singular/page/my-account',
                    'include/singular/page/cart',
                    'include/singular/page/checkout'
                ],
            ]
        ],
        'archive'           => [
            'location' => 'archive',
            'value' => [
                'baker-blog-sidebar' => 'include/archive'
            ]
        ],
        'search-results'    => [
            'location' => 'archive',
            'value'    => '',
            'default' => [
                'name' => 'include/archive/search'
            ],
        ],
        'error-404'         => [
            'location' => 'single',
            'value'    => '',
            'default' => [
                'name' => 'include/singular/not_found404'
            ],
        ],
        'product'           => [
            'location' => 'single',
            'value' => [
                'baker-single-product-01' => 'include/product'
            ]
        ],
        'product-archive'   => [
            'location' => 'archive',
            'value' => [
                'baker-shop-sidebar' => 'include/product_archive'
            ]
        ],
    ];

    $elementor_kit_settings = json_decode('{"system_colors":[{"_id":"primary","title":"Primary"},{"_id":"secondary","title":"Secondary"},{"_id":"text","title":"Text"},{"_id":"accent","title":"Accent"}],"custom_colors":[{"_id":"ae60f9c","title":"Grey Color","color":"#858585"},{"_id":"8a56718","title":"Dark Color","color":"#212121"},{"_id":"ef3c1e9","title":"White Color","color":"#FFFFFF"},{"_id":"ff9a433","title":"Primary Color 1","color":"#BC8157"},{"_id":"5719fc8","title":"Primary Color 2","color":"#830E0E"},{"_id":"b2a5d83","title":"Primary Color 3","color":"#992100"},{"_id":"381365b","title":"Primary Color 4","color":"#FF9D87"},{"_id":"e56722d","title":"Primary Color 5","color":"#C2943A"},{"_id":"0ed85da","title":"Primary Color 6","color":"#8D6443"},{"_id":"d83d066","title":"Primary Color 7","color":"#FF9457"}],"system_typography":[{"_id":"primary","title":"Primary"},{"_id":"secondary","title":"Secondary"},{"_id":"text","title":"Text"},{"_id":"accent","title":"Accent"}],"custom_typography":[],"default_generic_fonts":"Sans-serif","page_title_selector":"h1.entry-title","viewport_md":768,"viewport_lg":1281,"active_breakpoints":["viewport_mobile","viewport_mobile_extra","viewport_tablet","viewport_laptop"],"viewport_mobile":767,"viewport_mobile_extra":880,"viewport_tablet":1280,"viewport_laptop":1600,"button_background_position":"","button_background_repeat":"","button_background_size":"","button_background_slideshow_background_size":"","button_background_slideshow_background_position":"","button_hover_background_position":"","button_hover_background_repeat":"","button_hover_background_size":"","button_hover_background_slideshow_background_size":"","button_hover_background_slideshow_background_position":"","body_background_position":"","body_background_repeat":"","body_background_size":"","body_background_slideshow_background_size":"","body_background_slideshow_background_position":""}', true);

    $data_return = array();

    foreach ($demo_items as $demo_key => $demo_detail){
	    $value = array();

	    $value['title']             = $demo_detail['title'];
	    $value['category']          = !empty($demo_detail['category']) ? $demo_detail['category'] : array('Demo');
	    $value['demo_preset']       = $demo_key;
	    $value['demo_url']          = $demo_detail['link'];
	    $value['preview']           = !empty($demo_detail['preview']) ? $demo_detail['preview'] : ($preview . $demo_key . '.jpg');
	    $value['option']            = $setting . $demo_key . '.json';
	    $value['content']           = !empty($demo_detail['data_sample']) ? $content . $demo_detail['data_sample'] : $content . 'demo-data.json';
	    $value['product']           = !empty($demo_detail['data_product']) ? $product . $demo_detail['data_product'] : $product . 'sample-product.json';
	    $value['widget']            = !empty($demo_detail['data_widget']) ? $widget . $demo_detail['data_widget'] : $widget . 'widget-data.json';
	    $value['pages']             = array_merge( $default_page, array( 'page_on_front' => $demo_detail['title'] ));
	    $value['menu-locations']    = array_merge( $default_menu, isset($demo_detail['menu-locations']) ? $demo_detail['menu-locations'] : array());
	    $value['other_setting']     = array_merge( $default_image_setting, isset($demo_detail['other_setting']) ? $demo_detail['other_setting'] : array());
	    if(!empty($demo_detail['data_slider'])){
		    $value['slider'] = $slider . $demo_detail['data_slider'];
	    }
        $value['elementor']         = array_merge( $default_elementor, isset($demo_detail['data_elementor']) ? $demo_detail['data_elementor'] : array());
        $value['elementor_kit_settings']         = array_merge( $elementor_kit_settings, isset($demo_detail['elementor_kit_settings']) ? $demo_detail['elementor_kit_settings'] : array());
	    $data_return[$demo_key] = $value;
    }

    return $data_return;
}
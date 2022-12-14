(function ($, elementor) {
    "use strict";

    function getHeaderHeight(){
        var _height = 0;
        var $stickySection = $('.elementor-location-header .elementor-section[data-settings*="sticky_on"]');
        if($stickySection.length){
            _height = $stickySection.innerHeight();
        }
        return _height;
    }

    function checkHeaderHeight(){
        document.documentElement.style.setProperty('--lakit-header-height', getHeaderHeight() + 'px');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.add('lakit--js-ready');
        checkHeaderHeight();
    });

    $(window).on('load resize', checkHeaderHeight);

    $(document).on('lastudiokit/woocommerce/single/init_product_slider', function (e, slider) {
        slider.controlNav.eq(slider.animatingTo).closest('li').get(0).scrollIntoView({
            inline: "center",
            block: "nearest",
            behavior: "smooth"
        });
        slider.viewport.closest('.woocommerce-product-gallery').css('--singleproduct-thumbs-height', slider.viewport.height() + 'px');
    });

    var LaStudioKits = {
        log: function (...data){
            var args = Array.prototype.slice.call(arguments)
            console.log(...data);
        },
        addedScripts: {},
        addedStyles: {},
        addedAssetsPromises: [],
        carouselAsFor: [],
        localCache: {
            cache_key: typeof LaStudioKitSettings.themeName !== "undefined" ? LaStudioKitSettings.themeName : 'lakit',
            /**
             * timeout for cache in seconds, default 5 mins
             * @type {number}
             */
            timeout: typeof LaStudioKitSettings.cache_ttl !== "undefined" && parseInt(LaStudioKitSettings.cache_ttl) > 0 ? parseInt(LaStudioKitSettings.cache_ttl) : (60 * 5),
            timeout2: 60 * 10,
            /**
             * @type {{_: number, data: {}}}
             **/
            data:{},
            remove: function (url) {
                delete LaStudioKits.localCache.data[url];
            },
            exist: function (url) {
                return !!LaStudioKits.localCache.data[url] && ((Date.now() - LaStudioKits.localCache.data[url]._) / 1000 < LaStudioKits.localCache.timeout2);
            },
            get: function (url) {
                LaStudioKits.log('Get cache for ' + url);
                return LaStudioKits.localCache.data[url].data;
            },
            set: function (url, cachedData, callback) {
                LaStudioKits.localCache.remove(url);
                LaStudioKits.localCache.data[url] = {
                    _: Date.now(),
                    data: cachedData
                };
                if ("function" == typeof callback && "number" != typeof callback.nodeType) {
                    callback(cachedData)
                }
            },
            hashCode: function (s){
                var hash = 0;
                s = s.toString();
                if (s.length == 0) return hash;

                for (var i = 0; i < s.length; i++) {
                    var char = s.charCodeAt(i);
                    hash = (hash << 5) - hash + char;
                    hash = hash & hash; // Convert to 32bit integer
                }

                return Math.abs(hash);
            },
            validCache: function ( force ){
                var expiry = typeof LaStudioKitSettings.local_ttl !== "undefined" && parseInt(LaStudioKitSettings.local_ttl) > 0 ? parseInt(LaStudioKitSettings.local_ttl) : 60 * 30; // 30 mins
                var cacheKey = LaStudioKits.localCache.cache_key + '_cache_timeout' + LaStudioKits.localCache.hashCode(LaStudioKitSettings.homeURL);
                try{
                    var whenCached = localStorage.getItem(cacheKey);
                    if (whenCached !== null || force) {
                        var age = (Date.now() - whenCached) / 1000;
                        if (age > expiry || force) {
                            Object.keys(localStorage).forEach(function (key) {
                                if (key.indexOf(LaStudioKits.localCache.cache_key) === 0) {
                                    localStorage.removeItem(key);
                                }
                            });
                            localStorage.setItem(cacheKey, Date.now());
                        }
                    } else {
                        localStorage.setItem(cacheKey, Date.now());
                    }
                }
                catch (ex) {
                    LaStudioKits.log(ex);
                }
            }
        },
        isPageSpeed: function () {
            return (typeof navigator !== "undefined" && (/(lighthouse|gtmetrix)/i.test(navigator.userAgent.toLocaleLowerCase()) || /mozilla\/5\.0 \(x11; linux x86_64\)/i.test(navigator.userAgent.toLocaleLowerCase())));
        },
        addQueryArg: function (url, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = url.indexOf('?') !== -1 ? "&" : "?";

            if (url.match(re)) {
                return url.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return url + separator + key + "=" + value;
            }
        },
        getUrlParameter: function (name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        },
        parseQueryString: function (query) {
            var urlparts = query.split("?");
            var query_string = {};

            if (urlparts.length >= 2) {
                var vars = urlparts[1].split("&");

                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split("=");
                    var key = decodeURIComponent(pair[0]);
                    var value = decodeURIComponent(pair[1]); // If first entry with this name

                    if (typeof query_string[key] === "undefined") {
                        query_string[key] = decodeURIComponent(value); // If second entry with this name
                    } else if (typeof query_string[key] === "string") {
                        var arr = [query_string[key], decodeURIComponent(value)];
                        query_string[key] = arr; // If third or later entry with this name
                    } else {
                        query_string[key].push(decodeURIComponent(value));
                    }
                }
            }

            return query_string;
        },
        removeURLParameter: function (url, parameter) {
            var urlparts = url.split('?');

            if (urlparts.length >= 2) {
                var prefix = encodeURIComponent(parameter) + '=';
                var pars = urlparts[1].split(/[&;]/g); //reverse iteration as may be destructive

                for (var i = pars.length; i-- > 0;) {
                    //idiom for string.startsWith
                    if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                        pars.splice(i, 1);
                    }
                }

                url = urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
                return url;
            } else {
                return url;
            }
        },
        initCarousel: function ($scope) {

            var $carousel = $scope.find('.lakit-carousel').first();

            if ($carousel.length == 0) {
                return;
            }

            if ($carousel.hasClass('inited')) {
                return;
            }

            $carousel.addClass('inited');

            var elementSettings = $carousel.data('slider_options'),
                slidesToShow = parseInt(elementSettings.slidesToShow.desktop) || 1,
                elementorBreakpoints = elementorFrontend.config.responsive.activeBreakpoints,
                carousel_id = elementSettings.uniqueID;

            var swiperOptions = {
                slidesPerView: slidesToShow,
                loop: elementSettings.infinite,
                speed: elementSettings.speed,
                handleElementorBreakpoints: true,
                slidesPerColumn: elementSettings.rows.desktop,
                slidesPerGroup: elementSettings.slidesToScroll.desktop || 1
            }

            swiperOptions.breakpoints = {};

            var lastBreakpointSlidesToShowValue = 1;
            var defaultLGDevicesSlidesCount = 1;
            Object.keys(elementorBreakpoints).reverse().forEach(function (breakpointName) {
                // Tablet has a specific default `slides_to_show`.
                var defaultSlidesToShow = 'tablet' === breakpointName ? defaultLGDevicesSlidesCount : lastBreakpointSlidesToShowValue;
                swiperOptions.breakpoints[elementorBreakpoints[breakpointName].value] = {
                    slidesPerView: +elementSettings.slidesToShow[breakpointName] || defaultSlidesToShow,
                    slidesPerGroup: +elementSettings.slidesToScroll[breakpointName] || 1,
                    slidesPerColumn: +elementSettings.rows[breakpointName] || 1,
                };
                lastBreakpointSlidesToShowValue = +elementSettings.slidesToShow[breakpointName] || defaultSlidesToShow;
            });

            if (elementSettings.autoplay) {
                swiperOptions.autoplay = {
                    delay: (elementSettings.effect == 'slide' && elementSettings.infiniteEffect ? 10 : elementSettings.autoplaySpeed),
                    disableOnInteraction: elementSettings.pauseOnInteraction,
                    pauseOnMouseEnter: elementSettings.pauseOnHover,
                    reverseDirection: elementSettings.reverseDirection || false,
                };
                if(elementSettings.effect == 'slide' && elementSettings.infiniteEffect){
                    $carousel.addClass('lakit--linear-effect');
                }
            }
            if (elementSettings.centerMode) {
                swiperOptions.centerInsufficientSlides = true;
                swiperOptions.centeredSlides = true;
                swiperOptions.centeredSlidesBounds = false;
            }

            switch (elementSettings.effect) {
                case 'fade':
                    if (slidesToShow == 1) {
                        swiperOptions.effect = elementSettings.effect;
                        swiperOptions.fadeEffect = {
                            crossFade: true
                        };
                    }
                    break;

                case 'coverflow':
                    swiperOptions.effect = 'coverflow';
                    swiperOptions.grabCursor = true;
                    swiperOptions.centeredSlides = true;
                    swiperOptions.slidesPerView = 'auto';
                    swiperOptions.coverflowEffect = {
                        rotate: 50,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        slideShadows: true
                    };
                    break;

                case 'cube':
                    swiperOptions.effect = 'cube';
                    swiperOptions.grabCursor = true;
                    swiperOptions.cubeEffect = {
                        shadow: true,
                        slideShadows: true,
                        shadowOffset: 20,
                        shadowScale: 0.94,
                    }
                    swiperOptions.slidesPerView = 1;
                    swiperOptions.slidesPerGroup = 1;
                    break;

                case 'flip':
                    swiperOptions.effect = 'flip';
                    swiperOptions.grabCursor = true;
                    swiperOptions.slidesPerView = 1;
                    swiperOptions.slidesPerGroup = 1;
                    break;

                case 'slide':
                    swiperOptions.effect = 'slide';
                    swiperOptions.grabCursor = true;
                    break;
            }

            if (elementSettings.arrows) {
                swiperOptions.navigation = {
                    prevEl: elementSettings.prevArrow,
                    nextEl: elementSettings.nextArrow
                };
            }
            if (elementSettings.dots) {
                swiperOptions.pagination = {
                    el: elementSettings.dotsElm || '.lakit-carousel__dots',
                    type: swiperOptions.dotType || 'bullets',
                    clickable: true
                };
                if (elementSettings.dotType == 'bullets') {
                    swiperOptions.pagination.dynamicBullets = true;
                }
                if (elementSettings.dotType == 'custom') {
                    swiperOptions.pagination.renderBullet = function (index, className) {
                        return '<span class="' + className + '">' + (index + 1) + "</span>";
                    }
                }
            }

            var enableScrollbar = elementSettings.scrollbar || false;

            if (!enableScrollbar) {
                swiperOptions.scrollbar = false;
            } else {
                swiperOptions.scrollbar = {
                    el: '.lakit-carousel__scrollbar',
                    draggable: true
                }
            }

            var _has_slidechange_effect = false,
                _slide_change_effect_in = elementSettings.content_effect_in || 'fadeInUp',
                _slide_change_effect_out = elementSettings.content_effect_out || 'fadeOutDown';

            if (elementSettings.content_selector !== undefined && $carousel.find(elementSettings.content_selector).length > 0) {
                _has_slidechange_effect = true;
            }

            if ($carousel.closest('.no-slide-animation').length || $carousel.closest('.slide-no-animation').length) {
                _has_slidechange_effect = false;
            }

            if (elementSettings.direction) {
                swiperOptions.direction = elementSettings.direction;
            }
            if (elementSettings.autoHeight) {
                swiperOptions.autoHeight = elementSettings.autoHeight
            }
            swiperOptions.watchSlidesProgress = true;
            swiperOptions.watchSlidesVisibility = true;


            function findAsForObj(value, key) {
                var _found = [];
                for (var i = 0; i < LaStudioKits.carouselAsFor.length; i++) {
                    if (LaStudioKits.carouselAsFor[i][key] == value) {
                        LaStudioKits.carouselAsFor[i]['index'] = i;
                        _found.push(LaStudioKits.carouselAsFor[i]);
                        break;
                    }
                }
                return _found;
            }

            function makeLoadedAsFor(value, key) {
                var obj = findAsForObj(value, key);
                if (obj.length) {
                    obj[0][key + '_init'] = true;
                }
                return obj;
            }

            if (typeof elementSettings.asFor !== "undefined" && elementSettings.asFor != '' && elementSettings.asFor != '#' && $('#' + elementSettings.asFor).length) {
                var _thumb_swiper = $('#' + elementSettings.asFor).data('swiper');

                if (null === _thumb_swiper || "undefined" === _thumb_swiper) {
                    swiperOptions.thumbs = {
                        swiper: _thumb_swiper,
                    }
                } else {
                    LaStudioKits.carouselAsFor.push({
                        main: carousel_id,
                        thumb: elementSettings.asFor,
                        main_init: false,
                        thumb_init: false
                    });
                }
            }

            swiperOptions.slideToClickedSlide = true;

            var $swiperContainer = $scope.find('.swiper-container');

            var Swiper = elementorFrontend.utils.swiper;

            function initSlideContentAnimation( needwaiting ){
                var $sliderContents = $carousel.find('.swiper-slide-active .lakit-template-wrapper .elementor-invisible[data-settings*="_animation"]');

                $sliderContents.each(function () {
                    var _settings = $(this).data('settings'),
                        animation = elementorFrontend.getCurrentDeviceSetting(_settings, '_animation'),
                        animationDelay = _settings._animation_delay || 0,
                        $element = $(this);
                    if ('none' === animation) {
                        $element.removeClass('elementor-invisible');
                    } else {
                        setTimeout(function () {
                            $element.removeClass('elementor-invisible').addClass('animated ' + animation);
                        }, animationDelay);
                    }
                });

                if (_has_slidechange_effect) {
                    $carousel.find('.swiper-slide:not(.swiper-slide-visible) ' + elementSettings.content_selector).addClass('no-effect-class').removeClass(_slide_change_effect_in).addClass(_slide_change_effect_out);
                    $carousel.find('.swiper-slide-visible ' + elementSettings.content_selector).removeClass('no-effect-class').removeClass(_slide_change_effect_out).addClass(_slide_change_effect_in);
                }

                if(needwaiting){
                    setTimeout(function (){
                        var $InActiveSliderContents = $carousel.find('.swiper-slide:not(.swiper-slide-visible) .lakit-template-wrapper [data-settings*="_animation"]');
                        $InActiveSliderContents.each(function () {
                            var _settings = $(this).data('settings'),
                                animation = elementorFrontend.getCurrentDeviceSetting(_settings, '_animation');
                            if ('none' === animation) {
                                $(this).removeClass('animated').addClass('elementor-invisible');
                            }
                            else {
                                $(this).removeClass('animated ' + animation).addClass('elementor-invisible');
                            }
                        });
                    }, 1000);
                }
                else{
                    var $InActiveSliderContents = $carousel.find('.swiper-slide:not(.swiper-slide-visible) .lakit-template-wrapper [data-settings*="_animation"]');
                    $InActiveSliderContents.each(function () {
                        var _settings = $(this).data('settings'),
                            animation = elementorFrontend.getCurrentDeviceSetting(_settings, '_animation');
                        if ('none' === animation) {
                            $(this).removeClass('animated').addClass('elementor-invisible');
                        }
                        else {
                            $(this).removeClass('animated ' + animation).addClass('elementor-invisible');
                        }
                    });
                }
            }

            new Swiper($swiperContainer, swiperOptions).then(function (SwiperInstance) {

                if(elementSettings.autoplay && typeof SwiperInstance.autoplay !== "undefined" && typeof SwiperInstance.autoplay.onMouseEnter === "undefined"){
                    $swiperContainer.on('mouseenter', function (){
                        SwiperInstance.autoplay.stop();
                    }).on('mouseleave', function (){
                        SwiperInstance.autoplay.start();
                    });
                }

                $swiperContainer.data('swiper', SwiperInstance);

                $swiperContainer.find('.elementor-top-section').trigger('lastudio-kit/section/calculate-container-width');

                initSlideContentAnimation(true);

                var ob1 = makeLoadedAsFor(carousel_id, 'thumb');
                var ob2 = makeLoadedAsFor(carousel_id, 'main');

                if (ob1.length && ob1[0].main_init && ob1[0].thumb_init) {
                    var _main_swiper = $('#' + ob1[0].main).data('swiper');
                    _main_swiper.thumbs.swiper = $('#' + ob1[0].thumb).data('swiper');
                    _main_swiper.thumbs.init();
                }
                if (ob2.length && ob2[0].main_init && ob2[0].thumb_init) {
                    var _main_swiper = $('#' + ob2[0].main).data('swiper');
                    _main_swiper.thumbs.swiper = $('#' + ob2[0].thumb).data('swiper');
                    _main_swiper.thumbs.init();
                }

                if (_has_slidechange_effect) {
                    $carousel.find(elementSettings.content_selector).addClass('animated no-effect-class');
                    $carousel.find('.swiper-slide-visible ' + elementSettings.content_selector).removeClass('no-effect-class').addClass(_slide_change_effect_in);
                }

                SwiperInstance.on('slideChange', function () {

                    if ($swiperContainer.hasClass(this.params.thumbs.thumbsContainerClass)) {
                        this.clickedIndex = this.activeIndex;
                        this.clickedSlide = this.slides[this.clickedIndex];
                        this.emit('tap');
                    }
                });

                SwiperInstance.on('slideChangeTransitionEnd', function (){
                    initSlideContentAnimation(false);
                });

                $(document).trigger('lastudio-kit/carousel/init_success', { swiperContainer: $swiperContainer });
            });

        },
        initMasonry: function ($scope) {
            var $container = $scope.find('.lakit-masonry-wrapper').first();

            if ($container.length == 0) {
                return;
            }

            var $list_wrap = $scope.find($container.data('lakitmasonry_wrap')),
                itemSelector = $container.data('lakitmasonry_itemselector'),
                $advanceSettings = $container.data('lakitmasonry_layouts') || false,
                $itemsList = $scope.find(itemSelector),
                $masonryInstance,
                _configs;

            if ($list_wrap.length) {

                if ($advanceSettings !== false) {
                    $(document).trigger('lastudio-kit/masonry/calculate-item-sizes', [$container, false]);
                    $(window).on('resize', function () {
                        $(document).trigger('lastudio-kit/masonry/calculate-item-sizes', [$container, true]);
                    });
                    _configs = {
                        itemSelector: itemSelector,
                        percentPosition: true,
                        masonry: {
                            columnWidth: 1,
                            gutter: 0,
                        },
                    }
                } else {
                    _configs = {
                        itemSelector: itemSelector,
                        percentPosition: true,
                    }
                }

                $masonryInstance = $list_wrap.isotope(_configs);

                $('img', $itemsList).imagesLoaded().progress(function (instance, image) {
                    var $image = $(image.img),
                        $parentItem = $image.closest(itemSelector);
                    $parentItem.addClass('item-loaded');
                    if ($masonryInstance) {
                        $masonryInstance.isotope('layout');
                    }
                });
            }
        },
        initCustomHandlers: function () {
            $(document)
                .on('click', '.lastudio-kit .lakit-pagination_ajax_loadmore a', function (e){
                    e.preventDefault();
                    if ($('body').hasClass('elementor-editor-active')) {
                        return false;
                    }
                    var $kitWrap,$parentContainer, $container, ajaxType, $parentNav, widgetId, itemSelector;
                    $parentNav = $(this).closest('.lakit-pagination');
                    $kitWrap = $(this).closest('.lastudio-kit');
                    widgetId = $kitWrap.data('id');

                    if ($parentNav.hasClass('doing-ajax')) {
                        return false;
                    }

                    ajaxType = 'load_widget';
                    if($kitWrap.find('div[data-widget_current_query="yes"]').length > 0){
                        ajaxType = 'load_fullpage';
                    }

                    if($kitWrap.hasClass('elementor-lakit-wooproducts')){
                        $container = $kitWrap.find('.lakit-products__list');
                        $parentContainer = $kitWrap.find('.lakit-products');
                        itemSelector = '.lakit-product.product_item';
                    }
                    else{
                        $container = $($parentNav.data('container'));
                        $parentContainer = $($parentNav.data('parent-container'));
                        itemSelector = $parentNav.data('item-selector');
                    }

                    if ($('a.next', $parentNav).length) {
                        $parentNav.addClass('doing-ajax');
                        $parentContainer.addClass('doing-ajax');

                        var success_func = function (response) {
                            var $data = $(response).find('.elementor-element-' + widgetId + ' ' + itemSelector);

                            if ($parentContainer.find('.lakit-carousel').length > 0) {
                                var swiper = $parentContainer.find('.lakit-carousel').get(0).swiper;
                                swiper.appendSlide($data);
                            }
                            else if ($container.data('isotope')) {
                                $container.append($data);
                                $container.isotope('insert', $data);
                                $(document).trigger('lastudio-kit/masonry/calculate-item-sizes', [$parentContainer, true]);

                                $('img', $data).imagesLoaded().progress(function (instance, image) {
                                    var $image = $(image.img),
                                        $parentItem = $image.closest(itemSelector);
                                    $parentItem.addClass('item-loaded');
                                    $container.isotope('layout');
                                });
                            }
                            else {
                                $data.addClass('fadeIn animated').appendTo($container);
                            }

                            $parentContainer.removeClass('doing-ajax');
                            $parentNav.removeClass('doing-ajax lakit-ajax-load-first');

                            if ($(response).find( '.elementor-element-' + widgetId + ' .lakit-ajax-pagination').length) {
                                var $new_pagination = $(response).find( '.elementor-element-' + widgetId + ' .lakit-ajax-pagination');
                                $parentNav.replaceWith($new_pagination);
                                $parentNav = $new_pagination;
                            } else {
                                $parentNav.addClass('nothingtoshow');
                            }
                            $('body').trigger('jetpack-lazy-images-load');
                            $(document).trigger('lastudio-kit/ajax-loadmore/success', {
                                parentContainer: $parentContainer,
                                contentHolder: $container,
                                pagination: $parentNav
                            });
                        };

                        var url_request = $('a.next', $parentNav).get(0).href.replace(/^\//, '');
                        url_request = LaStudioKits.removeURLParameter(url_request, '_');

                        var ajaxOpts = {
                            url: url_request,
                            type: "GET",
                            cache: true,
                            dataType: 'html',
                            success: function (res) {
                                success_func(res);
                            }
                        };
                        $.ajax(ajaxOpts);
                    }

                })
                .on('click', '.lastudio-kit .lakit-ajax-pagination .page-numbers a', function (e){
                    e.preventDefault();
                    if ($('body').hasClass('elementor-editor-active')) {
                        return false;
                    }
                    var $kitWrap,$parentContainer, $container, ajaxType, $parentNav, widgetId, itemSelector, templateId, pagedKey;
                    $parentNav = $(this).closest('.lakit-pagination');
                    $kitWrap = $(this).closest('.lastudio-kit');
                    widgetId = $kitWrap.data('id');

                    if ($parentNav.hasClass('doing-ajax')) {
                        return false;
                    }

                    templateId = $kitWrap.closest('.elementor[data-elementor-settings][data-elementor-id]').data('elementor-id');

                    if($kitWrap.hasClass('elementor-lakit-wooproducts')){
                        $container = $kitWrap.find('.lakit-products__list');
                        $parentContainer = $kitWrap.find('.lakit-products');
                        itemSelector = '.lakit-product.product_item';
                        var tmpClass = $parentContainer.closest('.woocommerce').attr('class').match(/\blakit_wc_widget_([^\s]*)/);
                        if (tmpClass !== null && tmpClass[1]) {
                            pagedKey = 'product-page-' + tmpClass[1];
                        }
                        else{
                            pagedKey = 'paged';
                        }
                    }
                    else{
                        $container = $($parentNav.data('container'));
                        $parentContainer = $($parentNav.data('parent-container'));
                        itemSelector = $parentNav.data('item-selector');
                        pagedKey = $parentNav.data('ajax_request_id');
                    }

                    ajaxType = 'load_widget';
                    if($kitWrap.find('div[data-widget_current_query="yes"]').length > 0){
                        ajaxType = 'load_fullpage';
                        pagedKey = 'paged';
                    }

                    $parentNav.addClass('doing-ajax');
                    $parentContainer.addClass('doing-ajax');

                    var success_func = function (res, israw) {

                        var $response;

                        if(israw){
                            var jsoncontent = JSON.parse(res);
                            var contentraw = jsoncontent['template_content'];
                            $response = $('<div></div>').html(contentraw);
                        }
                        else{
                            $response = $(res);
                        }

                        var $data = $response.find('.elementor-element-' + widgetId + ' ' + itemSelector);

                        if ($parentContainer.find('.lakit-carousel').length > 0) {
                            var swiper = $parentContainer.find('.lakit-carousel').get(0).swiper;
                            swiper.removeAllSlides();
                            swiper.appendSlide($data);
                        }
                        else if ($container.data('isotope')) {
                            $container.isotope('remove', $container.isotope('getItemElements'));
                            $container.isotope('insert', $data);
                            $(document).trigger('lastudio-kit/masonry/calculate-item-sizes', [$parentContainer, true]);

                            $('img', $data).imagesLoaded().progress(function (instance, image) {
                                var $image = $(image.img),
                                    $parentItem = $image.closest(itemSelector);
                                $parentItem.addClass('item-loaded');
                                $container.isotope('layout');
                            });
                        }
                        else {
                            $data.addClass('fadeIn animated').appendTo($container.empty());
                        }

                        $parentContainer.removeClass('doing-ajax');
                        $parentNav.removeClass('doing-ajax lakit-ajax-load-first');

                        if ($response.find( '.elementor-element-' + widgetId + ' .lakit-ajax-pagination').length) {
                            var $new_pagination = $response.find( '.elementor-element-' + widgetId + ' .lakit-ajax-pagination');
                            $parentNav.replaceWith($new_pagination);
                            $parentNav = $new_pagination;
                        }
                        else {
                            $parentNav.addClass('nothingtoshow');
                        }

                        if($response.find( '.elementor-element-' + widgetId + ' .woocommerce-result-count').length && $kitWrap.find('.woocommerce-result-count').length){
                            $kitWrap.find('.woocommerce-result-count').replaceWith($response.find( '.elementor-element-' + widgetId + ' .woocommerce-result-count'));
                        }

                        $('html,body').animate({
                            'scrollTop': $parentContainer.offset().top - getHeaderHeight() - 50
                        }, 400);

                        $('body').trigger('jetpack-lazy-images-load');
                        $(document).trigger('lastudio-kit/ajax-pagination/success', {
                            parentContainer: $parentContainer,
                            contentHolder: $container,
                            pagination: $parentNav
                        });
                    };

                    var url_request = e.target.href.replace(/^\//, '');

                    if( ajaxType == 'load_widget' ){
                        var _tmpURL = url_request;
                        url_request = LaStudioKits.addQueryArg(LaStudioKitSettings.widgetApiUrl, 'template_id', templateId);
                        url_request = LaStudioKits.addQueryArg(url_request, 'widget_id', widgetId);
                        url_request = LaStudioKits.addQueryArg(url_request, 'dev', LaStudioKitSettings.devMode);
                        url_request = LaStudioKits.addQueryArg(url_request, pagedKey, LaStudioKits.getUrlParameter(pagedKey, _tmpURL));
                        url_request = LaStudioKits.addQueryArg(url_request, 'lakitpagedkey', pagedKey);
                    }

                    url_request = LaStudioKits.removeURLParameter(url_request, '_');

                    var ajaxOpts = {
                        url: url_request,
                        type: "GET",
                        cache: true,
                        dataType: 'html',
                        ajax_request_id: LaStudioKits.getUrlParameter(pagedKey, url_request),
                        success: function (res) {
                            if(ajaxType == 'load_widget'){
                                success_func(res, true);
                            }
                            else{
                                success_func(res, false);
                            }
                        }
                    };

                    $.ajax(ajaxOpts)

                })
                .on('click', '[data-lakit-element-link]', function (e) {
                    var $wrapper = $(this),
                        data = $wrapper.data('lakit-element-link'),
                        id = $wrapper.data('id'),
                        anchor = document.createElement('a'),
                        anchorReal;

                    anchor.id = 'lastudio-wrapper-link-' + id;
                    anchor.href = data.url;
                    anchor.target = data.is_external ? '_blank' : '_self';
                    anchor.rel = data.nofollow ? 'nofollow noreferer' : '';
                    anchor.style.display = 'none';

                    document.body.appendChild(anchor);

                    anchorReal = document.getElementById(anchor.id);
                    anchorReal.click();
                    anchorReal.remove();
                })
                .on('click', '.lakit-search__popup-trigger,.lakit-search__popup-close', function (e) {
                    var $this = $(this),
                        $widget = $this.closest('.lakit-search'),
                        $input = $('.lakit-search__field', $widget),
                        activeClass = 'lakit-search-popup-active',
                        transitionIn = 'lakit-transition-in',
                        transitionOut = 'lakit-transition-out';

                    if (!$widget.hasClass(activeClass)) {
                        $widget.addClass(transitionIn);
                        setTimeout(function () {
                            $widget.removeClass(transitionIn);
                            $widget.addClass(activeClass);
                        }, 300);
                        $input.focus();
                    } else {
                        $widget.removeClass(activeClass);
                        $widget.addClass(transitionOut);
                        setTimeout(function () {
                            $widget.removeClass(transitionOut);
                        }, 300);
                    }
                })
                .on('click', '.lakit-masonry_filter .lakit-masonry_filter-item', function (e){
                    e.preventDefault();
                    var $wrap = $(this).closest('.lakit-masonry_filter'),
                        $isotopeInstance = $($wrap.data('lakitmasonry_container')),
                        _filter = $(this).data('filter');
                    if (_filter != '*'){
                        _filter = '.' + _filter;
                    }
                    if ($isotopeInstance.data('isotope')) {
                        $(this).addClass('active').siblings('.lakit-masonry_filter-item').removeClass('active');
                        $isotopeInstance.isotope({
                            filter: _filter
                        });
                    }
                })
                .on('lastudio-kit/masonry/calculate-item-sizes', function (e, $isotope_container, need_relayout) {
                    var masonrySettings = $isotope_container.data('lakitmasonry_layouts') || false,
                        $isotopeInstance = $isotope_container.find($isotope_container.data('lakitmasonry_wrap'));

                    if (masonrySettings !== false) {
                        var win_w = $(window).width(),
                            selector = $isotope_container.data('lakitmasonry_itemselector');

                        if (win_w > 1023) {
                            $isotope_container.addClass('cover-img-bg');

                            var _base_w = masonrySettings.item_width,
                                _base_h = masonrySettings.item_height,
                                _container_width_base = masonrySettings.container_width,
                                _container_width = $isotope_container.width(),
                                item_per_page = Math.round(_container_width_base / _base_w),
                                itemwidth = Math.floor(_container_width / item_per_page),
                                margin = parseInt($isotope_container.data('lakitmasonry_itemmargin') || 0),
                                dimension = (_base_h ? parseFloat(_base_w / _base_h) : 1),
                                layout_mapping = masonrySettings.layout || [{w: 1, h: 1}];

                            var _idx = 0,
                                _idx2 = 0;

                            $(selector, $isotope_container).each(function () {
                                $(this)
                                    .css({
                                        'width': Math.floor(itemwidth * (layout_mapping[_idx]['w']) - (margin / 2)),
                                        'height': _base_h ? Math.floor(itemwidth / dimension * (layout_mapping[_idx]['h'])) : 'auto'
                                    })
                                    .addClass('lakit-disable-cols-style');
                                    // .attr('data-lakitmansory--item_setting', JSON.stringify({
                                    //     'index': _idx2 + '_' + _idx,
                                    //     'itemwidth': itemwidth,
                                    //     'layout': layout_mapping[_idx]
                                    // }));
                                _idx++;
                                if (_idx == layout_mapping.length) {
                                    _idx2++;
                                    _idx = 0;
                                }
                            });
                        } else {
                            $isotope_container.removeClass('lakit-masonry--cover-bg');
                            $(selector, $isotope_container).css({
                                'width': '',
                                'height': ''
                            }).removeClass('lakit-disable-cols-style');
                        }
                    }
                    if (need_relayout) {
                        if ($isotopeInstance.data('isotope')) {
                            $isotopeInstance.isotope('layout');
                        }
                    }
                })
                .on('keyup', function (e) {
                    if(e.keyCode == 27){
                        $('.lakit-search').removeClass('lakit-search-popup-active');
                        $('.lakit-cart').removeClass('lakit-cart-open');
                        $('.lakit-hamburger-panel').removeClass('open-state');
                        $('html').removeClass('lakit-hamburger-panel-visible');
                    }
                })
                .on('lastudio-kit/section/calculate-container-width', '.elementor-top-section', function (e){
                    var $scope = $(this);
                    var $child_container = $scope.find('>.elementor-container');
                    $child_container.css('--lakit-section-width', $child_container.width() + 'px');
                    $(window).on('resize', function (){
                        $child_container.css('--lakit-section-width', $child_container.width() + 'px');
                    });
                })
                .on('click', function (e){
                    if( $(e.target).closest('.lakit-cart').length == 0 ) {
                        $('.lakit-cart').removeClass('lakit-cart-open');
                    }
                })
        },
        isEditMode: function () {
            return Boolean(elementorFrontend.isEditMode());
        },
        mobileAndTabletCheck: function () {
            return ( (('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0)) && (window.innerWidth < 1400) )
        },
        onSearchSectionActivated: function ($scope) {
            if (!elementor) {
                return;
            }
            if (!window.LaStudioKitEditor) {
                return;
            }
            if (!window.LaStudioKitEditor.activeSection) {
                return;
            }
            var section = window.LaStudioKitEditor.activeSection;
            var isPopup = -1 !== ['section_popup_style', 'section_popup_close_style', 'section_form_style'].indexOf(section);
            if (isPopup) {
                $scope.find('.lakit-search').addClass('lakit-search-popup-active');
            } else {
                $scope.find('.lakit-search').removeClass('lakit-search-popup-active');
            }
        },
        loadStyle: function (style, uri) {

            if (LaStudioKits.addedStyles.hasOwnProperty(style) && LaStudioKits.addedStyles[style] === uri) {
                return style;
            }

            if (!uri) {
                return;
            }

            LaStudioKits.addedStyles[style] = uri;

            return new Promise(function (resolve, reject) {
                var tag = document.createElement('link');

                tag.id = style + '-css';
                tag.rel = 'stylesheet';
                tag.href = uri;
                tag.type = 'text/css';
                tag.media = 'all';
                tag.onload = function () {
                    resolve(style);
                };
                tag.onerror = function () {
                    reject(`Can not load css file "${uri}"`);
                }

                document.head.appendChild(tag);
            });
        },
        loadScriptAsync: function (script, uri, callback, async) {
            if (LaStudioKits.addedScripts.hasOwnProperty(script)) {
                return script;
            }
            if (!uri) {
                return;
            }
            LaStudioKits.addedScripts[script] = uri;
            return new Promise(function (resolve, reject) {
                var tag = document.createElement('script');

                tag.src = uri;
                tag.id = script + '-js';
                tag.async = async;
                tag.onload = function () {
                    resolve(script);
                    if ("function" == typeof callback && "number" != typeof callback.nodeType) {
                        callback();
                    }
                };

                tag.onerror = function () {
                    reject(`Can not load javascript file "${uri}"`);
                    if ("function" == typeof callback && "number" != typeof callback.nodeType) {
                        callback();
                    }
                }

                document.head.appendChild(tag);
            });
        },
        elementorFrontendInit: function ($container) {
            $(window).trigger('elementor/frontend/init');
            $container.find('[data-element_type]').each(function () {
                var $this = $(this),
                    elementType = $this.data('element_type');

                if (!elementType) {
                    return;
                }

                try {

                    if ('widget' === elementType) {
                        elementType = $this.data('widget_type');
                        window.elementorFrontend.hooks.doAction('frontend/element_ready/widget', $this, $);
                    }

                    window.elementorFrontend.hooks.doAction('frontend/element_ready/global', $this, $);
                    window.elementorFrontend.hooks.doAction('frontend/element_ready/' + elementType, $this, $);

                } catch (err) {
                    console.log(err);
                    $this.remove();
                    return false;
                }
            });
        },
        initAnimationsHandlers: function ($selector) {
            $selector.find('[data-element_type]').each(function () {
                var $this = $(this),
                    elementType = $this.data('element_type');

                if (!elementType) {
                    return;
                }

                window.elementorFrontend.hooks.doAction('frontend/element_ready/global', $this, $);
            });
        },
        hamburgerPanel: function ($scope) {

            var wid = $scope.data('id');

            if ($('.lakit-site-wrapper > .elementor-location-header > .elementor-section-wrap > .lakit-burger-wrapall').length == 0) {
                $(document.createElement('div')).addClass('lakit-burger-wrapall').appendTo($('.lakit-site-wrapper > .elementor-location-header > .elementor-section-wrap'));
            }

            var $burger_wrap_all = $('.lakit-burger-wrapall');

            if ($('.elementor-element-' + wid, $burger_wrap_all).length) {
                $('.elementor-element-' + wid, $burger_wrap_all).remove();
            }

            var $new_el = $(document.createElement('div')).addClass('elementor-element elementor-element-' + wid).append($scope.find('>.elementor-widget-container').clone()).appendTo($burger_wrap_all);

            //$burger_wrap_all.append('<div class="elementor-element elementor-element-'+wid+'">'+$scope.find('>.elementor-widget-container').html()+'</div>');

            var $new_scope = $('.elementor-element-' + wid, $burger_wrap_all);
            $('.lakit-hamburger-panel__instance', $scope).remove();
            $('.lakit-hamburger-panel__toggle', $new_scope).remove();

            var $panel_old = $('.lakit-hamburger-panel', $scope),
                $panel = $('.lakit-hamburger-panel', $new_scope),
                $toggleButton = $('.lakit-hamburger-panel__toggle', $scope),
                $instance = $('.lakit-hamburger-panel__instance', $new_scope),
                $cover = $('.lakit-hamburger-panel__cover', $new_scope),
                $inner = $('.lakit-hamburger-panel__inner', $new_scope),
                $closeButton = $('.lakit-hamburger-panel__close-button', $new_scope),
                $panelContent = $('.lakit-hamburger-panel__content', $new_scope),
                scrollOffset,
                timer,
                $html = $('html'),
                settings = $panel.data('settings') || {};

            if ('ontouchend' in window || 'ontouchstart' in window) {
                $toggleButton.on('touchstart', function (event) {
                    scrollOffset = $(window).scrollTop();
                });

                $toggleButton.on('touchend', function (event) {
                    if (scrollOffset !== $(window).scrollTop()) {
                        return false;
                    }

                    if (timer) {
                        clearTimeout(timer);
                    }

                    if (!$panel.hasClass('open-state')) {
                        timer = setTimeout(function () {
                            $panel.addClass('open-state');
                            $panel_old.addClass('open-state');
                        }, 10);
                        $html.addClass('lakit-hamburger-panel-visible');
                        LaStudioKits.elementorFrontendInit($inner);

                        if (settings['ajaxTemplate']) {
                            LaStudioKits.ajaxLoadTemplate($panelContent, $panel);
                        }
                    } else {
                        $panel.removeClass('open-state');
                        $panel_old.removeClass('open-state');
                        $html.removeClass('lakit-hamburger-panel-visible');
                    }
                });

            } else {
                $toggleButton.on('click', function (event) {

                    if (!$panel.hasClass('open-state')) {
                        $panel.addClass('open-state');
                        $panel_old.addClass('open-state');
                        $html.addClass('lakit-hamburger-panel-visible');
                        LaStudioKits.elementorFrontendInit($inner);

                        if (settings['ajaxTemplate']) {
                            LaStudioKits.ajaxLoadTemplate($panelContent, $panel);
                        }
                    } else {
                        $panel.removeClass('open-state');
                        $panel_old.removeClass('open-state');
                        $html.removeClass('lakit-hamburger-panel-visible');
                    }
                });
            }

            $closeButton.on('click', function (event) {

                if (!$panel.hasClass('open-state')) {
                    $panel.addClass('open-state');
                    $panel_old.addClass('open-state');
                    $html.addClass('lakit-hamburger-panel-visible');
                    LaStudioKits.initAnimationsHandlers($inner);
                } else {
                    $panel.removeClass('open-state');
                    $panel_old.removeClass('open-state');
                    $html.removeClass('lakit-hamburger-panel-visible');
                }
            });

            $(document).on('click.lakitHamburgerPanel', function (event) {
                if (($(event.target).closest($toggleButton).length || $(event.target).closest($instance).length)
                    && !$(event.target).closest($cover).length
                ) {
                    return;
                }

                if (!$panel.hasClass('open-state')) {
                    return;
                }

                $panel.removeClass('open-state');
                $panel_old.removeClass('open-state');

                if (!$(event.target).closest('.lakit-hamburger-panel__toggle').length) {
                    $html.removeClass('lakit-hamburger-panel-visible');
                }

                event.stopPropagation();
            });
        },
        /**
         * [ajaxLoadTemplate description]
         * @param  {[Object]} $panelContent [jQuery Object]
         * @param  {[Object]} $target [jQuery Object]
         * @return {[type]}        [description]
         */
        ajaxLoadTemplate: function ($panelContent, $target) {
            var $contentHolder = $panelContent,
                templateLoaded = $contentHolder.data('template-loaded') || false,
                templateId = $contentHolder.data('template-id'),
                loader = $('.lakit-tpl-panel-loader', $contentHolder);

            if (templateLoaded) {
                return false;
            }

            $(document).trigger('lastudio-kit/ajax-load-template/before', {
                target: $target,
                contentHolder: $contentHolder
            });

            $contentHolder.data('template-loaded', true);

            $.ajax({
                type: 'GET',
                url: window.LaStudioKitSettings.templateApiUrl,
                dataType: 'json',
                data: {
                    'id': templateId,
                    'current_url': window.location.href,
                    'current_url_no_search': window.location.href.replace(window.location.search, ''),
                    'dev': window.LaStudioKitSettings.devMode
                },
                success: function (response, textStatus, jqXHR) {
                    var templateContent = response['template_content'],
                        templateScripts = response['template_scripts'],
                        templateStyles = response['template_styles'];

                    for (var scriptHandler in templateScripts) {
                        if($( '#' + scriptHandler + '-js').length == 0){
                            LaStudioKits.addedAssetsPromises.push(LaStudioKits.loadScriptAsync(scriptHandler, templateScripts[scriptHandler], '', true));
                        }
                    }

                    for (var styleHandler in templateStyles) {
                        if($('#' + styleHandler + '-css').length == 0) {
                            LaStudioKits.addedAssetsPromises.push(LaStudioKits.loadStyle(styleHandler, templateStyles[styleHandler]));
                        }
                    }

                    Promise.all(LaStudioKits.addedAssetsPromises).then(function (value) {
                        loader.remove();
                        $contentHolder.append(templateContent);
                        LaStudioKits.elementorFrontendInit($contentHolder);

                        $(document).trigger('lastudio-kit/ajax-load-template/after', {
                            target: $target,
                            contentHolder: $contentHolder,
                            response: response
                        });
                    }, function (reason) {
                        console.log(`An error occurred while insert the asset resources, however we still need to insert content. Reason detail: "${reason}"`);
                        loader.remove();
                        $contentHolder.append(templateContent);
                        LaStudioKits.elementorFrontendInit($contentHolder);

                        $(document).trigger('lastudio-kit/ajax-load-template/after', {
                            target: $target,
                            contentHolder: $contentHolder,
                            response: response
                        });
                    });
                }
            });//end
        },
        wooCard: function ($scope) {
            if (window.LaStudioKitEditor && window.LaStudioKitEditor.activeSection) {
                let section = window.LaStudioKitEditor.activeSection,
                    isCart = -1 !== ['cart_list_style', 'cart_list_items_style', 'cart_buttons_style'].indexOf(section);

                $('.widget_shopping_cart_content').empty();
                $(document.body).trigger('wc_fragment_refresh');
            }

            var $target = $('.lakit-cart', $scope),
                $toggle = $('.lakit-cart__heading-link', $target),
                settings = $target.data('settings'),
                firstMouseEvent = true;

            switch (settings['triggerType']) {
                case 'hover':
                    hoverType();
                    break;
                case 'click':
                    clickType();
                    break;
            }

            $target.on('click', '.lakit-cart__close-button', function (event) {
                if (!$target.hasClass('lakit-cart-open-proccess')) {
                    $target.toggleClass('lakit-cart-open');
                }
            });

            function hoverType() {
                var scrollOffset = 0;
                if ('ontouchend' in window || 'ontouchstart' in window) {
                    $target.on('touchstart', function (event) {
                        scrollOffset = $(window).scrollTop();
                    });

                    $target.on('touchend', function (event) {

                        if (scrollOffset !== $(window).scrollTop()) {
                            return false;
                        }

                        var $this = $(this);

                        if ($this.hasClass('lakit-cart-open-proccess')) {
                            return;
                        }

                        setTimeout(function () {
                            $this.toggleClass('lakit-cart-open');
                        }, 10);
                    });

                    $(document).on('touchend', function (event) {

                        if ($(event.target).closest($target).length) {
                            return;
                        }

                        if ($target.hasClass('lakit-cart-open-proccess')) {
                            return;
                        }

                        if (!$target.hasClass('lakit-cart-open')) {
                            return;
                        }

                        $target.removeClass('lakit-cart-open');
                    });
                } else {

                    $target.on('mouseenter mouseleave', function (event) {

                        if (firstMouseEvent && 'mouseleave' === event.type) {
                            return;
                        }

                        if (firstMouseEvent && 'mouseenter' === event.type) {
                            firstMouseEvent = false;
                        }

                        if (!$(this).hasClass('lakit-cart-open-proccess')) {
                            $(this).toggleClass('lakit-cart-open');
                        }
                    });
                }
            }

            function clickType() {
                $toggle.on('click', function (event) {
                    event.preventDefault();

                    if (!$target.hasClass('lakit-cart-open-proccess')) {
                        $target.toggleClass('lakit-cart-open');
                    }
                });
            }
        },
        wooGallery: function ($scope) {
            if (LaStudioKits.isEditMode()) {
                $('.woocommerce-product-gallery', $scope).wc_product_gallery();
            }

            var centerdots_cb = function () {
                if ($scope.find('.flex-viewport').length) {
                    $scope.find('.woocommerce-product-gallery').css('--singleproduct-thumbs-height', $scope.find('.flex-viewport').height() + 'px');
                    if ($scope.find('.woocommerce-product-gallery__trigger').length) {
                        $scope.find('.woocommerce-product-gallery__trigger').appendTo($scope.find('.flex-viewport'));
                    }
                    if ($('.la-custom-badge', $scope).length) {
                        $('.la-custom-badge', $scope).prependTo($scope.find('.flex-viewport'));
                    }
                    if ($('.woocommerce-product-gallery__actions', $scope).length) {
                        $('.woocommerce-product-gallery__actions', $scope).prependTo($scope.find('.flex-viewport'));
                    }
                }

                var $nav = $scope.find('.flex-direction-nav');
                if ($nav.length && $scope.find('.flex-viewport').length) {
                    $nav.appendTo($scope.find('.flex-viewport'))
                }

                var $thumbs = $scope.find('.flex-control-thumbs').get(0);
                if (typeof $thumbs === "undefined" || $scope.find('.lakit-product-images').hasClass('layout-type-wc')) {
                    return;
                }

                var pos = {top: 0, left: 0, x: 0, y: 0};
                var mouseDownHandler = function (e) {
                    $thumbs.style.cursor = 'grabbing';
                    $thumbs.style.userSelect = 'none';

                    pos = {
                        left: $thumbs.scrollLeft,
                        top: $thumbs.scrollTop,
                        // Get the current mouse position
                        x: e.clientX,
                        y: e.clientY,
                    };

                    document.addEventListener('mousemove', mouseMoveHandler);
                    document.addEventListener('mouseup', mouseUpHandler);
                };

                var mouseMoveHandler = function (e) {
                    // How far the mouse has been moved
                    const dx = e.clientX - pos.x;
                    const dy = e.clientY - pos.y;

                    // Scroll the element
                    $thumbs.scrollTop = pos.top - dy;
                    $thumbs.scrollLeft = pos.left - dx;
                };

                var mouseUpHandler = function () {
                    $thumbs.style.cursor = 'grab';
                    $thumbs.style.removeProperty('user-select');

                    document.removeEventListener('mousemove', mouseMoveHandler);
                    document.removeEventListener('mouseup', mouseUpHandler);
                };
                // Attach the handler
                $thumbs.addEventListener('mousedown', mouseDownHandler);
            }
            setTimeout(centerdots_cb, 300);

            function flexdestroy($els) {
                $els.each(function () {
                    var $el = jQuery(this);
                    var $elClean = $el.clone();

                    $elClean.find('.flex-viewport').children().unwrap();
                    $elClean.find('img.zoomImg, .woocommerce-product-gallery__trigger').remove();
                    $elClean
                        .removeClass('flexslider')
                        .find('.clone, .flex-direction-nav, .flex-control-nav')
                        .remove()
                        .end()
                        .find('*').removeAttr('style').removeClass(function (index, css) {
                        // If element is SVG css has an Object inside (?)
                        if (typeof css === 'string') {
                            return (css.match(/\bflex\S+/g) || []).join(' ');
                        }
                    });
                    $elClean.insertBefore($el);
                    $el.remove();
                });

            }

            if ($scope.find('.lakit-product-images').hasClass('layout-type-5') || $scope.find('.lakit-product-images').hasClass('layout-type-6')) {
                flexdestroy($scope.find('.lakit-product-images'));
            }


            var $gallery_target = $scope.find('.woocommerce-product-gallery');

            var data_columns = parseInt($gallery_target.data('columns'));
            if($scope.find('.lakit-product-images').hasClass('layout-type-4')){
                data_columns = parseInt($gallery_target.closest('.elementor-lakit-wooproduct-images').css('--singleproduct-image-column'));
            }

            if ($gallery_target.find('.woocommerce-product-gallery__image').length <= data_columns) {
                $gallery_target.addClass('center-thumb');
                if($scope.find('.lakit-product-images').hasClass('layout-type-4')){
                    flexdestroy($scope.find('.lakit-product-images'));
                    $gallery_target = $scope.find('.woocommerce-product-gallery');
                }
            }

            $scope.find('.woocommerce-product-gallery__image a').attr('data-elementor-open-lightbox', 'no');
            $scope.find('.woocommerce-product-gallery__image').wrapInner('<div class="zoomouter"><div class="zoominner"></div></div>');
            var initZoom = function (zoomTarget) {

                var zoom_enabled = $.isFunction($.fn.zoom) && wc_single_product_params.zoom_enabled;
                if (!zoom_enabled) {
                    return;
                }
                var galleryWidth = $gallery_target.width(),
                    zoomEnabled = false,
                    zoom_options;

                if($scope.find('.lakit-product-images').hasClass('layout-type-4')){
                    galleryWidth = $(zoomTarget).width()
                }

                $(zoomTarget).each(function (index, target) {
                    var image = $(target).find('img');

                    if (image.data('large_image_width') > galleryWidth) {
                        zoomEnabled = true;
                        return false;
                    }
                });

                // But only zoom if the img is larger than its container.
                if (zoomEnabled) {

                    try {
                        zoom_options = $.extend({
                            touch: false
                        }, wc_single_product_params.zoom_options);
                    } catch (ex) {
                        zoom_options = {
                            touch: false
                        };
                    }

                    if ('ontouchstart' in document.documentElement) {
                        zoom_options.on = 'click';
                    }

                    zoomTarget.trigger('zoom.destroy');
                    zoomTarget.zoom(zoom_options);

                }
            }

            initZoom($gallery_target.find('.woocommerce-product-gallery__image .zoominner'));
        },
        wooTabs: function ($scope) {
            var $tabs = $scope.find('.wc-tabs-wrapper').first();
            if ($tabs) {
                $tabs.wrapInner('<div class="lakit-wc-tabs--content"></div>');
                $tabs.find('.wc-tabs').wrapAll('<div class="lakit-wc-tabs--controls"></div>');
                $tabs.find('.lakit-wc-tabs--controls').prependTo($tabs);
                $tabs.find('.wc-tab').wrapInner('<div class="tab-content"></div>');
                $tabs.find('.wc-tab').each(function () {
                    var _html = $('#' + $(this).attr('aria-labelledby')).html();
                    $(this).prepend('<div class="wc-tab-title">' + _html + '</div>');
                });
                $('.wc-tab-title a', $tabs).wrapInner('<span></span>');
                $('.wc-tab-title a', $tabs).on('click', function (e) {
                    e.preventDefault();
                    $tabs.find('.wc-tabs').find('li[aria-controls="' + $(this).attr('href').replace('#', '') + '"]').toggleClass('active').siblings().removeClass('active');
                    $(this).closest('.wc-tab').toggleClass('active').siblings().removeClass('active');
                });
                $('.wc-tabs li a', $tabs).on('click', function (e) {
                    var $wrapper = $(this).closest('.wc-tabs-wrapper, .woocommerce-tabs');
                    $wrapper.find($(this).attr('href')).addClass('active').siblings().removeClass('active');
                });
                $('.wc-tabs li', $tabs).removeClass('active');
                $('.wc-tab-title a', $tabs).first().trigger('click');
            }
        },
        animatedBoxHandler: function ($scope) {

            var $target = $scope.find('.lakit-animated-box'),
                toogleEvents = 'mouseenter mouseleave',
                scrollOffset = $(window).scrollTop(),
                firstMouseEvent = true;

            if (!$target.length) {
                return;
            }

            if ('ontouchend' in window || 'ontouchstart' in window) {
                $target.on('touchstart', function (event) {
                    scrollOffset = $(window).scrollTop();
                });

                $target.on('touchend', function (event) {

                    if (scrollOffset !== $(window).scrollTop()) {
                        return false;
                    }

                    if (!$(this).hasClass('flipped-stop')) {
                        $(this).toggleClass('flipped');
                    }
                });

            } else {
                $target.on(toogleEvents, function (event) {

                    if (firstMouseEvent && 'mouseleave' === event.type) {
                        return;
                    }

                    if (firstMouseEvent && 'mouseenter' === event.type) {
                        firstMouseEvent = false;
                    }

                    if (!$(this).hasClass('flipped-stop')) {
                        $(this).toggleClass('flipped');
                    }
                });
            }
        },

        ajaxTemplateHelper: {
            processInsertData: function ($el, templateContent, template_id){
                if (templateContent) {
                    $el.html(templateContent);
                    LaStudioKits.elementorFrontendInit($el);

                    if($el.find('div[data-lakit_ajax_loadtemplate]:not(.template-loaded,.is-loading)').length){
                        LaStudioKits.log('found template in ajax content');
                        LaStudioKits.ajaxTemplateHelper.init();
                    }
                }
                $('.elementor-motion-effects-element').trigger('resize');
                $('body').trigger('jetpack-lazy-images-load');
                $(document).trigger('lastudio-kit/ajax-load-template/after', {
                    target_id: template_id,
                    contentHolder: $el,
                    response: templateContent
                });
            },
            elementorContentRender: function ( $el, templateContent, template_id ){
                Promise.all(LaStudioKits.addedAssetsPromises).then(function (value) {
                    LaStudioKits.ajaxTemplateHelper.processInsertData($el, templateContent, template_id);
                }, function (reason){
                    LaStudioKits.log(`An error occurred while insert the asset resources, however we still need to insert content. Reason detail: "${reason}"`);
                    LaStudioKits.ajaxTemplateHelper.processInsertData($el, templateContent, template_id);
                })
            },
            templateRenderCallback: function ( response, template_id ){
                var templateContent = response['template_content'],
                    templateScripts = response['template_scripts'],
                    templateStyles = response['template_styles'],
                    template_metadata = response['template_metadata'];

                for (var scriptHandler in templateScripts) {
                    if($( '#' + scriptHandler + '-js').length == 0) {
                        LaStudioKits.addedAssetsPromises.push(LaStudioKits.loadScriptAsync(scriptHandler, templateScripts[scriptHandler], '', true));
                    }
                }

                for (var styleHandler in templateStyles) {
                    if($( '#' + styleHandler + '-css').length == 0) {
                        LaStudioKits.addedAssetsPromises.push(LaStudioKits.loadStyle(styleHandler, templateStyles[styleHandler]));
                    }
                }

                document.querySelectorAll('body:not(.elementor-editor-active) div[data-lakit_ajax_loadtemplate][data-cache-id="' + template_id + '"]:not(.template-loaded)').forEach(function (elm) {
                    elm.classList.remove('is-loading');
                    elm.classList.add('template-loaded');
                    LaStudioKits.ajaxTemplateHelper.elementorContentRender($(elm), templateContent, template_id);
                });

                var wpbar = document.querySelectorAll('#wp-admin-bar-elementor_edit_page ul');

                if (wpbar && typeof template_metadata['title'] !== "undefined") {
                    setTimeout(function () {
                        var _tid = 'wp-admin-bar-elementor_edit_doc_'+template_metadata['id'];
                        if($('#'+_tid).length == 0){
                            $('<li id="'+_tid+'" class="elementor-general-section"><a class="ab-item" href="' + template_metadata['href'] + '"><span class="elementor-edit-link-title">' + template_metadata['title'] + '</span><span class="elementor-edit-link-type">' + template_metadata['sub_title'] + '</span></a></li>').prependTo($(wpbar));
                        }
                    }, 2000);
                }
            },
            init: function (){
                var templates = document.querySelectorAll('body:not(.elementor-editor-active) div[data-lakit_ajax_loadtemplate]:not(.template-loaded)');
                if (templates.length) {
                    var template_ids = [];
                    templates.forEach(function (el) {
                        if (!el.classList.contains('is-loading') && !el.classList.contains('template-loaded')) {
                            el.classList.add('is-loading');
                            var _cache_key = el.getAttribute('data-template-id');
                            if (!template_ids.includes(_cache_key)) {
                                template_ids.push(_cache_key);
                            }
                            el.setAttribute('data-cache-id', _cache_key);
                        }
                    });

                    template_ids.forEach(function (templateId){
                        var cached_key = 'lakitTpl_' + templateId;
                        var cached_key2 = 'lakitTplExist_' + templateId;

                        if(LaStudioKits.localCache.exist(cached_key2)){
                            if(LaStudioKits.localCache.exist(cached_key)){
                                LaStudioKits.ajaxTemplateHelper.templateRenderCallback(LaStudioKits.localCache.get(cached_key), templateId);
                            }
                            return;
                        }
                        LaStudioKits.localCache.set(cached_key2, 'yes');

                        if(LaStudioKits.localCache.exist(cached_key)){
                            LaStudioKits.ajaxTemplateHelper.templateRenderCallback(LaStudioKits.localCache.get(cached_key), templateId);
                        }
                        else{

                            $(document).trigger('lastudio-kit/ajax-load-template/before', {
                                target_id: templateId
                            });

                            var browserCacheKey = LaStudioKits.localCache.cache_key + '_' + LaStudioKits.localCache.hashCode(templateId);
                            var expiry = LaStudioKits.localCache.timeout;
                            var ajaxData = {
                                'id': templateId,
                                'current_url': window.location.href,
                                'current_url_no_search': window.location.href.replace(window.location.search, ''),
                                'dev': window.LaStudioKitSettings.devMode
                            }

                            var ajaxCalling = function (){
                                $.ajax({
                                    type: 'GET',
                                    url: window.LaStudioKitSettings.templateApiUrl,
                                    dataType: 'json',
                                    data: ajaxData,
                                    success: function (response, textStatus, jqXHR) {
                                        LaStudioKits.localCache.set(cached_key, response);
                                        LaStudioKits.ajaxTemplateHelper.templateRenderCallback(response, templateId);
                                        try{
                                            LaStudioKits.log('setup browser cache for ' + browserCacheKey);
                                            localStorage.setItem(browserCacheKey, JSON.stringify(response));
                                            localStorage.setItem(browserCacheKey + ':ts', Date.now());
                                        }
                                        catch (ajax_ex1){
                                            LaStudioKits.log('Cannot setup browser cache');
                                        }
                                    }
                                });
                            }

                            try{
                                var browserCached = localStorage.getItem(browserCacheKey);
                                var browserWhenCached = localStorage.getItem(browserCacheKey + ':ts');

                                if (browserCached !== null && browserWhenCached !== null) {
                                    var age = (Date.now() - browserWhenCached) / 1000;
                                    if (age < expiry) {
                                        LaStudioKits.log('render from cache for ' + browserCacheKey);
                                        LaStudioKits.ajaxTemplateHelper.templateRenderCallback(JSON.parse(browserCached), templateId);
                                        return;
                                    }
                                    else {
                                        LaStudioKits.log('clear browser cache key for ' + browserCacheKey);
                                        // We need to clean up this old key
                                        localStorage.removeItem(browserCacheKey);
                                        localStorage.removeItem(browserCacheKey + ':ts');
                                    }
                                }
                                LaStudioKits.log('run ajaxCalling() for ' + templateId);
                                ajaxCalling();
                            }
                            catch (ajax_ex) {
                                LaStudioKits.log('Cannot setup browser cache ajaxCalling() for ' + templateId);
                                ajaxCalling();
                            }
                        }

                    });
                }
            }
        }
    }

    $(window).on('elementor/frontend/init', function () {

        elementor.hooks.addAction('frontend/element_ready/lakit-advanced-carousel.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-slides.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-posts.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
            LaStudioKits.initMasonry($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-portfolio.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
            LaStudioKits.initMasonry($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-images-layout.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
            LaStudioKits.initMasonry($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-team-member.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
            LaStudioKits.initMasonry($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-testimonials.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
            LaStudioKits.initMasonry($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-search.default', function ($scope) {
            LaStudioKits.onSearchSectionActivated($scope);
            $(document).on('click', function (event) {

                var $widget = $scope.find('.lakit-search'),
                    $popupToggle = $('.lakit-search__popup-trigger', $widget),
                    $popupContent = $('.lakit-search__popup-content', $widget),
                    activeClass = 'lakit-search-popup-active',
                    transitionOut = 'lakit-transition-out';

                if ($(event.target).closest($popupToggle).length || $(event.target).closest($popupContent).length) {
                    return;
                }

                if (!$widget.hasClass(activeClass)) {
                    return;
                }

                $widget.removeClass(activeClass);
                $widget.addClass(transitionOut);
                setTimeout(function () {
                    $widget.removeClass(transitionOut);
                }, 300);

                event.stopPropagation();
            });
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-hamburger-panel.default', function ($scope) {
            LaStudioKits.hamburgerPanel($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-menucart.default', function ($scope) {
            LaStudioKits.wooCard($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-animated-box.default', function ($scope) {
            LaStudioKits.animatedBoxHandler($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-wooproducts.default', function ($scope) {
            LaStudioKits.initCarousel($scope);
            LaStudioKits.initMasonry($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-wooproduct-images.default', function ($scope) {
            LaStudioKits.wooGallery($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/lakit-wooproduct-datatabs.default', function ($scope) {
            LaStudioKits.wooTabs($scope);
        });

        elementor.hooks.addAction('frontend/element_ready/section', function ($scope) {
            if( $scope.hasClass('elementor-top-section') ) {
                $scope.trigger('lastudio-kit/section/calculate-container-width');
            }
        });

        LaStudioKits.initCustomHandlers();

    });

    window.LaStudioKits = LaStudioKits;

    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        if (options.cache) {
            //Here is our identifier for the cache. Maybe have a better, safer ID (it depends on the object string representation here) ?
            // on $.ajax call we could also set an ID in originalOptions
            var id = LaStudioKits.removeURLParameter(originalOptions.url, '_') + ("undefined" !== typeof originalOptions.ajax_request_id ? JSON.stringify(originalOptions.ajax_request_id) : "undefined" !== typeof originalOptions.data ? JSON.stringify(originalOptions.data) : '');
            id = LaStudioKits.localCache.hashCode(id.replace(/null$/g, ''));
            options.cache = false;

            options.beforeSend = function () {
                if (!LaStudioKits.localCache.exist(id)) {
                    jqXHR.promise().done(function (data, textStatus) {
                        LaStudioKits.localCache.set(id, data);
                    });
                }
                return true;
            };
        }
    });
    $.ajaxTransport("+*", function (options, originalOptions, jqXHR) {
        //same here, careful because options.url has already been through jQuery processing
        var id = LaStudioKits.removeURLParameter(originalOptions.url, '_') + ("undefined" !== typeof originalOptions.ajax_request_id ? JSON.stringify(originalOptions.ajax_request_id) : "undefined" !== typeof originalOptions.data ? JSON.stringify(originalOptions.data) : '');
        options.cache = false;
        id = LaStudioKits.localCache.hashCode(id.replace(/null$/g, ''));

        if (LaStudioKits.localCache.exist(id)) {
            return {
                send: function (headers, completeCallback) {
                    setTimeout(function () {
                        completeCallback(200, "OK", [LaStudioKits.localCache.get(id)]);
                    }, 50);
                },
                abort: function () {
                    /* abort code, nothing needed here I guess... */
                }
            };
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        if(!LaStudioKits.isPageSpeed()){
            LaStudioKits.localCache.validCache(false);
            LaStudioKits.ajaxTemplateHelper.init();
        }
    });

}(jQuery, window.elementorFrontend));
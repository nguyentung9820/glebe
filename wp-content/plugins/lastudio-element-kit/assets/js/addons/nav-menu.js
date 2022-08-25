( function( $, elementor ) {

    "use strict";

    $( window ).on( 'elementor/frontend/init', function (){
        elementor.hooks.addAction( 'frontend/element_ready/lakit-nav-menu.default', function ( $scope ){
            if ( $scope.data( 'initialized' ) ) {
                return;
            }

            $scope.data( 'initialized', true );

            var hoverClass        = 'lakit-nav-hover',
                hoverOutClass     = 'lakit-nav-hover-out',
                mobileActiveClass = 'lakit-mobile-menu-active',
                _has_mobile_bkp   = false;

            if($scope.find('.lakit-mobile-menu').length){
                _has_mobile_bkp = $scope.find('.lakit-mobile-menu').data('mobile-breakpoint');
            }

            function checkActiveMobileTrigger(){
                if(_has_mobile_bkp !== false && $(window).width() <= _has_mobile_bkp){
                    $scope.find('.lakit-mobile-menu').addClass('lakit-active--mbmenu');
                }
                else{
                    $scope.find('.lakit-mobile-menu').removeClass('lakit-active--mbmenu');
                }
            }
            checkActiveMobileTrigger();
            $(window).on('resize', checkActiveMobileTrigger);

            $scope.find( '.lakit-nav:not(.lakit-nav--vertical-sub-bottom)' ).hoverIntent({
                over: function() {
                    $( this ).addClass( hoverClass );
                },
                out: function() {
                    var $this = $( this );
                    $this.removeClass( hoverClass );
                    $this.addClass( hoverOutClass );
                    setTimeout( function() {
                        $this.removeClass( hoverOutClass );
                    }, 200 );
                },
                timeout: 200,
                selector: '.menu-item-has-children'
            });

            if ( LaStudioKits.mobileAndTabletCheck() ) {
                $scope.find( '.lakit-nav:not(.lakit-nav--vertical-sub-bottom)' ).on( 'touchstart.lakitNavMenu', '.menu-item > a', touchStartItem );
                $scope.find( '.lakit-nav:not(.lakit-nav--vertical-sub-bottom)' ).on( 'touchend.lakitNavMenu', '.menu-item > a', touchEndItem );

                $( document ).on( 'touchstart.lakitNavMenu', prepareHideSubMenus );
                $( document ).on( 'touchend.lakitNavMenu', hideSubMenus );
            } else {
                $scope.find( '.lakit-nav:not(.lakit-nav--vertical-sub-bottom)' ).on( 'click.lakitNavMenu', '.menu-item > a', clickItem );
            }

            if ( ! LaStudioKits.isEditMode() ) {
                initMenuAnchorsHandler();
            }

            function touchStartItem( event ) {
                var $currentTarget = $( event.currentTarget ),
                    $this = $currentTarget.closest( '.menu-item' );

                $this.data( 'offset', $( window ).scrollTop() );
                $this.data( 'elemOffset', $this.offset().top );
            }

            function touchEndItem( event ) {
                var $this,
                    $siblingsItems,
                    $link,
                    $currentTarget,
                    subMenu,
                    offset,
                    elemOffset,
                    $hamburgerPanel;

                event.preventDefault();

                $currentTarget  = $( event.currentTarget );
                $this           = $currentTarget.closest( '.menu-item' );
                $siblingsItems  = $this.siblings( '.menu-item.menu-item-has-children' );
                $link           = $( '> a', $this );
                subMenu         = $( '.lakit-nav__sub:first', $this );
                offset          = $this.data( 'offset' );
                elemOffset      = $this.data( 'elemOffset' );
                $hamburgerPanel = $this.closest( '.lakit-hamburger-panel' );

                if ( offset !== $( window ).scrollTop() || elemOffset !== $this.offset().top ) {
                    return false;
                }

                if ( $siblingsItems[0] ) {
                    $siblingsItems.removeClass( hoverClass );
                    $( '.menu-item-has-children', $siblingsItems ).removeClass( hoverClass );
                }

                if ( ! $( '.lakit-nav__sub', $this )[0] || $this.hasClass( hoverClass ) ) {
                    $link.trigger( 'click' ); // Need for a smooth scroll when clicking on an anchor link
                    window.location.href = $link.attr( 'href' );

                    if ( $scope.find( '.lakit-nav-wrap' ).hasClass( mobileActiveClass ) ) {
                        $scope.find( '.lakit-nav-wrap' ).removeClass( mobileActiveClass );
                    }

                    if ( $hamburgerPanel[0] && $hamburgerPanel.hasClass( 'open-state' ) ) {
                        $hamburgerPanel.removeClass( 'open-state' );
                        $( 'html' ).removeClass( 'lakit-hamburger-panel-visible' );
                    }

                    return false;
                }

                if ( subMenu[0] ) {
                    $this.addClass( hoverClass );
                }
            }

            function clickItem( event ) {
                var $currentTarget  = $( event.currentTarget ),
                    $menuItem       = $currentTarget.closest( '.menu-item' ),
                    $hamburgerPanel = $menuItem.closest( '.lakit-hamburger-panel' );

                if ( ! $menuItem.hasClass( 'menu-item-has-children' ) || $menuItem.hasClass( hoverClass ) ) {

                    if ( $hamburgerPanel[0] && $hamburgerPanel.hasClass( 'open-state' ) ) {
                        $hamburgerPanel.removeClass( 'open-state' );
                        $( 'html' ).removeClass( 'lakit-hamburger-panel-visible' );
                    }

                }
            }

            var scrollOffset;

            function prepareHideSubMenus( event ) {
                scrollOffset = $( window ).scrollTop();
            }

            function hideSubMenus( event ) {
                var $menu = $scope.find( '.lakit-nav' );

                if ( 'touchend' === event.type && scrollOffset !== $( window ).scrollTop() ) {
                    return;
                }

                if ( $( event.target ).closest( $menu ).length ) {
                    return;
                }

                var $openMenuItems = $( '.menu-item-has-children.' + hoverClass, $menu );

                if ( ! $openMenuItems[0] ) {
                    return;
                }

                $openMenuItems.removeClass( hoverClass );
                $openMenuItems.addClass( hoverOutClass );

                setTimeout( function() {
                    $openMenuItems.removeClass( hoverOutClass );
                }, 200 );

                if ( $menu.hasClass( 'lakit-nav--vertical-sub-bottom' ) ) {
                    $( '.lakit-nav__sub', $openMenuItems ).slideUp( 200 );
                }

                event.stopPropagation();
            }

            // START Vertical Layout: Sub-menu at the bottom
            $scope.find( '.lakit-nav--vertical-sub-bottom' ).on( 'click.lakitNavMenu', '.menu-item > a', verticalSubBottomHandler );

            function verticalSubBottomHandler( event ) {
                var $currentTarget  = $( event.currentTarget ),
                    $menuItem       = $currentTarget.closest( '.menu-item' ),
                    $siblingsItems  = $menuItem.siblings( '.menu-item.menu-item-has-children' ),
                    $subMenu        = $( '.lakit-nav__sub:first', $menuItem ),
                    $hamburgerPanel = $menuItem.closest( '.lakit-hamburger-panel' );

                if ( ! $menuItem.hasClass( 'menu-item-has-children' ) || $menuItem.hasClass( hoverClass ) ) {

                    if ( $scope.find( '.lakit-nav-wrap' ).hasClass( mobileActiveClass ) ) {
                        $scope.find( '.lakit-nav-wrap' ).removeClass( mobileActiveClass );
                    }

                    if ( $hamburgerPanel[0] && $hamburgerPanel.hasClass( 'open-state' ) ) {
                        $hamburgerPanel.removeClass( 'open-state' );
                        $( 'html' ).removeClass( 'lakit-hamburger-panel-visible' );
                    }

                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                if ( $siblingsItems[0] ) {
                    $siblingsItems.removeClass( hoverClass );
                    $( '.menu-item-has-children', $siblingsItems ).removeClass( hoverClass );
                    $( '.lakit-nav__sub', $siblingsItems ).slideUp( 200 );
                }

                if ( $subMenu[0] ) {
                    $subMenu.slideDown( 200 );
                    $menuItem.addClass( hoverClass );
                }
            }

            $( document ).on( 'click.lakitNavMenu', hideVerticalSubBottomMenus );

            function hideVerticalSubBottomMenus( event ) {
                if ( ! $scope.find( '.lakit-nav' ).hasClass( 'lakit-nav--vertical-sub-bottom' ) ) {
                    return;
                }

                hideSubMenus( event );
            }
            // END Vertical Layout: Sub-menu at the bottom

            // Mobile trigger click event
            $( '.lakit-nav__mobile-trigger', $scope ).on( 'click.lakitNavMenu', function( event ) {
                $( this ).closest( '.lakit-nav-wrap' ).toggleClass( mobileActiveClass );
            } );

            // START Mobile Layout: Left-side, Right-side
            if ( 'ontouchend' in window ) {
                $( document ).on( 'touchend.lakitMobileNavMenu', removeMobileActiveClass );
            } else {
                $( document ).on( 'click.lakitMobileNavMenu', removeMobileActiveClass );
            }

            function removeMobileActiveClass( event ) {
                var mobileLayout = $scope.find( '.lakit-nav-wrap' ).data( 'mobile-layout' ),
                    $navWrap     = $scope.find( '.lakit-nav-wrap' ),
                    $trigger     = $scope.find( '.lakit-nav__mobile-trigger' ),
                    $menu        = $scope.find( '.lakit-nav' );

                if ( 'left-side' !== mobileLayout && 'right-side' !== mobileLayout ) {
                    return;
                }

                if ( 'touchend' === event.type && scrollOffset !== $( window ).scrollTop() ) {
                    return;
                }

                if ( $( event.target ).closest( $trigger ).length || $( event.target ).closest( $menu ).length ) {
                    return;
                }

                if ( ! $navWrap.hasClass( mobileActiveClass ) ) {
                    return;
                }

                $navWrap.removeClass( mobileActiveClass );

                event.stopPropagation();
            }

            $( '.lakit-nav__mobile-close-btn', $scope ).on( 'click.lakitMobileNavMenu', function( event ) {
                $( this ).closest( '.lakit-nav-wrap' ).removeClass( mobileActiveClass );
            } );

            // END Mobile Layout: Left-side, Right-side

            // START Mobile Layout: Full-width
            var initMobileFullWidthCss = false;

            setFullWidthMenuPosition();
            $( window ).on( 'resize.lakitMobileNavMenu', setFullWidthMenuPosition );

            function setFullWidthMenuPosition() {
                var mobileLayout = $scope.find( '.lakit-nav-wrap' ).data( 'mobile-layout' );

                if ( 'full-width' !== mobileLayout ) {
                    return;
                }

                var $menu = $scope.find( '.lakit-nav' ),
                    currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

                if ( 'mobile' !== currentDeviceMode ) {
                    if ( initMobileFullWidthCss ) {
                        $menu.css( { 'left': '' } );
                        initMobileFullWidthCss = false;
                    }
                    return;
                }

                if ( initMobileFullWidthCss ) {
                    $menu.css( { 'left': '' } );
                }

                var offset = - $menu.offset().left;

                $menu.css( { 'left': offset } );
                initMobileFullWidthCss = true;
            }
            // END Mobile Layout: Full-width

            // Menu Anchors Handler
            function initMenuAnchorsHandler() {
                var $anchorLinks = $scope.find( '.menu-item-link[href*="#"]' );

                if ( $anchorLinks[0] ) {
                    $anchorLinks.each( function() {
                        if ( '' !== this.hash && location.pathname === this.pathname ) {
                            menuAnchorHandler( $( this ) );
                        }
                    } );
                }
            }

            function menuAnchorHandler( $anchorLink ) {
                var anchorHash = $anchorLink[0].hash,
                    activeClass = 'current-menu-item',
                    rootMargin = '-50% 0% -50%',
                    $anchor;

                try {
                    $anchor = $( decodeURIComponent( anchorHash ) );
                } catch (e) {
                    return;
                }

                if ( !$anchor[0] ) {
                    return;
                }

                if ( $anchor.hasClass( 'elementor-menu-anchor' ) ) {
                    rootMargin = '300px 0% -300px';
                }

                var observer = new IntersectionObserver( function( entries ) {
                        if ( entries[0].isIntersecting ) {
                            $anchorLink.parent( '.menu-item' ).addClass( activeClass );
                        } else {
                            $anchorLink.parent( '.menu-item' ).removeClass( activeClass );
                        }
                    },
                    {
                        rootMargin: rootMargin
                    }
                );

                observer.observe( $anchor[0] );
            }

            if ( LaStudioKits.isEditMode() ) {
                $scope.data( 'initialized', false );
            }
        } );
    } );

}( jQuery, window.elementorFrontend ) );
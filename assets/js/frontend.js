/**
 * FloatedAds - Frontend JavaScript
 * Vanilla JS implementation using WordPress REST API.
 * No jQuery dependency required.
 *
 * @version 2.0.0
 */
(function() {
    'use strict';

    var FloatedAds = {
        /**
         * Initialize the plugin.
         */
        init: function() {
            if (typeof fladsData === 'undefined') {
                return;
            }

            this.data = fladsData;
            this.banners = this.data.banners || {};

            // Render banners immediately with data already inlined
            this.renderBanners();

            // Re-position on resize using ResizeObserver/requestAnimationFrame
            this.setupResizeHandler();
        },

        /**
         * Render all configured banners.
         */
        renderBanners: function() {
            var self = this;

            // Left banner
            if (this.banners.left && this.banners.left.active) {
                this.renderSideBanner('left', this.banners.left);
            }

            // Right banner
            if (this.banners.right && this.banners.right.active) {
                this.renderSideBanner('right', this.banners.right);
            }

            // Mobile banner
            if (this.banners.mobile && this.banners.mobile.active) {
                this.renderMobileBanner(this.banners.mobile);
            }
        },

        /**
         * Render a side banner (left or right).
         *
         * @param {string} side   'left' or 'right'
         * @param {object} config Banner configuration.
         */
        renderSideBanner: function(side, config) {
            var isMobile = this.data.isMobile === 1;
            var minWidth = this.data.minScreenWidth;

            // Don't render on mobile for side banners
            if (isMobile && config.position !== 'mobile') {
                return;
            }

            // Check screen width
            if (window.innerWidth < minWidth) {
                return;
            }

            var divId = side === 'left' ? 'divAdLeft' : 'divAdRight';
            var container = document.getElementById(divId);

            // If container already exists from server-side render, just position it
            if (container) {
                container.style.display = 'block';
                this.positionBanner(side, container, config);
                return;
            }

            // Create banner container
            container = document.createElement('div');
            container.id = divId;
            container.className = 'flads-banner flads-banner-' + side;
            if (config.sticky) {
                container.classList.add('flads-sticky');
            }
            container.style.display = 'none';

            // Build banner content
            if (config.type === 'image') {
                var link = config.link || '#';
                var img = document.createElement('img');
                img.src = config.src;
                img.alt = '';
                img.width = config.width || '';
                img.height = config.height || '';

                if (link !== '#') {
                    var anchor = document.createElement('a');
                    anchor.href = link;
                    anchor.target = '_blank';
                    anchor.rel = 'noopener noreferrer';
                    anchor.appendChild(img);
                    container.appendChild(anchor);
                } else {
                    container.appendChild(img);
                }
            } else if (config.type === 'code') {
                container.innerHTML = config.code || '';
            }

            document.body.appendChild(container);
            container.style.display = 'block';
            this.positionBanner(side, container, config);
        },

        /**
         * Position a side banner based on configuration.
         *
         * @param {string}   side      'left' or 'right'
         * @param {Element}  container The banner element.
         * @param {object}   config    Banner configuration.
         */
        positionBanner: function(side, container, config) {
            var mainWidth = this.data.mainContentWidth;
            var leftAdjust = this.data.marginLeft;
            var rightAdjust = this.data.marginRight;
            var topAdjust = this.data.marginTop;

            var bw = config.width || 0;
            var halfGap = (window.innerWidth - mainWidth) / 2;

            if (side === 'left') {
                var leftPos = halfGap - bw - leftAdjust;
                container.style.left = leftPos + 'px';
                container.style.top = topAdjust + 'px';
                container.style.width = (bw > 0 ? bw + 'px' : 'auto');
            } else if (side === 'right') {
                var rightPos = halfGap + mainWidth + rightAdjust;
                container.style.left = rightPos + 'px';
                container.style.top = topAdjust + 'px';
                container.style.width = (bw > 0 ? bw + 'px' : 'auto');
            }

            // Set height if available
            if (config.height) {
                container.style.height = config.height + 'px';
            }
        },

        /**
         * Render the mobile footer banner.
         *
         * @param {object} config Mobile banner configuration.
         */
        renderMobileBanner: function(config) {
            var container = document.createElement('div');
            container.className = 'flads-mobile-banner';

            // Close button
            var closeBtn = document.createElement('span');
            closeBtn.className = 'flads-close-btn';
            closeBtn.addEventListener('click', function() {
                container.classList.remove('visible');
                setTimeout(function() {
                    container.style.display = 'none';
                }, 300);
            });
            container.appendChild(closeBtn);

            // Banner content
            if (config.type === 'image') {
                var link = config.link || '#';
                var img = document.createElement('img');
                img.src = config.src;
                img.alt = '';

                if (link !== '#') {
                    var anchor = document.createElement('a');
                    anchor.href = link;
                    anchor.target = '_blank';
                    anchor.rel = 'noopener noreferrer';
                    anchor.appendChild(img);
                    container.appendChild(anchor);
                } else {
                    container.appendChild(img);
                }
            } else if (config.type === 'code') {
                var codeWrapper = document.createElement('div');
                codeWrapper.innerHTML = config.code || '';
                container.appendChild(codeWrapper);
            }

            document.body.appendChild(container);

            // Show with slide-up animation
            requestAnimationFrame(function() {
                container.style.display = 'block';
                requestAnimationFrame(function() {
                    container.classList.add('visible');
                });
            });
        },

        /**
         * Set up resize handler using requestAnimationFrame for performance.
         */
        setupResizeHandler: function() {
            var self = this;
            var resizeTimeout;

            window.addEventListener('resize', function() {
                if (resizeTimeout) {
                    cancelAnimationFrame(resizeTimeout);
                }
                resizeTimeout = requestAnimationFrame(function() {
                    self.repositionBanners();
                });
            });
        },

        /**
         * Reposition all visible banners on resize.
         */
        repositionBanners: function() {
            var self = this;
            var minWidth = this.data.minScreenWidth;
            var shouldShow = window.innerWidth >= minWidth;

            ['left', 'right'].forEach(function(side) {
                var config = self.banners[side];
                if (!config || !config.active) return;

                var divId = side === 'left' ? 'divAdLeft' : 'divAdRight';
                var container = document.getElementById(divId);

                if (!container) return;

                if (shouldShow) {
                    container.style.display = 'block';
                    self.positionBanner(side, container, config);
                } else {
                    container.style.display = 'none';
                }
            });
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            FloatedAds.init();
        });
    } else {
        FloatedAds.init();
    }

})();
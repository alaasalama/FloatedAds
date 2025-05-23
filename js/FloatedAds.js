jQuery(document).ready(function($) {
    // Use strict mode to prevent common errors
    'use strict';

    // Main function to initialize and load ads
    function ShowAdDiv() {
        var clientWidth = $(window).width();

        // Left Banner
        if (floatedAdsGlobalData.left_banner_on === 1 && floatedAdsGlobalData.device_is_mobile === 0) {
            if (clientWidth >= floatedAdsGlobalData.screen_min_width) {
                var leftBannerData = {
                    action: 'floatedads_left_banner',
                    nonce: floatedAdsGlobalData.left_banner_nonce,
                    clientwidth_php: clientWidth, // Though not used by PHP template directly, good to pass for context
                    screen_w: floatedAdsGlobalData.screen_min_width, // Same as above
                    left_banner_is_on: floatedAdsGlobalData.left_banner_on,
                    left_banner_is_image: floatedAdsGlobalData.left_banner_is_image_js,
                    left_banner_is_code: floatedAdsGlobalData.left_banner_is_code_js,
                    LeftBannerW: floatedAdsGlobalData.LeftBannerW,
                    LeftBannerH: floatedAdsGlobalData.LeftBannerH,
                    left_banner_link: floatedAdsGlobalData.left_banner_link,
                    left_banner_url: floatedAdsGlobalData.left_banner_url,
                    left_banner_custom: floatedAdsGlobalData.left_banner_custom,
                    left_banner_sticky_php: floatedAdsGlobalData.left_banner_sticky_js
                };

                $.ajax({
                    type: "POST",
                    url: floatedAdsGlobalData.ajax_url,
                    data: leftBannerData,
                    success: function(response) {
                        if (response.success && response.data.html) {
                            $("body").append(response.data.html);
                            var $divAdLeft = $("#divAdLeft");
                            if ($divAdLeft.length) { // Check if the div was actually added
                                $divAdLeft.show(); // Make it visible
                                FloatBannerLeft(); // Position it
                                if (floatedAdsGlobalData.left_banner_sticky_js === 1) {
                                    $divAdLeft.addClass("fixed_float");
                                }
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("FloatedAds AJAX Error (Left Banner): " + textStatus, errorThrown);
                    }
                });
            }
        }

        // Right Banner
        if (floatedAdsGlobalData.right_banner_on === 1 && floatedAdsGlobalData.device_is_mobile === 0) {
            if (clientWidth >= floatedAdsGlobalData.screen_min_width) {
                var rightBannerData = {
                    action: 'floatedads_right_banner',
                    nonce: floatedAdsGlobalData.right_banner_nonce,
                    clientwidth_php: clientWidth,
                    screen_w: floatedAdsGlobalData.screen_min_width,
                    right_banner_is_on: floatedAdsGlobalData.right_banner_on,
                    right_banner_is_image: floatedAdsGlobalData.right_banner_is_image_js,
                    right_banner_is_code: floatedAdsGlobalData.right_banner_is_code_js,
                    RightBannerW: floatedAdsGlobalData.RightBannerW,
                    RightBannerH: floatedAdsGlobalData.RightBannerH,
                    right_banner_link: floatedAdsGlobalData.right_banner_link,
                    right_banner_url: floatedAdsGlobalData.right_banner_url,
                    right_banner_custom: floatedAdsGlobalData.right_banner_custom,
                    right_banner_sticky_php: floatedAdsGlobalData.right_banner_sticky_js
                };

                $.ajax({
                    type: "POST",
                    url: floatedAdsGlobalData.ajax_url,
                    data: rightBannerData,
                    success: function(response) {
                        if (response.success && response.data.html) {
                            $("body").append(response.data.html);
                            var $divAdRight = $("#divAdRight");
                            if ($divAdRight.length) {
                                $divAdRight.show();
                                FloatBannerRight();
                                if (floatedAdsGlobalData.right_banner_sticky_js === 1) {
                                    $divAdRight.addClass("fixed_float");
                                }
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("FloatedAds AJAX Error (Right Banner): " + textStatus, errorThrown);
                    }
                });
            }
        }

        // Mobile Banner
        if (floatedAdsGlobalData.mobile_banner_on === 1 && floatedAdsGlobalData.device_is_mobile === 1) {
             var mobileBannerData = {
                action: 'floatedads_mobile_banner',
                nonce: floatedAdsGlobalData.mobile_banner_nonce,
                mobile_banner_is_on: floatedAdsGlobalData.mobile_banner_on,
                mobile_banner_is_image: floatedAdsGlobalData.mobile_banner_is_image,
                mobile_banner_is_code: floatedAdsGlobalData.mobile_banner_is_code,
                MobileBannerW: floatedAdsGlobalData.MobileBannerW,
                MobileBannerH: floatedAdsGlobalData.MobileBannerH,
                mobile_banner_link: floatedAdsGlobalData.mobile_banner_link,
                mobile_banner_url: floatedAdsGlobalData.mobile_banner_url,
                mobile_banner_custom: floatedAdsGlobalData.mobile_banner_custom
                // No sticky for mobile banner
            };

            $.ajax({
                type: "POST",
                url: floatedAdsGlobalData.ajax_url,
                data: mobileBannerData,
                success: function(response) {
                    if (response.success && response.data.html) {
                        $("body").append(response.data.html);
                        var $bottomBanner = $("#bottom_banner");
                        if ($bottomBanner.length && $bottomBanner.html().trim().length > 0) {
                             $bottomBanner.show(); // Make it visible
                            // Style and attach event to close button if it exists
                            var $closeBtn = $bottomBanner.find('.close-btn');
                            if ($closeBtn.length) {
                                var marginLeft = floatedAdsGlobalData.MobileBannerW + 20; // Default for image
                                if (floatedAdsGlobalData.mobile_banner_is_code === 1) {
                                     marginLeft = floatedAdsGlobalData.MobileBannerW / 2;
                                }
                                var marginTop = floatedAdsGlobalData.MobileBannerH * (-1);
                                
                                // The CSS for the close button might need to be adjusted based on the final HTML structure
                                // and whether it's inside or outside the banner content.
                                // This attempts to replicate the previous logic.
                                $closeBtn.css({
                                    'position': 'absolute', // Ensure it's positioned relative to the banner
                                    'left': marginLeft + 'px',
                                    'top': marginTop + 'px' // This might need review based on actual banner layout
                                });
                                // Event delegation for close button
                                // jQuery('body').on('click', '#bottom_banner .close-btn', function() {
                                //     jQuery('#bottom_banner').fadeOut();
                                // }); 
                                // Simpler since we have $closeBtn directly now:
                                $closeBtn.on('click', function() {
                                    $bottomBanner.fadeOut();
                                });
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("FloatedAds AJAX Error (Mobile Banner): " + textStatus, errorThrown);
                }
            });
        }
    }

    // Calculate left banner position
    function FloatBannerLeft() {
        if (floatedAdsGlobalData.left_banner_on !== 1 || floatedAdsGlobalData.device_is_mobile === 1) return;

        var mainContentW = floatedAdsGlobalData.MainContentW;
        var leftBannerW = floatedAdsGlobalData.LeftBannerW;
        var leftAdjust = floatedAdsGlobalData.LeftAdjust;
        var topAdjust = floatedAdsGlobalData.TopAdjust;
        
        var startLX = ((document.body.clientWidth - mainContentW) / 2) - (leftBannerW + leftAdjust);
        var startLY = topAdjust;
        
        var $e2 = $("#divAdLeft");
        if ($e2.length) {
            $e2.css({
                'left': startLX + 'px',
                'top': startLY + 'px'
            });
        }
    }

    // Calculate right banner position
    function FloatBannerRight() {
        if (floatedAdsGlobalData.right_banner_on !== 1 || floatedAdsGlobalData.device_is_mobile === 1) return;

        var mainContentW = floatedAdsGlobalData.MainContentW;
        var rightBannerW = floatedAdsGlobalData.RightBannerW; // Ensure this is the actual width used
        var rightAdjust = floatedAdsGlobalData.RightAdjust;
        var topAdjust = floatedAdsGlobalData.TopAdjust;

        var startRX = ((document.body.clientWidth - mainContentW) / 2) + (mainContentW + rightAdjust);
        var startRY = topAdjust;
        
        var $e2 = $("#divAdRight");
        if ($e2.length) {
            $e2.css({
                'left': startRX + 'px',
                'top': startRY + 'px'
            });
        }
    }

    // Re-position banners when window resizes
    function AdsWindowResize() {
        // Recalculate clientWidth on resize
        var clientWidth = $(window).width();

        if (floatedAdsGlobalData.left_banner_on === 1 && floatedAdsGlobalData.device_is_mobile === 0) {
            if (clientWidth < floatedAdsGlobalData.screen_min_width) {
                $('#divAdLeft').hide(); // Hide if window is too small
            } else {
                $('#divAdLeft').show();
                FloatBannerLeft();
            }
        }
        if (floatedAdsGlobalData.right_banner_on === 1 && floatedAdsGlobalData.device_is_mobile === 0) {
            if (clientWidth < floatedAdsGlobalData.screen_min_width) {
                $('#divAdRight').hide(); // Hide if window is too small
            } else {
                $('#divAdRight').show();
                FloatBannerRight();
            }
        }
        // Mobile banner is typically not repositioned by FloatBannerLeft/Right,
        // its layout is usually CSS-driven (e.g., fixed to bottom).
    }

    // Initial call to display ads
    ShowAdDiv();

    // Attach resize event listener
    $(window).on('resize', AdsWindowResize);

    // Specific logic for mobile close button using event delegation,
    // in case the #bottom_banner is added after this script initially runs
    // and the direct binding in success callback has issues.
    // This is a more robust way for dynamically added content.
    $('body').on('click', '#bottom_banner .close-btn', function() {
        $(this).closest('#bottom_banner').fadeOut();
    });

});

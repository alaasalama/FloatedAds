/**
 * FloatedAds - Admin JavaScript
 * Handles media uploader, conditional fields, and tab interactions.
 */

(function($) {
    'use strict';

    /**
     * Initialize the admin functionality.
     */
    function init() {
        initTabs();
        initConditionalToggles();
        initMediaUploader();
        initRemoveImage();
    }

    /**
     * Initialize client-side tab switching.
     * All tabs are rendered in the DOM, hidden/shown via JS.
     */
    function initTabs() {
        $('#flads-tab-nav').on('click', '.flads-tab', function(e) {
            e.preventDefault();

            var $tab = $(this);
            var tabId = $tab.data('tab');

            // Update active tab button.
            $tab.closest('#flads-tab-nav').find('.flads-tab').removeClass('active');
            $tab.addClass('active');

            // Show the corresponding panel, hide others.
            $('.flads-tab-panel').hide();
            $('#flads-tab-' + tabId).show();
        });
    }

    /**
     * Toggle conditional fields when checkbox is clicked.
     */
    function initConditionalToggles() {
        $('.flads-conditional-checkbox').on('change', function() {
            var $fields = $(this).closest('.flads-conditional-wrapper').find('.flads-conditional-fields');
            if ($(this).is(':checked')) {
                $fields.slideDown(200);
            } else {
                $fields.slideUp(200);
            }
        });
    }

    /**
     * Initialize WordPress media uploader for image fields.
     */
    function initMediaUploader() {
        var frame;

        $('.flads-upload-image').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var fieldId = $button.data('field');
            var $wrapper = $button.closest('.flads-image-upload-wrapper');
            var $preview = $wrapper.find('.flads-image-preview');
            var $uploadDiv = $wrapper.find('.flads-image-upload');
            var $srcInput = $wrapper.find('input[id$="_src"]');
            var $idInput = $wrapper.find('input[id$="_id"]');
            var $img = $preview.find('img');

            // If the media frame already exists, reopen it.
            if (frame) {
                frame.open();
                return;
            }

            // Create a new media frame.
            frame = wp.media({
                title: fladsAdmin.mediaTitle || 'Select Banner Image',
                button: {
                    text: fladsAdmin.mediaButton || 'Use as Banner'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });

            // When an image is selected.
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();

                // Update preview.
                $img.attr('src', attachment.url);
                $preview.show();
                $uploadDiv.hide();

                // Update hidden inputs.
                $srcInput.val(attachment.url);
                $idInput.val(attachment.id);
            });

            frame.open();
        });
    }

    /**
     * Handle remove image button.
     */
    function initRemoveImage() {
        $('.flads-remove-image').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var fieldId = $button.data('field');
            var $wrapper = $button.closest('.flads-image-upload-wrapper');
            var $preview = $wrapper.find('.flads-image-preview');
            var $uploadDiv = $wrapper.find('.flads-image-upload');
            var $srcInput = $wrapper.find('input[id$="_src"]');
            var $idInput = $wrapper.find('input[id$="_id"]');
            var $img = $preview.find('img');

            // Clear preview.
            $img.attr('src', '');
            $preview.hide();
            $uploadDiv.show();

            // Clear hidden inputs.
            $srcInput.val('');
            $idInput.val('');
        });
    }

    // Initialize on document ready.
    $(document).ready(init);

})(jQuery);
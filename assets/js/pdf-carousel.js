/**
 * PDF Carousel Footer - JavaScript
 * 
 * @package PDF_Carousel_Footer
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Set PDF.js worker source
    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = pcfConfig.workerSrc;
    }

    /**
     * Render PDF thumbnail on canvas
     * @param {jQuery} $slide - The slide element containing the PDF
     */
    function renderPdfThumb($slide) {
        var url = $slide.data('pdfUrl');
        if (!url || !window.pdfjsLib) {
            return;
        }

        var canvas = $slide.find('canvas')[0];
        if (!canvas) {
            return;
        }

        var ctx = canvas.getContext('2d');
        var desiredWidth = canvas.dataset.w ? parseInt(canvas.dataset.w, 10) : canvas.width;
        var desiredHeight = canvas.dataset.h ? parseInt(canvas.dataset.h, 10) : canvas.height;

        // Load PDF and render first page as thumbnail
        pdfjsLib.getDocument(url).promise
            .then(function(pdf) {
                return pdf.getPage(1);
            })
            .then(function(page) {
                var viewport = page.getViewport({ scale: 1 });
                var scale = Math.min(desiredWidth / viewport.width, desiredHeight / viewport.height);
                var scaledViewport = page.getViewport({ scale: scale });

                canvas.width = Math.floor(scaledViewport.width);
                canvas.height = Math.floor(scaledViewport.height);

                var renderTask = page.render({
                    canvasContext: ctx,
                    viewport: scaledViewport
                });

                return renderTask.promise;
            })
            .catch(function(err) {
                console.warn('PDF Carousel: Failed to render PDF thumbnail:', err);
                // Show fallback link
                var $fallback = $slide.find('.pcf-thumb-fallback');
                $fallback.show();
            });
    }

    /**
     * Initialize PDF carousel
     */
    function initPdfCarousel() {
        $('.pcf-pdf-carousel').not('.pcf-initialized').each(function() {
            var $el = $(this);
            
            // Render thumbnails for each slide
            $el.find('.pcf-slide').each(function() {
                renderPdfThumb($(this));
            });
            
            // Mark as initialized
            $el.addClass('pcf-initialized');
            
            // Initialize Slick carousel
            $el.slick(pcfConfig.sliderSettings);
            
            // Add CSS class based on arrow setting
            if (!pcfConfig.sliderSettings.arrows) {
                $el.addClass('no-arrows');
            }
        });
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initPdfCarousel();
    });

    // Re-initialize on AJAX content load (for dynamic content)
    $(document).on('DOMNodeInserted', function(e) {
        if ($(e.target).find('.pcf-pdf-carousel').length > 0) {
            initPdfCarousel();
        }
    });

})(jQuery);

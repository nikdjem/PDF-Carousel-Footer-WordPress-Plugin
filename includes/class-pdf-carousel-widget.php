<?php
/**
 * PDF Carousel Footer Widget Class
 *
 * @package PDF_Carousel_Footer
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PDF_Carousel_Footer_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'pdf_carousel_footer_widget',
            'PDF Carousel Footer',
            array('description' => 'Displays a PDF carousel using Slick Carousel.')
        );
    }

    public function widget($args, $instance)
    {
        $plugin = PDF_Carousel_Footer_Plugin::instance();
        $plugin->should_enqueue_assets = true;
        echo $args['before_widget'];
        echo $plugin->render_carousel_html();
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        echo '<p>Configure PDFs and options in Settings → PDF Carousel Footer.</p>';
    }

    public function update($new_instance, $old_instance)
    {
        return $old_instance;
    }
}

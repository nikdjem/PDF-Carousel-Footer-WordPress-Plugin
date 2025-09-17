<?php
/**
 * Main PDF Carousel Footer Plugin Class
 *
 * @package PDF_Carousel_Footer
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PDF_Carousel_Footer_Plugin
{
    const OPTION_KEY = 'pcf_options';
    private static $instance = null;
    private $did_register_assets = false;
    public $should_enqueue_assets = false;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('widgets_init', array($this, 'register_widget'));
        add_shortcode('pdf_carousel_footer', array($this, 'shortcode_handler'));
        add_action('wp_enqueue_scripts', array($this, 'maybe_register_assets'));
        add_action('wp_footer', array($this, 'enqueue_if_needed'), 1);
        add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path(__FILE__) . '../PDF Carousel Footer.php'), array($this, 'add_settings_link'));
    }

    public function default_options()
    {
        return array(
            'pdf_urls' => '',
            'width' => 600,
            'height' => 400,
            'autoplay' => 0,
            'autoplay_speed' => 4000,
            'show_arrows' => 1,
            'show_dots' => 1,
            'infinite_loop' => 1,
            'slides_to_show' => 1,
            'slides_to_scroll' => 1,
            'fade_effect' => 0,
            'pause_on_hover' => 1,
        );
    }

    public function get_options()
    {
        $options = get_option(self::OPTION_KEY, array());
        return wp_parse_args($options, $this->default_options());
    }

    public function register_settings_page()
    {
        add_options_page(
            'PDF Carousel Footer',
            'PDF Carousel Footer',
            'manage_options',
            'pdf-carousel-footer',
            array($this, 'render_settings_page')
        );
    }

    public function add_settings_link($links)
    {
        $url = admin_url('options-general.php?page=pdf-carousel-footer');
        $settings_link = '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'pdf-carousel-footer') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function register_settings()
    {
        register_setting('pcf_settings_group', self::OPTION_KEY, array($this, 'sanitize_options'));

        // PDF Settings Section
        add_settings_section('pcf_pdf', 'PDF Settings', '__return_false', 'pdf-carousel-footer');
        add_settings_field('pdf_urls', 'PDF URLs (comma separated)', array($this, 'field_pdf_urls'), 'pdf-carousel-footer', 'pcf_pdf');
        add_settings_field('width', 'PDF Width (px)', array($this, 'field_width'), 'pdf-carousel-footer', 'pcf_pdf');
        add_settings_field('height', 'PDF Height (px)', array($this, 'field_height'), 'pdf-carousel-footer', 'pcf_pdf');

        // Carousel Behavior Section
        add_settings_section('pcf_behavior', 'Carousel Behavior', '__return_false', 'pdf-carousel-footer');
        add_settings_field('autoplay', 'Enable Auto-rotation', array($this, 'field_autoplay'), 'pdf-carousel-footer', 'pcf_behavior');
        add_settings_field('autoplay_speed', 'Rotation Speed (ms)', array($this, 'field_autoplay_speed'), 'pdf-carousel-footer', 'pcf_behavior');
        add_settings_field('pause_on_hover', 'Pause on Hover', array($this, 'field_pause_on_hover'), 'pdf-carousel-footer', 'pcf_behavior');
        add_settings_field('infinite_loop', 'Infinite Loop', array($this, 'field_infinite_loop'), 'pdf-carousel-footer', 'pcf_behavior');

        // Navigation Section
        add_settings_section('pcf_navigation', 'Navigation Settings', '__return_false', 'pdf-carousel-footer');
        add_settings_field('show_arrows', 'Show Navigation Arrows', array($this, 'field_show_arrows'), 'pdf-carousel-footer', 'pcf_navigation');
        add_settings_field('show_dots', 'Show Dots Indicator', array($this, 'field_show_dots'), 'pdf-carousel-footer', 'pcf_navigation');
        add_settings_field('slides_to_show', 'Slides to Show', array($this, 'field_slides_to_show'), 'pdf-carousel-footer', 'pcf_navigation');
        add_settings_field('slides_to_scroll', 'Slides to Scroll', array($this, 'field_slides_to_scroll'), 'pdf-carousel-footer', 'pcf_navigation');
        add_settings_field('fade_effect', 'Fade Effect', array($this, 'field_fade_effect'), 'pdf-carousel-footer', 'pcf_navigation');
    }

    public function sanitize_options($input)
    {
        $defaults = $this->default_options();
        $output = array();
        
        // PDF Settings
        $output['pdf_urls'] = isset($input['pdf_urls']) ? sanitize_text_field($input['pdf_urls']) : $defaults['pdf_urls'];
        $output['width'] = isset($input['width']) ? max(100, intval($input['width'])) : $defaults['width'];
        $output['height'] = isset($input['height']) ? max(100, intval($input['height'])) : $defaults['height'];
        
        // Carousel Behavior
        $output['autoplay'] = isset($input['autoplay']) ? 1 : 0;
        $output['autoplay_speed'] = isset($input['autoplay_speed']) ? max(1000, intval($input['autoplay_speed'])) : $defaults['autoplay_speed'];
        $output['pause_on_hover'] = isset($input['pause_on_hover']) ? 1 : 0;
        $output['infinite_loop'] = isset($input['infinite_loop']) ? 1 : 0;
        
        // Navigation Settings
        $output['show_arrows'] = isset($input['show_arrows']) ? 1 : 0;
        $output['show_dots'] = isset($input['show_dots']) ? 1 : 0;
        $output['slides_to_show'] = isset($input['slides_to_show']) ? max(1, intval($input['slides_to_show'])) : $defaults['slides_to_show'];
        $output['slides_to_scroll'] = isset($input['slides_to_scroll']) ? max(1, intval($input['slides_to_scroll'])) : $defaults['slides_to_scroll'];
        $output['fade_effect'] = isset($input['fade_effect']) ? 1 : 0;
        
        return $output;
    }

    public function field_pdf_urls()
    {
        $options = $this->get_options();
        echo '<input type="text" name="' . esc_attr(self::OPTION_KEY) . '[pdf_urls]" value="' . esc_attr($options['pdf_urls']) . '" style="width: 100%; max-width: 600px;" placeholder="https://example.com/a.pdf, https://example.com/b.pdf" />';
    }

    public function field_width()
    {
        $options = $this->get_options();
        echo '<input type="number" min="100" name="' . esc_attr(self::OPTION_KEY) . '[width]" value="' . esc_attr($options['width']) . '" />';
    }

    public function field_height()
    {
        $options = $this->get_options();
        echo '<input type="number" min="100" name="' . esc_attr(self::OPTION_KEY) . '[height]" value="' . esc_attr($options['height']) . '" />';
    }

    public function field_autoplay()
    {
        $options = $this->get_options();
        $checked = $options['autoplay'] ? 'checked' : '';
        echo '<label><input type="checkbox" name="' . esc_attr(self::OPTION_KEY) . '[autoplay]" value="1" ' . $checked . ' /> Enabled</label>';
    }

    public function field_autoplay_speed()
    {
        $options = $this->get_options();
        echo '<input type="number" min="1000" step="500" name="' . esc_attr(self::OPTION_KEY) . '[autoplay_speed]" value="' . esc_attr($options['autoplay_speed']) . '" />';
        echo '<p class="description">Minimum: 1000ms (1 second)</p>';
    }

    public function field_pause_on_hover()
    {
        $options = $this->get_options();
        $checked = $options['pause_on_hover'] ? 'checked' : '';
        echo '<label><input type="checkbox" name="' . esc_attr(self::OPTION_KEY) . '[pause_on_hover]" value="1" ' . $checked . ' /> Pause auto-rotation when hovering over the carousel</label>';
    }

    public function field_infinite_loop()
    {
        $options = $this->get_options();
        $checked = $options['infinite_loop'] ? 'checked' : '';
        echo '<label><input type="checkbox" name="' . esc_attr(self::OPTION_KEY) . '[infinite_loop]" value="1" ' . $checked . ' /> Enable infinite loop (restart from beginning after last slide)</label>';
    }

    public function field_show_arrows()
    {
        $options = $this->get_options();
        $checked = $options['show_arrows'] ? 'checked' : '';
        echo '<label><input type="checkbox" name="' . esc_attr(self::OPTION_KEY) . '[show_arrows]" value="1" ' . $checked . ' /> Show left/right navigation arrows</label>';
    }

    public function field_show_dots()
    {
        $options = $this->get_options();
        $checked = $options['show_dots'] ? 'checked' : '';
        echo '<label><input type="checkbox" name="' . esc_attr(self::OPTION_KEY) . '[show_dots]" value="1" ' . $checked . ' /> Show dots indicator at the bottom</label>';
    }

    public function field_slides_to_show()
    {
        $options = $this->get_options();
        echo '<input type="number" min="1" max="5" name="' . esc_attr(self::OPTION_KEY) . '[slides_to_show]" value="' . esc_attr($options['slides_to_show']) . '" />';
        echo '<p class="description">Number of slides visible at once (1-5)</p>';
    }

    public function field_slides_to_scroll()
    {
        $options = $this->get_options();
        echo '<input type="number" min="1" max="5" name="' . esc_attr(self::OPTION_KEY) . '[slides_to_scroll]" value="' . esc_attr($options['slides_to_scroll']) . '" />';
        echo '<p class="description">Number of slides to scroll at once (1-5)</p>';
    }

    public function field_fade_effect()
    {
        $options = $this->get_options();
        $checked = $options['fade_effect'] ? 'checked' : '';
        echo '<label><input type="checkbox" name="' . esc_attr(self::OPTION_KEY) . '[fade_effect]" value="1" ' . $checked . ' /> Use fade transition instead of slide</label>';
        echo '<p class="description">Note: Fade effect works best with 1 slide at a time</p>';
    }

    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>PDF Carousel Footer Settings</h1>
            <div class="pcf-admin-container">
                <div class="pcf-admin-main">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('pcf_settings_group');
                        do_settings_sections('pdf-carousel-footer');
                        submit_button('Save Settings', 'primary', 'submit', true, array('id' => 'pcf-save-settings'));
                        ?>
                    </form>
                </div>
                <div class="pcf-admin-sidebar">
                    <div class="pcf-info-box">
                        <h3>How to Use</h3>
                        <p><strong>Shortcode:</strong> <code>[pdf_carousel_footer]</code></p>
                        <p><strong>Widget:</strong> Add "PDF Carousel Footer" widget to any widget area</p>
                        <p><strong>Settings:</strong> Configure PDF URLs and carousel behavior above</p>
                    </div>
                    <div class="pcf-info-box">
                        <h3>Quick Tips</h3>
                        <ul>
                            <li>Enter PDF URLs separated by commas</li>
                            <li>Use HTTPS URLs for better security</li>
                            <li>Test different rotation speeds to find what works best</li>
                            <li>Enable pause on hover for better user experience</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <style>
        .pcf-admin-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .pcf-admin-main {
            flex: 2;
        }
        .pcf-admin-sidebar {
            flex: 1;
            max-width: 300px;
        }
        .pcf-info-box {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .pcf-info-box h3 {
            margin-top: 0;
            color: #23282d;
        }
        .pcf-info-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .pcf-info-box li {
            margin-bottom: 5px;
        }
        .form-table th {
            width: 200px;
        }
        .form-table td {
            padding: 15px 10px;
        }
        .description {
            font-style: italic;
            color: #666;
        }
        @media (max-width: 768px) {
            .pcf-admin-container {
                flex-direction: column;
            }
            .pcf-admin-sidebar {
                max-width: none;
            }
        }
        </style>
        <?php
    }

    public function register_widget()
    {
        register_widget('PDF_Carousel_Footer_Widget');
    }

    public function shortcode_handler($atts)
    {
        $atts = shortcode_atts(array(), $atts, 'pdf_carousel_footer');
        $this->should_enqueue_assets = true;
        return $this->render_carousel_html();
    }

    public function maybe_register_assets()
    {
        if ($this->did_register_assets) {
            return;
        }
        $this->did_register_assets = true;

        // Register Slick Carousel from CDN
        wp_register_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1');
        wp_register_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array('slick-css'), '1.8.1');
        wp_register_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);

        // Register PDF.js from CDN
        wp_register_script('pdfjs', 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js', array(), '3.11.174', true);

        // Register plugin assets
        wp_register_style('pcf-css', plugin_dir_url(dirname(__FILE__)) . 'assets/css/pdf-carousel.css', array('slick-css', 'slick-theme-css'), '1.0.0');
        wp_register_script('pcf-js', plugin_dir_url(dirname(__FILE__)) . 'assets/js/pdf-carousel.js', array('jquery', 'slick-js', 'pdfjs'), '1.0.0', true);
    }

    public function enqueue_if_needed()
    {
        if (!$this->should_enqueue_assets) {
            return;
        }
        
        $options = $this->get_options();
        
        // Enqueue styles
        wp_enqueue_style('slick-css');
        wp_enqueue_style('slick-theme-css');
        wp_enqueue_style('pcf-css');
        
        // Enqueue scripts
        wp_enqueue_script('slick-js');
        wp_enqueue_script('pdfjs');
        wp_enqueue_script('pcf-js');

        // Localize script with configuration
        $slider_settings = array(
            'autoplay' => (bool) $options['autoplay'],
            'autoplaySpeed' => intval($options['autoplay_speed']),
            'pauseOnHover' => (bool) $options['pause_on_hover'],
            'arrows' => (bool) $options['show_arrows'],
            'dots' => (bool) $options['show_dots'],
            'infinite' => (bool) $options['infinite_loop'],
            'slidesToShow' => 1, // Always show only 1 slide to prevent empty frames
            'slidesToScroll' => 1, // Always scroll 1 slide at a time
            'fade' => (bool) $options['fade_effect'],
            'centerMode' => false, // Disable center mode to prevent empty spaces
            'variableWidth' => false, // Disable variable width to prevent gaps
            'adaptiveHeight' => true, // Adjust height to content
            'swipeToSlide' => true, // Allow swiping to slide
            'responsive' => array(
                array(
                    'breakpoint' => 768,
                    'settings' => array(
                        'slidesToShow' => 1,
                        'slidesToScroll' => 1,
                        'arrows' => (bool) $options['show_arrows'], // Use admin setting for arrows
                        'dots' => (bool) $options['show_dots'], // Use admin setting for dots
                        'centerMode' => false,
                        'variableWidth' => false
                    )
                )
            )
        );
        
        $worker_src = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
        
        wp_localize_script('pcf-js', 'pcfConfig', array(
            'sliderSettings' => $slider_settings,
            'workerSrc' => $worker_src
        ));
    }

    private function parse_urls($csv)
    {
        $urls = array();
        foreach (explode(',', $csv) as $maybe_url) {
            $u = trim($maybe_url);
            if (empty($u)) {
                continue;
            }
            // Allow only http/https URLs
            if (filter_var($u, FILTER_VALIDATE_URL) && preg_match('/^https?:\/\//i', $u)) {
                $urls[] = esc_url($u);
            }
        }
        return $urls;
    }

    public function render_carousel_html()
    {
        $options = $this->get_options();
        $urls = $this->parse_urls($options['pdf_urls']);
        $width = max(100, intval($options['width']));
        $height = max(100, intval($options['height']));

        if (empty($urls)) {
            return '<div class="pcf-notice">No PDF URLs configured.</div>';
        }

        // Build slides with canvas thumbnails rendered via PDF.js
        $html = '<div class="pcf-pdf-carousel">';
        foreach ($urls as $url) {
            $sep = strpos($url, '#') === false ? '#' : '&';
            $src = $url . $sep . 'toolbar=0&navpanes=0&scrollbar=0';
            $canvas = '<canvas class="pcf-thumb" data-w="' . esc_attr($width) . '" data-h="' . esc_attr($height) . '" width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"></canvas>';
            $linked_canvas = '<a class="pcf-thumb-link" href="' . esc_url($url) . '" target="_blank" rel="noopener">' . $canvas . '</a>';
            $fallback = '<div class="pcf-thumb-fallback" style="display:none;text-align:center;"><a href="' . esc_url($src) . '" target="_blank" rel="noopener">Open PDF</a></div>';
            $item = '<div class="pcf-slide" data-pdf-url="' . esc_url($url) . '">' . $linked_canvas . $fallback . '</div>';
            $html .= $item;
        }
        $html .= '</div>';

        return $html;
    }
}

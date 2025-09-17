# PDF Carousel Footer WordPress Plugin

A powerful WordPress plugin that displays PDF files in an elegant carousel format using Slick Carousel and PDF.js. Perfect for showcasing PDF documents in your website's footer or anywhere using shortcodes or widgets.

## 🚀 Features

### 📄 PDF Display
- **PDF Thumbnails**: Automatically generates thumbnails from PDF first pages using PDF.js
- **Multiple PDFs**: Support for multiple PDF files in a single carousel
- **Responsive Design**: Fully responsive and mobile-friendly
- **Fallback Support**: Graceful fallback to direct PDF links if thumbnail generation fails

### 🎛️ Advanced Admin Panel
- **Organized Settings**: Clean, categorized admin interface with three main sections:
  - **PDF Settings**: Configure PDF URLs, dimensions
  - **Carousel Behavior**: Auto-rotation, speed, pause controls
  - **Navigation Settings**: Arrows, dots, slides configuration

### 🎨 Carousel Customization
- **Auto-rotation**: Enable/disable automatic slide rotation
- **Rotation Speed**: Configurable speed (minimum 1000ms)
- **Pause on Hover**: Pause auto-rotation when hovering
- **Infinite Loop**: Continuous looping through slides
- **Navigation Controls**: Toggle arrows and dots indicators
- **Fade Effect**: Optional fade transition instead of slide
- **Responsive Behavior**: Mobile-optimized settings

### 📱 Responsive Design
- **Mobile-First**: Optimized for all screen sizes
- **Touch Support**: Swipe gestures on mobile devices
- **Adaptive Layout**: Automatically adjusts to container width

## 📦 Installation

1. **Upload Plugin**:
   - Upload the plugin folder to `/wp-content/plugins/`
   - Or install via WordPress admin → Plugins → Add New → Upload

2. **Activate Plugin**:
   - Go to WordPress admin → Plugins
   - Find "PDF Carousel Footer" and click "Activate"

3. **Configure Settings**:
   - Go to Settings → PDF Carousel Footer
   - Add your PDF URLs and configure carousel behavior

## ⚙️ Configuration

### Admin Panel Settings

#### PDF Settings
- **PDF URLs**: Comma-separated list of PDF file URLs
- **PDF Width**: Thumbnail width in pixels (minimum 100px)
- **PDF Height**: Thumbnail height in pixels (minimum 100px)

#### Carousel Behavior
- **Enable Auto-rotation**: Toggle automatic slide rotation
- **Rotation Speed**: Speed in milliseconds (minimum 1000ms)
- **Pause on Hover**: Pause rotation when hovering over carousel
- **Infinite Loop**: Enable continuous looping

#### Navigation Settings
- **Show Navigation Arrows**: Toggle left/right arrow buttons
- **Show Dots Indicator**: Toggle bottom dots navigation
- **Slides to Show**: Number of slides visible (1-5)
- **Slides to Scroll**: Number of slides to scroll (1-5)
- **Fade Effect**: Use fade transition instead of slide

### Default Settings
```php
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
```

## 🎯 Usage

### Shortcode
Place this shortcode anywhere in your content:
```
[pdf_carousel_footer]
```

### Widget
1. Go to Appearance → Widgets
2. Find "PDF Carousel Footer" widget
3. Drag it to your desired widget area
4. Configure settings in Settings → PDF Carousel Footer

### PHP (for developers)
```php
// Get plugin instance
$plugin = PDF_Carousel_Footer_Plugin::instance();

// Render carousel HTML
echo $plugin->render_carousel_html();

// Get current options
$options = $plugin->get_options();
```

## 🎨 Styling

### CSS Classes
- `.pcf-pdf-carousel` - Main carousel container
- `.pcf-slide` - Individual slide container
- `.pcf-thumb-link` - PDF thumbnail link
- `.pcf-thumb-fallback` - Fallback content when PDF fails to load
- `.pcf-notice` - Notice messages

### Custom CSS Example
```css
/* Customize carousel appearance */
.pcf-pdf-carousel {
    max-width: 800px;
    margin: 0 auto;
}

.pcf-pdf-carousel canvas {
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Custom arrow styling */
.pcf-pdf-carousel .slick-prev,
.pcf-pdf-carousel .slick-next {
    background: #0073aa;
    border-radius: 50%;
}
```

## 🔧 Technical Details

### Dependencies
- **Slick Carousel**: For carousel functionality
- **PDF.js**: For PDF thumbnail generation
- **jQuery**: Required for Slick Carousel

### File Structure
```
pdf-carousel-footer/
├── PDF Carousel Footer.php          # Main plugin file
├── includes/
│   ├── class-pdf-carousel-plugin.php    # Main plugin class
│   └── class-pdf-carousel-widget.php    # Widget class
├── assets/
│   ├── css/
│   │   └── pdf-carousel.css             # Plugin styles
│   └── js/
│       └── pdf-carousel.js              # Plugin JavaScript
└── README.md                           # This file
```

### Hooks and Filters
- `pcf_options` - Filter plugin options
- `pcf_carousel_html` - Filter carousel HTML output
- `pcf_slider_settings` - Filter Slick carousel settings

## 🐛 Troubleshooting

### Common Issues

**PDFs not loading**:
- Ensure PDF URLs are accessible and use HTTPS
- Check browser console for CORS errors
- Verify PDF files are not password-protected

**Thumbnails not generating**:
- Check if PDF.js is loading properly
- Verify PDF URLs are valid and accessible
- Check browser console for JavaScript errors

**Carousel not responsive**:
- Ensure theme doesn't override plugin CSS
- Check for CSS conflicts
- Verify Slick Carousel is loading

**Admin settings not saving**:
- Check user permissions (requires 'manage_options')
- Verify WordPress nonce is working
- Check for plugin conflicts

### Debug Mode
Enable WordPress debug mode to see detailed error messages:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📝 Changelog

### Version 1.0.0
- Initial release
- Basic PDF carousel functionality
- Admin panel with comprehensive settings
- Responsive design
- Widget and shortcode support
- PDF.js integration for thumbnails
- Slick Carousel integration
- Mobile-optimized interface

## 🤝 Support

For support, feature requests, or bug reports:
1. Check the troubleshooting section above
2. Review WordPress error logs
3. Test with default WordPress theme
4. Disable other plugins to check for conflicts

## 📄 License

This plugin is licensed under GPLv2 or later.

## 👨‍💻 Author

**Nikolay Djemerenov**

---

## 🎯 Quick Start Guide

1. **Install and activate** the plugin
2. **Go to Settings → PDF Carousel Footer**
3. **Add your PDF URLs** (comma-separated)
4. **Configure dimensions** (width/height)
5. **Set up carousel behavior** (autoplay, speed, etc.)
6. **Choose navigation options** (arrows, dots)
7. **Save settings**
8. **Add shortcode** `[pdf_carousel_footer]` or use the widget

Your PDF carousel is now ready to display your documents beautifully! 🎉

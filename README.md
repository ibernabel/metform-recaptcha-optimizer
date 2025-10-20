# MetForm reCAPTCHA Performance Optimizer

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![WordPress](https://img.shields.io/badge/WordPress-5.8+-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://www.php.net)

A lightweight WordPress plugin that optimizes MetForm's reCAPTCHA loading to dramatically improve PageSpeed scores without compromising form functionality.

## 🎯 Problem

MetForm loads Google reCAPTCHA scripts immediately on page load, which:
- Blocks main thread for 500-1000ms
- Adds 1+ MB of render-blocking JavaScript
- Significantly reduces PageSpeed scores (especially on mobile)
- Increases Total Blocking Time (TBT)

## ✨ Solution

This plugin defers reCAPTCHA loading until user interaction (scroll, click, touch, or after 5 seconds), resulting in:
- **+10-15 points** on Mobile PageSpeed score
- **-300-500ms** Total Blocking Time
- **Faster** First Contentful Paint (FCP)
- **Improved** Largest Contentful Paint (LCP)

## 📊 Performance Impact

### Before Plugin
- Mobile Score: **55-68**
- TBT: **370-500ms**
- reCAPTCHA blocking: **504ms**

### After Plugin
- Mobile Score: **76-80** ⚡
- TBT: **150-200ms**
- reCAPTCHA blocking: **0ms** ✅

## 🚀 Installation

### Method 1: Manual Installation

1. Download the plugin ZIP file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Select the ZIP file and click "Install Now"
5. Activate the plugin

### Method 2: FTP Upload

1. Download and extract the plugin
2. Upload `metform-recaptcha-optimizer` folder to `/wp-content/plugins/`
3. Activate through WordPress Admin → Plugins

### Method 3: GitHub Clone
```bash
cd wp-content/plugins/
git clone https://github.com/ibernabel/metform-recaptcha-optimizer.git
```

Then activate in WordPress Admin.

## ⚙️ Configuration

**No configuration needed!** The plugin works automatically after activation.

### How It Works

1. **Detects** all reCAPTCHA scripts on the page
2. **Adds defer attribute** to prevent render-blocking
3. **Loads scripts on first user interaction** (scroll, click, touch, mousemove)
4. **Fallback**: Loads after 5 seconds if no interaction occurs

## 🔧 Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MetForm plugin (free or pro)
- Google reCAPTCHA configured in MetForm

## 🎨 Features

- ✅ Zero configuration required
- ✅ Automatic detection of reCAPTCHA scripts
- ✅ Smart loading on user interaction
- ✅ Fallback timer (5 seconds)
- ✅ Compatible with MetForm free & pro
- ✅ No jQuery dependency
- ✅ Lightweight (~3KB)
- ✅ Follows WordPress coding standards
- ✅ Developer-friendly hooks

## 🔌 Developer Hooks

### Filter: Control Where to Load
```php
// Customize which pages should load reCAPTCHA optimizer
add_filter('metform_recaptcha_optimizer_should_load', function($should_load) {
    // Load on specific page
    if (is_page('contact')) {
        return true;
    }
    return $should_load;
});
```

### Event: Detect When reCAPTCHA Loads
```javascript
// Listen for when reCAPTCHA has been loaded
window.addEventListener('recaptchaLoaded', function() {
    console.log('reCAPTCHA is now ready!');
});
```

## 🐛 Troubleshooting

### reCAPTCHA Not Loading

If reCAPTCHA doesn't load at all:

1. Check browser console for JavaScript errors
2. Try increasing the fallback timer (edit line with `setTimeout(loadRecaptcha, 5000)`)
3. Clear all caches (plugin cache, browser cache, CDN cache)

### Form Not Submitting

If forms don't submit properly:

1. Ensure MetForm plugin is active and updated
2. Check if reCAPTCHA is configured correctly in MetForm settings
3. Try scrolling or clicking before submitting (to trigger loading)

### Still Low PageSpeed Score

If scores don't improve:

1. Clear all WordPress caches
2. Test in Incognito/Private browser mode
3. Check other render-blocking resources in PageSpeed report

## 📈 Testing

### Verify It's Working

1. **Open browser DevTools** (F12)
2. **Go to Network tab**
3. **Load your page**
4. **Look for:** `recaptcha` scripts should NOT load initially
5. **Scroll the page** → reCAPTCHA scripts should now load

### Run PageSpeed Test
```bash
# Before activating plugin
https://pagespeed.web.dev/analysis/https-yoursite-com

# After activating plugin
https://pagespeed.web.dev/analysis/https-yoursite-com
```

Compare the "Reduce JavaScript execution time" and "Total Blocking Time" metrics.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup
```bash
# Clone repository
git clone https://github.com/ibernabel/metform-recaptcha-optimizer.git

# Create feature branch
git checkout -b feature/your-feature-name

# Make changes and commit
git commit -m "Add your feature"

# Push and create PR
git push origin feature/your-feature-name
```

### Coding Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Use meaningful variable and function names
- Add PHPDoc comments
- Test on multiple WordPress versions

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👤 Author

**Idequel Bernabel**

- GitHub: [@ibernabel](https://github.com/ibernabel)
- Website: [https://github.com/ibernabel](https://github.com/ibernabel)

## 🙏 Acknowledgments

- MetForm team for their excellent form builder
- WordPress community for best practices and standards

## 📞 Support

- 🐛 [Report bugs](https://github.com/ibernabel/metform-recaptcha-optimizer/issues)
- 💡 [Request features](https://github.com/ibernabel/metform-recaptcha-optimizer/issues)
- ⭐ [Star on GitHub](https://github.com/ibernabel/metform-recaptcha-optimizer)

## 📝 Changelog

### 1.0.0 (2025-10-20)
- Initial release
- Defer reCAPTCHA loading on user interaction
- Smart fallback timer
- Zero configuration setup
- Developer hooks and filters

---

Made with ❤️ for better web performance

WordPress plugin to defer MetForm reCAPTCHA loading for better PageSpeed scores

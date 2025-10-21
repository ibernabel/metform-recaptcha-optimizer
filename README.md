# MetForm reCAPTCHA Performance Optimizer

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![WordPress](https://img.shields.io/badge/WordPress-5.8+-blue.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://www.php.net)
[![Version](https://img.shields.io/badge/version-1.0.0-green.svg)](https://github.com/ibernabel/metform-recaptcha-optimizer/releases)

A lightweight WordPress plugin that optimizes MetForm's reCAPTCHA loading to dramatically improve PageSpeed scores without compromising form functionality.

## 🎯 Problem

MetForm loads Google reCAPTCHA scripts immediately on page load, which:
- **Blocks main thread** for 500-1,369ms
- **Adds 1+ MB** of render-blocking JavaScript (1,036 KB from reCAPTCHA alone)
- **Significantly reduces** PageSpeed scores (especially on mobile)
- **Increases Total Blocking Time** (TBT) by up to 504ms
- **Delays critical metrics** like FCP and LCP

### Real-World Impact (Before Plugin)
On a production WordPress site (leyesya.com):
- reCAPTCHA JavaScript Execution: **1,369ms** (66% of total JS execution)
- Third-party mainthread blocking: **630ms**
- Total Blocking Time (TBT): **490ms**
- Mobile PageSpeed Score: **48-55** 🔴

## ✨ Solution

This plugin uses **MutationObserver** and **event-driven loading** to defer reCAPTCHA until user interaction (scroll, click, touch, mousemove, keydown) or after 10 seconds as fallback.

### Technical Approach
1. **Adds `defer` attribute** to static reCAPTCHA scripts
2. **MutationObserver** intercepts dynamically injected scripts
3. **Blocks execution** until user interaction is detected
4. **Loads on first interaction** with passive event listeners
5. **Fallback timer** ensures loading after 10 seconds (no interaction)

## 📊 Performance Impact (Real Metrics)

### Before Plugin
```
Mobile Score:           48-55 🔴
Desktop Score:          70-74
FCP (First Contentful): 5.1s
LCP (Largest Content):  6.5s
TBT (Total Blocking):   490ms
CLS (Layout Shift):     0.0002
reCAPTCHA Execution:    1,369ms
reCAPTCHA Blocking:     504ms (74% of TBT)
Third-party Thread:     630ms
```

### After Plugin
```
Mobile Score:           68 ⚡ (+20 points, +42% improvement)
Desktop Score:          66-70
FCP (First Contentful): 3.2s (-1.9s, -37% improvement) ✅
LCP (Largest Content):  4.4s (-2.1s, -32% improvement) ✅
TBT (Total Blocking):   230ms (-260ms, -53% improvement) ✅
CLS (Layout Shift):     0.0002 (unchanged)
reCAPTCHA Execution:    1,019ms (-350ms, -25%)
reCAPTCHA Blocking:     0ms (no longer render-blocking) ✅
Third-party Thread:     475ms (-155ms, -25%)
Max Potential FID:      161ms (-33ms)
```

### Summary of Improvements
| Metric | Before | After | Change | Improvement |
|--------|--------|-------|--------|-------------|
| **Mobile Score** | 48-55 | 68 | +13-20 | +27-42% |
| **FCP** | 5.1s | 3.2s | -1.9s | -37% |
| **LCP** | 6.5s | 4.4s | -2.1s | -32% |
| **TBT** | 490ms | 230ms | -260ms | **-53%** |
| **reCAPTCHA Blocking** | 504ms | 0ms | -504ms | **-100%** |

## 🚀 Installation

### Method 1: Manual Installation

1. Download the latest [release ZIP file](https://github.com/ibernabel/metform-recaptcha-optimizer/releases)
2. Go to **WordPress Admin → Plugins → Add New**
3. Click **"Upload Plugin"**
4. Select the ZIP file and click **"Install Now"**
5. Click **"Activate"**

### Method 2: FTP Upload

1. Download and extract the plugin
2. Upload `metform-recaptcha-optimizer` folder to `/wp-content/plugins/`
3. Activate through **WordPress Admin → Plugins**

### Method 3: GitHub Clone
```bash
cd wp-content/plugins/
git clone https://github.com/ibernabel/metform-recaptcha-optimizer.git
cd metform-recaptcha-optimizer
```

Then activate in WordPress Admin → Plugins.

## ⚙️ Configuration

**No configuration needed!** The plugin works automatically after activation.

### How It Works (Technical Deep Dive)

```
Page Load
    ↓
1. Plugin adds 'defer' attribute to static reCAPTCHA scripts
    ↓
2. MutationObserver starts monitoring DOM for dynamic scripts
    ↓
3. Any reCAPTCHA script injected dynamically is blocked (type="text/plain")
    ↓
4. Event listeners wait for user interaction:
   - scroll, click, touchstart, mousemove, keydown
    ↓
5a. USER INTERACTS → Load reCAPTCHA immediately
5b. NO INTERACTION → Load after 10 seconds (fallback)
    ↓
6. MutationObserver disconnects
    ↓
7. Blocked scripts are converted to active scripts
    ↓
8. grecaptcha.ready() initializes
    ↓
9. Custom event 'recaptchaLoaded' is dispatched
    ↓
✅ Form is ready to submit
```

### Smart Loading Logic
- **First priority**: User interaction (best UX)
- **Second priority**: 10-second timer (ensures loading)
- **Safety**: Scripts load only once (prevents duplicates)
- **Compatibility**: Works with MetForm free & pro

## 🔧 Requirements

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **MetForm**: Any version (free or pro)
- **reCAPTCHA**: v2 or v3 configured in MetForm settings

### Tested With
- WordPress 6.0 - 6.4
- PHP 7.4 - 8.2
- MetForm Free & Pro
- Elementor Page Builder
- WP Super Cache, Autoptimize, Smush

## 🎨 Features

- ✅ **Zero configuration** required
- ✅ **Automatic detection** of reCAPTCHA scripts (static & dynamic)
- ✅ **Smart loading** on user interaction
- ✅ **Fallback timer** (10 seconds)
- ✅ **MutationObserver** for dynamic script blocking
- ✅ **Compatible** with MetForm free & pro
- ✅ **No jQuery dependency** (vanilla JavaScript)
- ✅ **Lightweight** (~4KB minified)
- ✅ **Production-ready** (no console logs)
- ✅ **WordPress coding standards** compliant
- ✅ **Developer-friendly** hooks

## 🔌 Developer Hooks

### Filter: Control Where to Load

```php
/**
 * Customize which pages should load reCAPTCHA optimizer
 * 
 * @param bool $should_load Whether to load on current page
 * @return bool
 */
add_filter('metform_recaptcha_optimizer_should_load', function($should_load) {
    // Example: Load only on specific pages
    if (is_page(array('contact', 'register', 'booking'))) {
        return true;
    }
    
    // Example: Load on custom post type
    if (is_singular('custom_post_type')) {
        return true;
    }
    
    return $should_load;
});
```

### Event: Detect When reCAPTCHA Loads

```javascript
/**
 * Custom event dispatched when reCAPTCHA is loaded
 * Useful for third-party integrations
 */
window.addEventListener('recaptchaLoaded', function() {
    console.log('reCAPTCHA is now ready!');
    
    // Your custom logic here
    // Example: Initialize custom form validation
    if (typeof grecaptcha !== 'undefined') {
        // Safe to use grecaptcha object
    }
});
```

## 🐛 Troubleshooting

### reCAPTCHA Not Loading

**Symptoms**: Form shows "Please verify you are human" but reCAPTCHA doesn't appear

**Solutions**:
1. **Check browser console** for JavaScript errors
2. **Scroll the page** (triggers loading)
3. **Wait 10 seconds** (fallback timer)
4. **Clear all caches**:
   - WordPress cache (WP Super Cache, W3 Total Cache, etc.)
   - Browser cache (Ctrl+Shift+Delete)
   - CDN cache (Cloudflare, etc.)
5. **Check MetForm settings**: Ensure reCAPTCHA site key and secret key are configured

### Form Not Submitting

**Symptoms**: Submit button doesn't work or shows validation error

**Solutions**:
1. **Ensure MetForm is active** and updated to latest version
2. **Verify reCAPTCHA config** in MetForm → Settings → reCAPTCHA
3. **Test reCAPTCHA keys**: Use Google's [test keys](https://developers.google.com/recaptcha/docs/faq#id-like-to-run-automated-tests-with-recaptcha.-what-should-i-do)
4. **Check for conflicts**: Temporarily disable other optimization plugins
5. **Interact before submit**: Scroll or click before submitting (to ensure loading)

### Still Low PageSpeed Score

**Symptoms**: Score doesn't improve as expected

**Solutions**:
1. **Clear all caches** (plugin cache + browser cache)
2. **Test in Incognito** mode (prevents extension interference)
3. **Wait 2-3 minutes** after clearing cache (CDN propagation)
4. **Check other issues** in PageSpeed report:
   - Large images (use Smush or similar)
   - Render-blocking CSS (use Critical CSS)
   - Unused JavaScript (use Asset CleanUp)
   - Server response time (consider better hosting)
5. **Run test 3 times** (PageSpeed results can vary)

### Compatibility Issues

**Known Compatible Plugins**:
- ✅ WP Super Cache
- ✅ Autoptimize
- ✅ Smush
- ✅ Asset CleanUp
- ✅ Elementor
- ✅ GTM (Google Tag Manager)

**Not Compatible**:
- ❌ Flying Scripts (conflicts with MutationObserver approach)

## 📈 Testing & Validation

### Verify It's Working

1. **Open DevTools** (F12 in Chrome/Firefox)
2. **Go to Network tab**
3. **Filter by "recaptcha"**
4. **Load your page**
5. **Expected**: No reCAPTCHA requests initially
6. **Scroll the page**
7. **Expected**: reCAPTCHA scripts now load

### Visual Verification

```
Before scroll:
Network tab → No "recaptcha" requests ✅

After scroll:
Network tab → Multiple "recaptcha" requests appear ✅
Console → "reCAPTCHA is now ready!" (if listening to event)
```

### PageSpeed Testing

```bash
# Test your site (replace with your URL)
https://pagespeed.web.dev/analysis/https-yoursite-com

# Compare these metrics before/after:
# 1. Total Blocking Time (TBT)
# 2. First Contentful Paint (FCP)
# 3. Largest Contentful Paint (LCP)
# 4. "Reduce JavaScript execution time" section
```

**Expected Improvements**:
- Mobile Score: +10-20 points
- TBT: -200-500ms
- FCP: -1-2 seconds
- LCP: -1-2 seconds

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

```bash
# Clone repository
git clone https://github.com/ibernabel/metform-recaptcha-optimizer.git
cd metform-recaptcha-optimizer

# Create feature branch
git checkout -b feature/your-feature-name

# Make changes and test thoroughly

# Commit with meaningful message
git commit -m "feat: add your feature description"

# Push and create PR
git push origin feature/your-feature-name
```

### Coding Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Use **meaningful** variable and function names (English)
- Add **PHPDoc comments** for all functions
- Test on **multiple WordPress versions** (5.8+)
- Test on **multiple PHP versions** (7.4+)
- **No console.log()** in production code
- Use **vanilla JavaScript** (no jQuery)

### Commit Message Format

```
feat: add new feature
fix: bug fix
docs: documentation updates
style: formatting changes
refactor: code refactoring
test: adding tests
chore: maintenance tasks
```

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

You are free to:
- ✅ Use commercially
- ✅ Modify
- ✅ Distribute
- ✅ Private use

## 👤 Author

**Idequel Bernabel**

- GitHub: [@ibernabel](https://github.com/ibernabel)
- Website: [https://github.com/ibernabel](https://github.com/ibernabel)

## 🌟 Show Your Support

If this plugin helped improve your site's performance:

- ⭐ [Star on GitHub](https://github.com/ibernabel/metform-recaptcha-optimizer)
- 🐛 [Report bugs or suggest features](https://github.com/ibernabel/metform-recaptcha-optimizer/issues)
- 📣 Share with others facing similar performance issues
- ☕ Consider [buying me a coffee](https://github.com/ibernabel) (optional)

## 🙏 Acknowledgments

- **MetForm team** for their excellent form builder plugin
- **WordPress community** for best practices and coding standards
- **Google PageSpeed Insights** for performance testing tools
- **MutationObserver API** for making dynamic script blocking possible

## 📞 Support

- 🐛 **Bug Reports**: [GitHub Issues](https://github.com/ibernabel/metform-recaptcha-optimizer/issues)
- 💡 **Feature Requests**: [GitHub Issues](https://github.com/ibernabel/metform-recaptcha-optimizer/issues)
- 📖 **Documentation**: [GitHub Wiki](https://github.com/ibernabel/metform-recaptcha-optimizer/wiki)
- ⭐ **Star the Repo**: [GitHub](https://github.com/ibernabel/metform-recaptcha-optimizer)

## 📝 Changelog

### 1.0.0 (2025-10-21)

**Initial Release** 🎉

- ✅ Defer reCAPTCHA loading on user interaction
- ✅ MutationObserver for dynamic script blocking
- ✅ Smart fallback timer (10 seconds)
- ✅ Event listeners: scroll, click, touch, mousemove, keydown
- ✅ Zero configuration setup
- ✅ Developer hooks and filters
- ✅ Production-ready (no debug logs)
- ✅ WordPress 5.8+ and PHP 7.4+ compatible
- ✅ Tested with MetForm free & pro
- ✅ MIT License

**Measured Performance Improvements**:
- Mobile Score: +20 points (48 → 68)
- TBT: -53% (490ms → 230ms)
- FCP: -37% (5.1s → 3.2s)
- LCP: -32% (6.5s → 4.4s)
- reCAPTCHA blocking: -100% (504ms → 0ms)

---

**Made with ❤️ for better web performance**

*Optimizing WordPress sites, one script at a time.*
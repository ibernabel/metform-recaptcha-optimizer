<?php
/**
 * Plugin Name: MetForm reCAPTCHA Performance Optimizer
 * Plugin URI: https://github.com/ibernabel/metform-recaptcha-optimizer
 * Description: Defers MetForm reCAPTCHA loading to improve PageSpeed scores. Loads reCAPTCHA only on user interaction (scroll, click, touch) for better performance without compromising functionality.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Idequel Bernabel
 * Author URI: https://github.com/ibernabel
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: metform-recaptcha-optimizer
 * Domain Path: /languages
 *
 * @package MetForm_reCAPTCHA_Optimizer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Plugin Class
 */
class MetForm_reCAPTCHA_Optimizer {
    
    /**
     * Plugin version
     *
     * @var string
     */
    const VERSION = '1.0.0';
    
    /**
     * Singleton instance
     *
     * @var MetForm_reCAPTCHA_Optimizer
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     *
     * @return MetForm_reCAPTCHA_Optimizer
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     *
     * @return void
     */
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'defer_recaptcha_scripts'), 999);
        add_action('wp_footer', array($this, 'add_interaction_loader'), 999);
        
        // Add settings link in plugins page
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
    }
    
    /**
     * Defer reCAPTCHA scripts by adding defer attribute
     *
     * @return void
     */
    public function defer_recaptcha_scripts() {
        // Only run on frontend
        if (is_admin()) {
            return;
        }
        
        add_filter('script_loader_tag', array($this, 'add_defer_attribute'), 10, 3);
    }
    
    /**
     * Add defer attribute to reCAPTCHA scripts
     *
     * @param string $tag    The script tag.
     * @param string $handle The script handle.
     * @param string $src    The script source URL.
     * @return string Modified script tag
     */
    public function add_defer_attribute($tag, $handle, $src) {
        // Target all reCAPTCHA-related scripts
        if (strpos($src, 'google.com/recaptcha') !== false || 
            strpos($src, 'gstatic.com/recaptcha') !== false) {
            
            // Add defer attribute
            $tag = str_replace(' src', ' defer src', $tag);
            
            // Add data attribute for identification
            $tag = str_replace('<script', '<script data-recaptcha-defer="true"', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Add JavaScript to load reCAPTCHA on user interaction
     *
     * @return void
     */
    public function add_interaction_loader() {
        // Only run on frontend
        if (is_admin()) {
            return;
        }
        
        // Check if we're on a page that likely has forms
        if (!$this->should_load_on_page()) {
            return;
        }
        
        ?>
        <script id="metform-recaptcha-optimizer">
        (function() {
            'use strict';
            
            var recaptchaLoaded = false;
            
            /**
             * Load reCAPTCHA scripts
             */
            function loadRecaptcha() {
                if (recaptchaLoaded) {
                    return;
                }
                
                recaptchaLoaded = true;
                
                // Find all deferred reCAPTCHA scripts
                var scripts = document.querySelectorAll('script[data-recaptcha-defer="true"]');
                
                scripts.forEach(function(oldScript) {
                    if (!oldScript.src) {
                        return;
                    }
                    
                    // Create new script element
                    var newScript = document.createElement('script');
                    newScript.src = oldScript.src;
                    newScript.async = true;
                    
                    // Copy attributes
                    Array.from(oldScript.attributes).forEach(function(attr) {
                        if (attr.name !== 'src' && attr.name !== 'data-recaptcha-defer') {
                            newScript.setAttribute(attr.name, attr.value);
                        }
                    });
                    
                    // Append to head
                    document.head.appendChild(newScript);
                });
                
                // Dispatch custom event for other scripts that may need it
                if (typeof Event === 'function') {
                    window.dispatchEvent(new Event('recaptchaLoaded'));
                }
            }
            
            // Event types that trigger loading
            var events = ['scroll', 'click', 'touchstart', 'mousemove', 'keydown'];
            
            // Add event listeners
            events.forEach(function(eventType) {
                window.addEventListener(eventType, loadRecaptcha, {
                    once: true,
                    passive: true
                });
            });
            
            // Fallback: load after 5 seconds if no interaction
            setTimeout(loadRecaptcha, 5000);
            
        })();
        </script>
        <?php
    }
    
    /**
     * Determine if reCAPTCHA should be loaded on current page
     *
     * @return bool
     */
    private function should_load_on_page() {
        global $post;
        
        // Always load on front page
        if (is_front_page()) {
            return true;
        }
        
        // Check if post content has MetForm shortcode
        if (isset($post->post_content) && has_shortcode($post->post_content, 'metform')) {
            return true;
        }
        
        // Allow developers to filter
        return apply_filters('metform_recaptcha_optimizer_should_load', false);
    }
    
    /**
     * Add action links to plugin page
     *
     * @param array $links Existing plugin action links.
     * @return array Modified action links
     */
    public function add_action_links($links) {
        $custom_links = array(
            '<a href="https://github.com/ibernabel/metform-recaptcha-optimizer" target="_blank">' . 
            esc_html__('GitHub', 'metform-recaptcha-optimizer') . '</a>',
        );
        
        return array_merge($custom_links, $links);
    }
}

/**
 * Initialize the plugin
 *
 * @return MetForm_reCAPTCHA_Optimizer
 */
function metform_recaptcha_optimizer() {
    return MetForm_reCAPTCHA_Optimizer::get_instance();
}

// Start the plugin
metform_recaptcha_optimizer();
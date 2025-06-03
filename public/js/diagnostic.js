/**
 * UCUA System Diagnostic Script
 * Checks for common JavaScript issues and reports them
 */

(function() {
    'use strict';

    console.log('🔍 UCUA System Diagnostic Starting...');

    // Check jQuery
    if (typeof $ !== 'undefined') {
        console.log('✅ jQuery is loaded (version: ' + $.fn.jquery + ')');
    } else {
        console.error('❌ jQuery is not loaded');
    }

    // Check Bootstrap
    if (typeof $ !== 'undefined' && $.fn.modal) {
        console.log('✅ Bootstrap modals are available');
    } else {
        console.error('❌ Bootstrap modals are not available');
    }

    // Check UCUA utilities
    if (typeof window.UCUA !== 'undefined') {
        console.log('✅ UCUA utilities are loaded');
        console.log('   - Button utilities:', typeof window.UCUA.Button !== 'undefined' ? '✅' : '❌');
        console.log('   - Form utilities:', typeof window.UCUA.Form !== 'undefined' ? '✅' : '❌');
        console.log('   - Modal utilities:', typeof window.UCUA.Modal !== 'undefined' ? '✅' : '❌');
        console.log('   - Alert utilities:', typeof window.UCUA.Alert !== 'undefined' ? '✅' : '❌');
    } else {
        console.error('❌ UCUA utilities are not loaded');
    }

    // Check CSRF token
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (csrfToken) {
        console.log('✅ CSRF token is available');
    } else {
        console.error('❌ CSRF token is missing');
    }

    // Check for common DOM elements
    const commonElements = {
        'Forms': 'form',
        'Submit buttons': 'button[type="submit"]',
        'Modals': '.modal',
        'Alerts': '.alert'
    };

    Object.keys(commonElements).forEach(function(name) {
        const selector = commonElements[name];
        const count = $(selector).length;
        if (count > 0) {
            console.log('✅ ' + name + ' found: ' + count);
        } else {
            console.log('ℹ️ ' + name + ' not found on this page');
        }
    });

    // Test basic functionality
    console.log('🧪 Testing basic functionality...');

    // Test button click handling
    $(document).on('click', '[data-diagnostic-test]', function(e) {
        e.preventDefault();
        console.log('✅ Button click event is working');
        
        if (typeof window.UCUA !== 'undefined' && window.UCUA.Alert) {
            window.UCUA.Alert.show('success', 'Diagnostic test successful!');
        } else {
            alert('Diagnostic test successful! (fallback alert)');
        }
    });

    // Test form submission handling
    $(document).on('submit', '[data-diagnostic-form]', function(e) {
        e.preventDefault();
        console.log('✅ Form submission event is working');
        
        if (typeof window.UCUA !== 'undefined' && window.UCUA.Alert) {
            window.UCUA.Alert.show('info', 'Form submission test successful!');
        } else {
            alert('Form submission test successful! (fallback alert)');
        }
    });

    // Check for JavaScript errors
    window.addEventListener('error', function(e) {
        console.error('❌ JavaScript Error:', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            colno: e.colno,
            error: e.error
        });
    });

    // Check for unhandled promise rejections
    window.addEventListener('unhandledrejection', function(e) {
        console.error('❌ Unhandled Promise Rejection:', e.reason);
    });

    console.log('🔍 UCUA System Diagnostic Complete');

    // Add diagnostic buttons to page if not already present
    $(document).ready(function() {
        if (!$('[data-diagnostic-test]').length) {
            const diagnosticHtml = `
                <div id="diagnostic-panel" style="position: fixed; top: 10px; right: 10px; background: #fff; border: 2px solid #007bff; border-radius: 5px; padding: 10px; z-index: 9999; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h6 style="margin: 0 0 10px 0; color: #007bff;">🔧 Diagnostic Panel</h6>
                    <button type="button" class="btn btn-sm btn-primary mb-1" data-diagnostic-test style="display: block; width: 100%;">Test Button Click</button>
                    <form data-diagnostic-form style="margin: 0;">
                        <button type="submit" class="btn btn-sm btn-success" style="display: block; width: 100%;">Test Form Submit</button>
                    </form>
                    <button type="button" class="btn btn-sm btn-secondary mt-1" onclick="document.getElementById('diagnostic-panel').style.display='none'" style="display: block; width: 100%;">Hide Panel</button>
                </div>
            `;
            $('body').append(diagnosticHtml);
        }
    });

})();

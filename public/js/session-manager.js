/**
 * Session Management JavaScript
 * Handles session timeout warnings and automatic logout
 */
class SessionManager {
    constructor(options = {}) {
        this.sessionLifetime = options.sessionLifetime || 120; // minutes
        this.warningTime = options.warningTime || 10; // minutes before expiry
        this.checkInterval = options.checkInterval || 60000; // 1 minute
        this.lastActivity = Date.now();
        this.warningShown = false;
        this.logoutUrl = options.logoutUrl || '/logout';
        this.loginUrl = options.loginUrl || '/login';
        
        this.init();
    }

    init() {
        this.bindActivityEvents();
        this.startSessionCheck();
        this.createWarningModal();
    }

    bindActivityEvents() {
        // Track user activity
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.updateLastActivity();
            }, { passive: true });
        });
    }

    updateLastActivity() {
        this.lastActivity = Date.now();
        if (this.warningShown) {
            this.hideWarning();
        }
    }

    startSessionCheck() {
        setInterval(() => {
            this.checkSession();
        }, this.checkInterval);
    }

    checkSession() {
        const now = Date.now();
        const timeSinceActivity = (now - this.lastActivity) / 1000 / 60; // minutes
        const timeUntilExpiry = this.sessionLifetime - timeSinceActivity;

        if (timeUntilExpiry <= 0) {
            this.handleSessionExpiry();
        } else if (timeUntilExpiry <= this.warningTime && !this.warningShown) {
            this.showWarning(Math.ceil(timeUntilExpiry));
        }
    }

    createWarningModal() {
        const modal = document.createElement('div');
        modal.id = 'session-warning-modal';
        modal.className = 'session-modal';
        modal.innerHTML = `
            <div class="session-modal-content">
                <div class="session-modal-header">
                    <h4><i class="fas fa-exclamation-triangle text-warning"></i> Session Timeout Warning</h4>
                </div>
                <div class="session-modal-body">
                    <p>Your session will expire in <span id="session-countdown" class="font-weight-bold text-danger"></span> minutes due to inactivity.</p>
                    <p>Click "Stay Logged In" to extend your session, or you will be automatically logged out.</p>
                </div>
                <div class="session-modal-footer">
                    <button type="button" class="btn btn-primary" id="extend-session-btn">
                        <i class="fas fa-clock"></i> Stay Logged In
                    </button>
                    <button type="button" class="btn btn-secondary" id="logout-now-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout Now
                    </button>
                </div>
            </div>
        `;

        // Add CSS styles
        const style = document.createElement('style');
        style.textContent = `
            .session-modal {
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                backdrop-filter: blur(3px);
            }
            .session-modal-content {
                background-color: #fff;
                margin: 15% auto;
                padding: 0;
                border-radius: 8px;
                width: 90%;
                max-width: 500px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                animation: slideIn 0.3s ease-out;
            }
            .session-modal-header {
                padding: 20px;
                border-bottom: 1px solid #dee2e6;
                background-color: #f8f9fa;
                border-radius: 8px 8px 0 0;
            }
            .session-modal-header h4 {
                margin: 0;
                color: #495057;
            }
            .session-modal-body {
                padding: 20px;
            }
            .session-modal-footer {
                padding: 15px 20px;
                border-top: 1px solid #dee2e6;
                text-align: right;
                background-color: #f8f9fa;
                border-radius: 0 0 8px 8px;
            }
            .session-modal-footer .btn {
                margin-left: 10px;
            }
            @keyframes slideIn {
                from { transform: translateY(-50px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
        `;

        document.head.appendChild(style);
        document.body.appendChild(modal);

        // Bind events
        document.getElementById('extend-session-btn').addEventListener('click', () => {
            this.extendSession();
        });

        document.getElementById('logout-now-btn').addEventListener('click', () => {
            this.logout();
        });
    }

    showWarning(minutesLeft) {
        this.warningShown = true;
        const modal = document.getElementById('session-warning-modal');
        const countdown = document.getElementById('session-countdown');
        
        countdown.textContent = minutesLeft;
        modal.style.display = 'block';

        // Update countdown every minute
        this.countdownInterval = setInterval(() => {
            minutesLeft--;
            countdown.textContent = minutesLeft;
            
            if (minutesLeft <= 0) {
                clearInterval(this.countdownInterval);
                this.handleSessionExpiry();
            }
        }, 60000);
    }

    hideWarning() {
        this.warningShown = false;
        const modal = document.getElementById('session-warning-modal');
        modal.style.display = 'none';
        
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
    }

    extendSession() {
        // Make AJAX request to extend session
        fetch('/api/extend-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateLastActivity();
                this.hideWarning();
                this.showNotification('Session extended successfully!', 'success');
            } else {
                this.showNotification('Failed to extend session. Please login again.', 'error');
                setTimeout(() => this.logout(), 2000);
            }
        })
        .catch(error => {
            console.error('Session extension failed:', error);
            this.showNotification('Session extension failed. Please login again.', 'error');
            setTimeout(() => this.logout(), 2000);
        });
    }

    handleSessionExpiry() {
        this.hideWarning();
        this.showNotification('Your session has expired. You will be redirected to login.', 'warning');
        
        setTimeout(() => {
            this.logout();
        }, 3000);
    }

    logout() {
        // Create a form to submit logout request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.logoutUrl;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} session-notification`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
        `;
        
        // Add notification styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            animation: slideInRight 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-in';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if user is authenticated
    if (document.querySelector('meta[name="user-authenticated"]')) {
        const sessionLifetime = parseInt(document.querySelector('meta[name="session-lifetime"]')?.getAttribute('content')) || 120;
        
        window.sessionManager = new SessionManager({
            sessionLifetime: sessionLifetime,
            warningTime: 10, // Show warning 10 minutes before expiry
            checkInterval: 60000 // Check every minute
        });
    }
});

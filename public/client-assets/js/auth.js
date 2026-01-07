// API Configuration
const API_BASE_URL = window.location.origin + '/api';
const TOKEN_KEY = 'auth_token';
const USER_KEY = 'user_data';

// API Helper Functions
const api = {
    // Make authenticated request
    async request(endpoint, options = {}) {
        const token = this.getToken();
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers,
        };

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                ...options,
                headers,
            });

            const data = await response.json();

            if (!response.ok) {
                throw {
                    status: response.status,
                    data: data
                };
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    // Login
    async login(email, password) {
        const data = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password }),
        });

        if (data.success && data.data.token) {
            this.setToken(data.data.token);
            this.setUser(data.data.user);
        }

        return data;
    },

    // Register
    async register(name, email, password, password_confirmation) {
        const data = await this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify({ name, email, password, password_confirmation }),
        });

        if (data.success && data.data.token) {
            this.setToken(data.data.token);
            this.setUser(data.data.user);
        }

        return data;
    },

    // Logout
    async logout() {
        try {
            await this.request('/auth/logout', {
                method: 'POST',
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.clearAuth();
        }
    },

    // Get current user
    async getCurrentUser() {
        return await this.request('/auth/me');
    },

    // Token management
    setToken(token) {
        localStorage.setItem(TOKEN_KEY, token);
    },

    getToken() {
        return localStorage.getItem(TOKEN_KEY);
    },

    removeToken() {
        localStorage.removeItem(TOKEN_KEY);
    },

    // User data management
    setUser(user) {
        localStorage.setItem(USER_KEY, JSON.stringify(user));
    },

    getUser() {
        const userData = localStorage.getItem(USER_KEY);
        return userData ? JSON.parse(userData) : null;
    },

    removeUser() {
        localStorage.removeItem(USER_KEY);
    },

    // Clear all auth data
    clearAuth() {
        this.removeToken();
        this.removeUser();
    },

    // Check if user is authenticated
    isAuthenticated() {
        return !!this.getToken();
    }
};

// UI Helper Functions
const ui = {
    // Show loading state on button
    showButtonLoading(button) {
        button.disabled = true;
        button.classList.add('btn-loading');
        button.dataset.originalText = button.textContent;
        button.textContent = 'Loading...';
    },

    // Hide loading state on button
    hideButtonLoading(button) {
        button.disabled = false;
        button.classList.remove('btn-loading');
        if (button.dataset.originalText) {
            button.textContent = button.dataset.originalText;
        }
    },

    // Show alert message
    showAlert(message, type = 'success') {
        const alertDiv = document.getElementById('alert');
        if (alertDiv) {
            alertDiv.className = `alert alert-${type} show`;
            alertDiv.textContent = message;

            setTimeout(() => {
                alertDiv.classList.remove('show');
            }, 5000);
        }
    },

    // Show field error
    showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(`${fieldId}-error`);

        if (field) {
            field.classList.add('error');
        }

        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }
    },

    // Clear field error
    clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(`${fieldId}-error`);

        if (field) {
            field.classList.remove('error');
        }

        if (errorDiv) {
            errorDiv.classList.remove('show');
        }
    },

    // Clear all errors
    clearAllErrors() {
        document.querySelectorAll('.form-control').forEach(field => {
            field.classList.remove('error');
        });

        document.querySelectorAll('.error-message').forEach(error => {
            error.classList.remove('show');
        });

        const alertDiv = document.getElementById('alert');
        if (alertDiv) {
            alertDiv.classList.remove('show');
        }
    },

    // Handle validation errors from API
    handleValidationErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const message = Array.isArray(messages) ? messages[0] : messages;
            this.showFieldError(field, message);
        }
    }
};

// Auth Guard - Redirect if not authenticated
function requireAuth() {
    if (!api.isAuthenticated()) {
        window.location.href = '/client/login';
        return false;
    }
    return true;
}

// Guest Guard - Redirect if already authenticated
async function requireGuest() {
    const token = api.getToken();
    
    if (!token) {
        return true;
    }
    
    // Validate token with API
    try {
        const response = await api.getCurrentUser();
        
        // Token is valid, redirect to home
        window.location.href = '/client/home';
        return false;
    } catch (error) {
        // Token is invalid or API error, clear it and stay
        api.clearAuth();
        return true;
    }
}

// Handle Login Form
function handleLoginForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        ui.clearAllErrors();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const csrfToken = form.querySelector('input[name="_token"]').value;
        const submitBtn = form.querySelector('button[type="submit"]');

        ui.showButtonLoading(submitBtn);

        try {
            // Use web-based login endpoint
            const response = await fetch('/client/login-submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ email, password }),
            });

            if (response.ok) {
                ui.showAlert('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = '/client/home';
                }, 500);
            } else if (response.status === 422) {
                const data = await response.json();
                if (data.errors) {
                    ui.handleValidationErrors(data.errors);
                }
                ui.showAlert('Validation failed', 'danger');
            } else if (response.status === 401) {
                ui.showAlert('Invalid email or password', 'danger');
            } else {
                ui.showAlert('An error occurred. Please try again later', 'danger');
            }
        } catch (error) {
            console.error('Login error:', error);
            ui.showAlert('Network error. Please try again', 'danger');
        } finally {
            ui.hideButtonLoading(submitBtn);
        }
    });

    // Clear errors on input
    form.querySelectorAll('.form-control').forEach(field => {
        field.addEventListener('input', () => {
            ui.clearFieldError(field.id);
        });
    });
}

// Handle Register Form
function handleRegisterForm() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        ui.clearAllErrors();

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;
        const csrfToken = form.querySelector('input[name="_token"]').value;
        const submitBtn = form.querySelector('button[type="submit"]');

        ui.showButtonLoading(submitBtn);

        try {
            // Use web-based register endpoint
            const response = await fetch('/client/register-submit', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name, email, password, password_confirmation }),
            });

            if (response.ok) {
                ui.showAlert('Registration successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = '/client/home';
                }, 500);
            } else if (response.status === 422) {
                const data = await response.json();
                if (data.errors) {
                    ui.handleValidationErrors(data.errors);
                }
                ui.showAlert('Validation failed', 'danger');
            } else {
                ui.showAlert('An error occurred. Please try again later', 'danger');
            }
        } catch (error) {
            console.error('Register error:', error);
            ui.showAlert('Network error. Please try again', 'danger');
        } finally {
            ui.hideButtonLoading(submitBtn);
        }
    });

    // Clear errors on input
    form.querySelectorAll('.form-control').forEach(field => {
        field.addEventListener('input', () => {
            ui.clearFieldError(field.id);
        });
    });
}

// Initialize Home Page
function initHomePage() {
    if (!requireAuth()) return;

    const user = api.getUser();
    if (user) {
        // Display user info
        const userNameElement = document.getElementById('userName');
        const userEmailElement = document.getElementById('userEmail');
        const userAvatarElement = document.getElementById('userAvatar');

        if (userNameElement) userNameElement.textContent = user.name;
        if (userEmailElement) userEmailElement.textContent = user.email;
        if (userAvatarElement) {
            userAvatarElement.textContent = user.name.charAt(0).toUpperCase();
        }
    }

    // Handle logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            if (confirm('Are you sure you want to sign out?')) {
                await api.logout();
                window.location.href = '/client/login';
            }
        });
    }
}

// Export for use in HTML pages
window.auth = {
    api,
    ui,
    requireAuth,
    requireGuest,
    handleLoginForm,
    handleRegisterForm,
    initHomePage
};

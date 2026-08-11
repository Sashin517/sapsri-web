document.addEventListener('DOMContentLoaded', () => {
    
    const signupForm = document.getElementById('signupForm');
    const otpForm = document.getElementById('otpForm');
    const authAlert = document.getElementById('auth-alert');

    // ==========================================
    // LOGIN & FORGOT PASSWORD FLOWS
    // ==========================================

    const loginForm = document.getElementById('loginForm');
    const forgotPassForm = document.getElementById('forgotPassForm');
    const resetOtpForm = document.getElementById('resetOtpForm');
    const newPasswordForm = document.getElementById('newPasswordForm');

    // Utility to show error messages
    const showError = (msg) => {
        authAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>${msg}`;
        authAlert.classList.remove('d-none');
    };

    // Helper to toggle button loading states
    const toggleButtonLoading = (btnId, isLoading) => {
        const btn = document.getElementById(btnId);
        if (isLoading) {
            btn.querySelector('.btn-text').classList.add('d-none');
            btn.querySelector('.spinner-border').classList.remove('d-none');
            btn.disabled = true;
        } else {
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.spinner-border').classList.add('d-none');
            btn.disabled = false;
        }
    };

    // Step 1: Handle Initial Signup
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            authAlert.classList.add('d-none');

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                showError("Passwords do not match.");
                return;
            }

            toggleButtonLoading('btn-signup', true);

            try {
                const formData = new FormData(signupForm);
                const response = await fetch('actions/auth/process-signup.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();

                if (data.status === 'success') {
                    // Transition to OTP View
                    document.getElementById('step-1-signup').classList.add('d-none');
                    document.getElementById('step-2-otp').classList.remove('d-none');
                    
                    // Pass email forward to next steps
                    const userEmail = document.getElementById('email').value;
                    document.getElementById('display-email').innerText = userEmail;
                    document.getElementById('verify-email').value = userEmail;
                } else {
                    showError(data.message || "An error occurred during signup.");
                }
            } catch (error) {
                showError("Network error. Please try again.");
            } finally {
                toggleButtonLoading('btn-signup', false);
            }
        });
    }

    // Step 2: Handle OTP Verification
    if (otpForm) {
        otpForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            authAlert.classList.add('d-none');
            toggleButtonLoading('btn-verify', true);

            try {
                const formData = new FormData(otpForm);
                const response = await fetch('actions/auth/verify-signup-otp.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();

                if (data.status === 'success') {
                    // Transition to Success View
                    document.getElementById('step-2-otp').classList.add('d-none');
                    document.getElementById('step-3-success').classList.remove('d-none');
                } else {
                    showError(data.message || "Invalid or expired verification code.");
                }
            } catch (error) {
                showError("Network error. Please try again.");
            } finally {
                toggleButtonLoading('btn-verify', false);
            }
        });
    }

    // Handle Login
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            authAlert.classList.add('d-none');
            toggleButtonLoading('btn-login', true);

            try {
                const response = await fetch('actions/auth/process-login.php', {
                    method: 'POST',
                    body: new FormData(loginForm)
                });
                const data = await response.json();

                if (data.status === 'success') {
                    window.location.href = 'index.php'; // Redirect to dashboard
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError("Network error. Please try again.");
            } finally {
                toggleButtonLoading('btn-login', false);
            }
        });
    }

    // Handle Forgot Password Request
    if (forgotPassForm) {
        forgotPassForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            authAlert.classList.add('d-none');
            toggleButtonLoading('btn-send-reset', true);

            try {
                const response = await fetch('actions/auth/process-forgot-pass.php', {
                    method: 'POST',
                    body: new FormData(forgotPassForm)
                });
                const data = await response.json();

                if (data.status === 'success') {
                    const userEmail = document.getElementById('forgotEmail').value;
                    document.getElementById('display-reset-email').innerText = userEmail;
                    document.getElementById('reset-verify-email').value = userEmail;
                    
                    document.getElementById('step-2-forgot').classList.add('d-none');
                    document.getElementById('step-3-otp').classList.remove('d-none');
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError("Network error. Please try again.");
            } finally {
                toggleButtonLoading('btn-send-reset', false);
            }
        });
    }

    // Handle Reset OTP Verification
    if (resetOtpForm) {
        resetOtpForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            authAlert.classList.add('d-none');
            toggleButtonLoading('btn-verify-reset', true);

            try {
                const response = await fetch('actions/auth/verify-forgot-pass-otp.php', {
                    method: 'POST',
                    body: new FormData(resetOtpForm)
                });
                const data = await response.json();

                if (data.status === 'success') {
                    document.getElementById('step-3-otp').classList.add('d-none');
                    document.getElementById('step-4-new-pass').classList.remove('d-none');
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError("Network error. Please try again.");
            } finally {
                toggleButtonLoading('btn-verify-reset', false);
            }
        });
    }

    // Handle New Password Submission
    if (newPasswordForm) {
        newPasswordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            authAlert.classList.add('d-none');

            const password = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmNewPassword').value;

            if (password !== confirmPassword) {
                showError("Passwords do not match.");
                return;
            }

            toggleButtonLoading('btn-save-pass', true);

            try {
                const response = await fetch('actions/auth/reset-password.php', {
                    method: 'POST',
                    body: new FormData(newPasswordForm)
                });
                const data = await response.json();

                if (data.status === 'success') {
                    document.getElementById('step-4-new-pass').classList.add('d-none');
                    document.getElementById('step-5-success').classList.remove('d-none');
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError("Network error. Please try again.");
            } finally {
                toggleButtonLoading('btn-save-pass', false);
            }
        });
    }
});
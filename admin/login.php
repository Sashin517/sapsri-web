<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SAPSRI Admin | Login</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
      --sapsri-red: #A20A35; /* Primary Brand Crimson */
      --text-dark: #1A1A1A;
      --text-muted: #666666;
    }

    body {
      font-family: 'Inter', -apple-system, sans-serif;
      background-color: #ffffff;
      color: var(--text-dark);
      overflow-x: hidden;
    }

    /* --- Left Panel Styling --- */
    .login-left-panel {
      padding: 1.5rem; /* Creates the margin around the image */
    }

    .login-image-container {
      position: relative;
      height: 100%;
      width: 100%;
      border-radius: 20px;
      overflow: hidden;
      background-color: var(--sapsri-red);
    }

    .login-image-container::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('assets/img/sapsri-fluid-bg.png');
      background-size: cover;
      background-position: center;
      transform: scaleY(-1);
      transform-origin: center;
      z-index: 0;
    }

    .left-panel-content {
      position: absolute;
      top: 3rem;
      left: 3rem;
      color: #ffffff;
      z-index: 1;
    }

    .left-panel-content h1 {
      font-size: 2.5rem;
      font-weight: 400;
      line-height: 1.3;
      margin-top: 0.5rem;
    }

    .left-panel-logo {
      position: absolute;
      bottom: 3rem;
      right: 3rem;
      height: 50px;
      /* Replace with your actual logo image path */
      /* Note: Ensure the logo file has white text or high contrast if placed on the red background */
    }

    /* --- Right Panel Styling --- */
    .login-right-panel {
      padding: 2rem;
    }

    .login-form-wrapper {
      max-width: 420px;
      width: 100%;
      margin: 0 auto;
    }

    .login-title {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    .login-subtitle {
      color: var(--text-muted);
      margin-bottom: 2.5rem;
    }

    /* Form Inputs */
    .form-label {
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 0.4rem;
    }

    .custom-input {
      border-radius: 8px;
      padding: 0.75rem 1rem;
      border: 1px solid #ced4da;
      font-size: 0.95rem;
    }

    .custom-input:focus {
      border-color: var(--sapsri-red);
      box-shadow: 0 0 0 3px rgba(162, 10, 53, 0.1);
    }

    /* Active/Filled state matching your design */
    .custom-input.is-active {
      border-color: var(--sapsri-red);
    }

    /* Password Group */
    .password-wrapper {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--text-muted);
    }

    /* Custom Checkbox */
    .form-check-input:checked {
      background-color: var(--sapsri-red);
      border-color: var(--sapsri-red);
    }

    /* Buttons and Links */
    .btn-primary-red {
      background-color: var(--sapsri-red);
      color: white;
      border-radius: 8px;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary-red:hover {
      background-color: #8a082d;
      color: white;
    }

    .link-red {
      color: var(--sapsri-red);
      text-decoration: none;
      font-weight: 600;
    }

    .link-red:hover {
      text-decoration: underline;
    }

    .text-sm {
      font-size: 0.85rem;
    }

    .link-underline {
      color: var(--text-dark);
      text-decoration: underline;
    }

    .footer-copyright {
      margin-top: 4rem;
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* 6-Box OTP Layout Styles */
    .otp-container { display: flex; gap: 10px; justify-content: center; margin-bottom: 1.5rem; }
    .otp-box { width: 50px; height: 60px; text-align: center; font-size: 1.5rem; font-weight: 700; border-radius: 8px; border: 1px solid #ced4da; background-color: #f8f9fa; transition: all 0.2s ease; -moz-appearance: textfield; }
    .otp-box::-webkit-outer-spin-button, .otp-box::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .otp-box:focus { border-color: var(--sapsri-red); background-color: #ffffff; box-shadow: 0 0 0 3px rgba(162, 10, 53, 0.1); outline: none; }
    .step-icon-wrapper { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; }
    .bg-soft-red { background-color: rgba(162, 10, 53, 0.1); color: var(--sapsri-red); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .step-icon-wrapper i { font-size: 2.5rem; }
  </style>
</head>
<body>

  <div class="container-fluid p-0 min-vh-100 d-flex flex-column">
    <div class="row g-0 flex-grow-1">
      
      <!-- Left Branding Panel (Hidden on mobile) -->
      <div class="col-lg-6 d-none d-lg-block login-left-panel">
        <div class="login-image-container">
          <div class="left-panel-content">
            <p class="mb-0">You can easily</p>
            <h1>Manage Sapsri Web<br>content with admin portal</h1>
          </div>
          <!-- Inject your SAPSRI Logo Here -->
          <img src="assets/img/sapsri-logo.png" alt="SAPSRI Logo" class="left-panel-logo">
        </div>
      </div>

      <!-- Right Form Panel -->
      <div class="col-lg-6 d-flex align-items-center justify-content-center login-right-panel">
        <div class="login-form-wrapper">
          
          <div id="auth-alert" class="alert alert-danger d-none" role="alert"></div>

          <!-- STEP 1: LOGIN FORM -->
          <div id="step-1-login">
            <h2 class="login-title">Log In</h2>
            <p class="login-subtitle">Enter your credentials to access the portal.</p>

            <form id="loginForm">
              <div class="mb-3">
                <label for="loginEmail" class="form-label">Email</label>
                <input type="email" class="form-control custom-input" id="loginEmail" name="email" placeholder="Type your email..." required>
              </div>

              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label for="loginPassword" class="form-label mb-0">Password</label>
                  <a href="#" class="link-red text-sm" id="showForgotPass">Forgot Password?</a>
                </div>
                <div class="password-wrapper">
                  <input type="password" class="form-control custom-input password-input" id="loginPassword" name="password" placeholder="Enter your password..." required>
                  <i class="bi bi-eye password-toggle"></i>
                </div>
              </div>

              <button type="submit" class="btn btn-primary-red w-100 mb-4" id="btn-login">
                <span class="btn-text">Log In</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </form>
            
            <div class="text-center text-sm">
              Don't have an account? <a href="signup.php" class="link-red">Sign up</a>
            </div>
          </div>

          <!-- STEP 2: FORGOT PASSWORD REQUEST -->
          <div id="step-2-forgot" class="d-none text-center">
            <div class="step-icon-wrapper bg-soft-red">
              <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2 class="login-title">Reset Password</h2>
            <p class="login-subtitle">Enter your email address and we'll send you a code to reset your password.</p>

            <form id="forgotPassForm">
              <div class="mb-4 text-start">
                <label for="forgotEmail" class="form-label">Email Address</label>
                <input type="email" class="form-control custom-input" id="forgotEmail" name="email" placeholder="name@example.com" required>
              </div>
              <button type="submit" class="btn btn-primary-red w-100 mb-3" id="btn-send-reset">
                <span class="btn-text">Send Verification Code</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
              <a href="#" class="link-red text-sm" id="backToLogin1"><i class="bi bi-arrow-left"></i> Back to Log In</a>
            </form>
          </div>

          <!-- STEP 3: OTP VERIFICATION -->
          <div id="step-3-otp" class="d-none text-center">
            <div class="step-icon-wrapper bg-soft-red">
              <i class="bi bi-envelope-paper-fill"></i>
            </div>
            <h2 class="login-title">Check Your Email</h2>
            <p class="login-subtitle">We've sent a 6-digit verification code to <br><strong id="display-reset-email" class="text-dark"></strong></p>

            <form id="resetOtpForm">
              <input type="hidden" id="reset-verify-email" name="email">
              <input type="hidden" id="resetFinalOtpCode" name="otp_code" required>
              
              <div class="otp-container">
                <input type="text" class="otp-box reset-otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box reset-otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box reset-otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box reset-otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box reset-otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box reset-otp-box" maxlength="1" inputmode="numeric" required>
              </div>

              <button type="submit" class="btn btn-primary-red w-100 mb-4" id="btn-verify-reset">
                <span class="btn-text">Verify Code</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </form>
            <a href="#" class="link-red text-sm" id="backToLogin2"><i class="bi bi-arrow-left"></i> Back to Log In</a>
          </div>

          <!-- STEP 4: CREATE NEW PASSWORD -->
          <div id="step-4-new-pass" class="d-none">
            <h2 class="login-title">Set New Password</h2>
            <p class="login-subtitle">Create a new, strong password for your account.</p>

            <form id="newPasswordForm">
              <div class="mb-3">
                <label for="newPassword" class="form-label">New Password</label>
                <div class="password-wrapper">
                  <input type="password" class="form-control custom-input password-input" id="newPassword" name="password" placeholder="Create a password..." required minlength="8">
                  <i class="bi bi-eye password-toggle"></i>
                </div>
              </div>

              <div class="mb-4">
                <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                <div class="password-wrapper">
                  <input type="password" class="form-control custom-input password-input" id="confirmNewPassword" name="confirm_password" placeholder="Confirm your password..." required>
                  <i class="bi bi-eye password-toggle"></i>
                </div>
              </div>

              <button type="submit" class="btn btn-primary-red w-100 mb-4" id="btn-save-pass">
                <span class="btn-text">Update Password</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </form>
          </div>

          <!-- STEP 5: SUCCESS -->
          <div id="step-5-success" class="d-none text-center py-2">
            <div class="step-icon-wrapper bg-soft-success">
              <i class="bi bi-check-lg"></i>
            </div>
            <h2 class="login-title mb-2">Password Reset!</h2>
            <p class="login-subtitle mb-4">Your password has been changed successfully.</p>
            <button onclick="window.location.reload();" class="btn btn-outline-red w-100">Proceed to Log In</button>
          </div>

          <!-- Copyright -->
          <div class="text-center footer-copyright">
            © 2026 <span class="link-red">SL Devs</span>, All rights reserved
          </div>

        </div>
      </div>

    </div>
  </div>

  <!-- JavaScript for Password Toggle Visibility -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const togglePassword = document.querySelector('#togglePassword');
      const passwordInput = document.querySelector('#password');

      togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye icon class
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
      });
    });
  </script>

  <script src="assets/js/auth.js"></script>

  <!-- Script for UI toggles and OTP box handling -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      
      // Password Toggles
      document.querySelectorAll('.password-toggle').forEach(icon => {
        icon.addEventListener('click', function () {
          const input = this.previousElementSibling;
          const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
          input.setAttribute('type', type);
          this.classList.toggle('bi-eye');
          this.classList.toggle('bi-eye-slash');
        });
      });

      // OTP Box Logic
      const otpBoxes = document.querySelectorAll('.reset-otp-box');
      const finalOtpInput = document.getElementById('resetFinalOtpCode');

      otpBoxes.forEach((box, index) => {
        box.addEventListener('input', (e) => {
          e.target.value = e.target.value.replace(/[^0-9]/g, '');
          if (e.target.value !== '' && index < otpBoxes.length - 1) {
            otpBoxes[index + 1].focus();
          }
          updateFinalOtp();
        });
        box.addEventListener('keydown', (e) => {
          if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
            otpBoxes[index - 1].focus();
          }
        });
        box.addEventListener('paste', (e) => {
          e.preventDefault();
          const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
          if (pastedData.length > 0) {
            for (let i = 0; i < pastedData.length; i++) {
              if (otpBoxes[i]) otpBoxes[i].value = pastedData[i];
            }
            otpBoxes[Math.min(pastedData.length, 5)].focus();
            updateFinalOtp();
          }
        });
      });

      function updateFinalOtp() {
        let otpString = '';
        otpBoxes.forEach(b => otpString += b.value);
        finalOtpInput.value = otpString;
      }

      // View Transitions
      const toggleView = (hideId, showId) => {
        document.getElementById('auth-alert').classList.add('d-none');
        document.getElementById(hideId).classList.add('d-none');
        document.getElementById(showId).classList.remove('d-none');
      };

      document.getElementById('showForgotPass').addEventListener('click', (e) => {
        e.preventDefault(); toggleView('step-1-login', 'step-2-forgot');
      });
      document.getElementById('backToLogin1').addEventListener('click', (e) => {
        e.preventDefault(); toggleView('step-2-forgot', 'step-1-login');
      });
      document.getElementById('backToLogin2').addEventListener('click', (e) => {
        e.preventDefault(); toggleView('step-3-otp', 'step-1-login');
      });
    });
  </script>

</body>
</html>
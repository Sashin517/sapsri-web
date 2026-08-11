<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SAPSRI Admin | Sign Up</title>
  
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
    /* Custom styles for OTP and Success states */
    /* --- 6-Box OTP Layout Styles --- */
    .otp-container {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-bottom: 1.5rem;
    }
    .otp-box {
      width: 50px;
      height: 60px;
      text-align: center;
      font-size: 1.5rem;
      font-weight: 700;
      border-radius: 8px;
      border: 1px solid #ced4da;
      background-color: #f8f9fa;
      transition: all 0.2s ease;
      /* Hide arrows in number inputs */
      -moz-appearance: textfield;
    }
    .otp-box::-webkit-outer-spin-button,
    .otp-box::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    .otp-box:focus {
      border-color: var(--sapsri-red);
      background-color: #ffffff;
      box-shadow: 0 0 0 3px rgba(162, 10, 53, 0.1);
      outline: none;
    }
    .step-icon-wrapper {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem auto;
    }
    .bg-soft-red {
      background-color: rgba(162, 10, 53, 0.1);
      color: var(--sapsri-red);
    }
    .bg-soft-success {
      background-color: rgba(25, 135, 84, 0.1);
      color: #198754;
    }
    .step-icon-wrapper i {
      font-size: 2.5rem;
    }
    .btn-outline-red {
      color: var(--sapsri-red);
      border: 2px solid var(--sapsri-red);
      background-color: transparent;
      border-radius: 8px;
      padding: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-outline-red:hover {
      background-color: var(--sapsri-red);
      color: white;
    }
    .approval-card {
      background-color: #f8f9fa;
      border: 1px solid #eaeaea;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 2rem;
      font-size: 0.9rem;
    }
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
          <img src="assets/img/sapsri-logo.png" alt="SAPSRI Logo" class="left-panel-logo">
        </div>
      </div>

      <!-- Right Form Panel -->
      <div class="col-lg-6 d-flex align-items-center justify-content-center login-right-panel">
        <div class="login-form-wrapper">
          
          <div id="auth-alert" class="alert alert-danger d-none" role="alert"></div>

          <!-- STEP 1: SIGNUP FORM -->
          <div id="step-1-signup">
            <h2 class="login-title">Create an Account</h2>
            <p class="login-subtitle">Sign up to access the SAPSRI admin portal.</p>

            <form id="signupForm">
              <div class="row mb-3">
                <div class="col-md-6 mb-3 mb-md-0">
                  <label for="firstName" class="form-label">First Name</label>
                  <input type="text" class="form-control custom-input" id="firstName" name="first_name" placeholder="John" required>
                </div>
                <div class="col-md-6">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input type="text" class="form-control custom-input" id="lastName" name="last_name" placeholder="Doe" required>
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control custom-input" id="email" name="email" placeholder="Type your email..." required>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrapper">
                  <input type="password" class="form-control custom-input password-input" id="password" name="password" placeholder="Create a password..." required minlength="8">
                  <i class="bi bi-eye password-toggle"></i>
                </div>
              </div>

              <div class="mb-4">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                  <input type="password" class="form-control custom-input password-input" id="confirmPassword" name="confirm_password" placeholder="Confirm your password..." required>
                  <i class="bi bi-eye password-toggle"></i>
                </div>
              </div>

              <div class="mb-4 form-check d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-2 shadow-none" id="termsCheck" name="terms" required checked>
                <label class="form-check-label text-sm" for="termsCheck">
                  I agree to the <a href="#" class="link-underline">terms & privacy</a>
                </label>
              </div>

              <button type="submit" class="btn btn-primary-red w-100 mb-4" id="btn-signup">
                <span class="btn-text">Sign Up</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </form>
            
            <div class="text-center text-sm mb-3">
              Already have an account? <a href="login.php" class="link-red">Log in</a>
            </div>
            
            <div class="text-center text-sm">
              <i class="bi bi-headset me-1"></i> Trouble signing up? <a href="contact.php" class="link-red">Contact us</a>
            </div>
          </div>

          <!-- STEP 2: OTP VERIFICATION (6-BOX) -->
          <div id="step-2-otp" class="d-none text-center">
            
            <div class="step-icon-wrapper bg-soft-red">
              <i class="bi bi-envelope-paper-fill"></i>
            </div>
            
            <h2 class="login-title">Check Your Email</h2>
            <p class="login-subtitle">We've sent a 6-digit verification code to <br><strong id="display-email" class="text-dark"></strong></p>

            <form id="otpForm">
              <input type="hidden" id="verify-email" name="email">
              
              <!-- Hidden input that actually gets submitted to PHP -->
              <input type="hidden" id="finalOtpCode" name="otp_code" required>
              
              <div class="otp-container">
                <input type="text" class="otp-box" maxlength="1" inputmode="numeric" autocomplete="one-time-code" required>
                <input type="text" class="otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box" maxlength="1" inputmode="numeric" required>
                <input type="text" class="otp-box" maxlength="1" inputmode="numeric" required>
              </div>

              <button type="submit" class="btn btn-primary-red w-100 mb-4" id="btn-verify">
                <span class="btn-text">Verify Account</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
            </form>
            
            <div class="text-center text-sm">
              Didn't receive the code? <a href="javascript:void(0);" class="link-red fw-bold" id="resendCode">Resend Code</a>
            </div>
          </div>

          <!-- STEP 3: SUCCESS STATE (IMPROVED) -->
          <div id="step-3-success" class="d-none text-center py-2">
            
            <div class="step-icon-wrapper bg-soft-success">
              <i class="bi bi-check-lg"></i>
            </div>
            
            <h2 class="login-title mb-2">Verification Complete!</h2>
            <p class="login-subtitle mb-4">Your email has been successfully verified.</p>
            
            <!-- Official Looking Info Card -->
            <div class="approval-card text-start d-flex align-items-start">
              <i class="bi bi-info-circle-fill text-muted me-3 fs-5"></i>
              <div>
                <strong class="d-block mb-1 text-dark">Pending Admin Approval</strong>
                <span class="text-muted">Please wait for an existing administrator to review and approve your access request before logging in.</span>
              </div>
            </div>

            <a href="login.php" class="btn btn-outline-red w-100">Back to Login</a>
          </div>

          <!-- Copyright -->
          <div class="text-center footer-copyright">
            © 2026 <span class="link-red">SL Devs</span>, All rights reserved
          </div>

    <!-- Authentication Logic -->
  <script src="assets/js/auth.js"></script>

  <!-- JavaScript for Password Toggle Visibility -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Select all toggle icons on the page
      const toggleIcons = document.querySelectorAll('.password-toggle');

      toggleIcons.forEach(function(icon) {
        icon.addEventListener('click', function () {
          // Find the input field right before this specific icon
          const passwordInput = this.previousElementSibling;
          
          // Toggle the type attribute
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          
          // Toggle the eye icon class
          this.classList.toggle('bi-eye');
          this.classList.toggle('bi-eye-slash');
        });
      });
    });
  </script>

  <!-- JavaScript for 6-Box OTP Auto-Focus -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const otpBoxes = document.querySelectorAll('.otp-box');
      const finalOtpInput = document.getElementById('finalOtpCode');

      otpBoxes.forEach((box, index) => {
        // Handle typing
        box.addEventListener('input', (e) => {
          // Force numbers only
          e.target.value = e.target.value.replace(/[^0-9]/g, '');
          
          // Move to next box if a number was entered
          if (e.target.value !== '' && index < otpBoxes.length - 1) {
            otpBoxes[index + 1].focus();
          }
          updateFinalOtp();
        });

        // Handle Backspace
        box.addEventListener('keydown', (e) => {
          // Move to previous box if backspace is pressed on an empty box
          if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
            otpBoxes[index - 1].focus();
          }
        });
        
        // Handle Copy/Paste (if user pastes the whole 6-digit code at once)
        box.addEventListener('paste', (e) => {
          e.preventDefault();
          const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
          if (pastedData.length > 0) {
            for (let i = 0; i < pastedData.length; i++) {
              if (otpBoxes[i]) {
                otpBoxes[i].value = pastedData[i];
              }
            }
            // Focus the last filled box
            const focusIndex = Math.min(pastedData.length, 5);
            otpBoxes[focusIndex].focus();
            updateFinalOtp();
          }
        });
      });

      // Combine the 6 boxes into the hidden finalOtpCode field
      function updateFinalOtp() {
        let otpString = '';
        otpBoxes.forEach(b => otpString += b.value);
        finalOtpInput.value = otpString;
      }
    });
  </script>

</body>
</html>
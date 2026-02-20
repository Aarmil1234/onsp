<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Signup</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --secondary: #8b5cf6;
      --success: #10b981;
      --dark: #0f172a;
      --gray: #64748b;
      --light-bg: #f8fafc;
      --border: #e2e8f0;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .signup-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 500px;
      padding: 20px;
    }

    .signup-card {
      background: white;
      border-radius: 24px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }

    .signup-header {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      padding: 1rem;
      text-align: center;
      color: white;
      position: relative;
    }

    .signup-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E");
    }

    .brand-logo {
      font-size: 3rem;
      position: relative;
      z-index: 1;
    }

    .signup-header h2 {
      font-size: 1.8rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      position: relative;
      z-index: 1;
    }

    .signup-header p {
      font-size: 1rem;
      opacity: 0.95;
      position: relative;
      z-index: 1;
    }

    .signup-body {
      padding: 2.5rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      display: block;
    }

    .input-group-custom {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 1.2rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.2rem;
      color: var(--gray);
      z-index: 1;
    }

    .form-control-modern {
      width: 100%;
      padding: 0.9rem 1.2rem 0.9rem 3rem;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s;
      font-family: 'Inter', sans-serif;
    }

    .form-control-modern:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-control-modern::placeholder {
      color: #cbd5e1;
    }

    .password-toggle {
      position: relative;
    }

    .password-toggle-btn {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--gray);
      cursor: pointer;
      font-size: 1.2rem;
      padding: 0.3rem;
      transition: color 0.3s;
      z-index: 2;
    }

    .password-toggle-btn:hover {
      color: var(--dark);
    }

    .password-strength {
      margin-top: 0.5rem;
      font-size: 0.8rem;
    }

    .strength-bar {
      height: 4px;
      background: var(--border);
      border-radius: 2px;
      overflow: hidden;
      margin-bottom: 0.3rem;
    }

    .strength-fill {
      height: 100%;
      width: 0%;
      transition: all 0.3s;
      border-radius: 2px;
    }

    .strength-text {
      color: var(--gray);
      font-weight: 600;
    }

    .btn-signup {
      width: 100%;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 1rem;
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 0.5rem;
    }

    .btn-signup:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }

    .btn-signup:active {
      transform: translateY(0);
    }

    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 1.5rem 0;
      color: var(--gray);
      font-size: 0.85rem;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid var(--border);
    }

    .divider span {
      padding: 0 1rem;
    }

    .login-link {
      text-align: center;
      margin-top: 1rem;
    }

    .login-link a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      transition: color 0.3s;
    }

    .login-link a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .back-home {
      text-align: center;
      margin-top: 2rem;
    }

    .back-home a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.7rem 1.5rem;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s;
    }

    .back-home a:hover {
      background: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
    }

    .benefits {
      margin-top: 1.5rem;
      padding: 1.5rem;
      background: var(--light-bg);
      border-radius: 12px;
    }

    .benefits-title {
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }

    .benefit-item {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      margin-bottom: 0.8rem;
      font-size: 0.9rem;
      color: var(--dark);
    }

    .benefit-item:last-child {
      margin-bottom: 0;
    }

    .benefit-icon {
      width: 24px;
      height: 24px;
      background: linear-gradient(135deg, var(--success), #059669);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.75rem;
      flex-shrink: 0;
    }

    @media (max-width: 576px) {
      .signup-header {
        padding: 2rem 1.5rem 1.5rem;
      }

      .signup-header h2 {
        font-size: 1.5rem;
      }

      .signup-body {
        padding: 2rem 1.5rem;
      }

      .brand-logo {
        font-size: 2.5rem;
      }
    }
  </style>
</head>
<body>

<div class="signup-container">
  <div class="signup-card">
    
    <!-- Header -->
    <div class="signup-header">
        <img src="../assets/logo.png" alt="ONSP Logo" class="brand-logo" style="height: 130px; border-radius: 130px">
    </div>

    <!-- Body -->
    <div class="signup-body">
      
      <form method="POST" action="student_signup_action.php" id="signupForm">
        
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <div class="input-group-custom">
            <span class="input-icon">👤</span>
            <input 
              type="text" 
              name="name" 
              class="form-control-modern" 
              placeholder="Enter your full name" 
              required
              autocomplete="name"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <div class="input-group-custom">
            <span class="input-icon">📧</span>
            <input 
              type="email" 
              name="email" 
              class="form-control-modern" 
              placeholder="Enter your email" 
              required
              autocomplete="email"
            >
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="password-toggle">
            <div class="input-group-custom">
              <span class="input-icon">🔒</span>
              <input 
                type="password" 
                name="password" 
                id="password"
                class="form-control-modern" 
                placeholder="Create a strong password" 
                required
                autocomplete="new-password"
                oninput="checkPasswordStrength()"
              >
              <button 
                type="button" 
                class="password-toggle-btn" 
                onclick="togglePassword()"
                id="toggleBtn"
              >
                👁️
              </button>
            </div>
          </div>
          <div class="password-strength" id="passwordStrength" style="display: none;">
            <div class="strength-bar">
              <div class="strength-fill" id="strengthFill"></div>
            </div>
            <div class="strength-text" id="strengthText"></div>
          </div>
        </div>

        <button type="submit" class="btn-signup">Create Free Account</button>
      </form>

      <div class="divider">
        <span>Already have an account?</span>
      </div>

      <div class="login-link">
        <a href="login.php">Sign in to your account →</a>
      </div>


    </div>
  </div>

</div>

<script>
function togglePassword() {
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.getElementById('toggleBtn');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleBtn.textContent = '🙈';
  } else {
    passwordInput.type = 'password';
    toggleBtn.textContent = '👁️';
  }
}

function checkPasswordStrength() {
  const password = document.getElementById('password').value;
  const strengthDiv = document.getElementById('passwordStrength');
  const strengthFill = document.getElementById('strengthFill');
  const strengthText = document.getElementById('strengthText');
  
  if (password.length === 0) {
    strengthDiv.style.display = 'none';
    return;
  }
  
  strengthDiv.style.display = 'block';
  
  let strength = 0;
  let text = '';
  let color = '';
  
  // Length check
  if (password.length >= 8) strength += 25;
  if (password.length >= 12) strength += 25;
  
  // Character variety checks
  if (/[a-z]/.test(password)) strength += 15;
  if (/[A-Z]/.test(password)) strength += 15;
  if (/[0-9]/.test(password)) strength += 10;
  if (/[^a-zA-Z0-9]/.test(password)) strength += 10;
  
  if (strength < 40) {
    text = 'Weak';
    color = '#ef4444';
  } else if (strength < 70) {
    text = 'Medium';
    color = '#f59e0b';
  } else {
    text = 'Strong';
    color = '#10b981';
  }
  
  strengthFill.style.width = strength + '%';
  strengthFill.style.background = color;
  strengthText.textContent = text;
  strengthText.style.color = color;
}
</script>

</body>
</html>
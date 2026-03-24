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

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  font-family:'Inter',sans-serif;
  background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
}

.signup-container{
  width:100%;
  max-width:500px;
  padding:20px;
}

.signup-card{
  background:white;
  border-radius:24px;
  box-shadow:0 20px 60px rgba(0,0,0,0.3);
  overflow:hidden;
}

.signup-header{
  background:linear-gradient(135deg,var(--primary),var(--secondary));
  padding:1rem;
  text-align:center;
}

.signup-body{
  padding:2.5rem;
}

.form-group{
  margin-bottom:1.5rem;
}

.form-label{
  font-weight:600;
  color:var(--dark);
  margin-bottom:0.5rem;
  font-size:0.9rem;
  display:block;
}

.input-group-custom{
  position:relative;
}

.input-icon{
  position:absolute;
  left:1.2rem;
  top:50%;
  transform:translateY(-50%);
  font-size:1.2rem;
  color:var(--gray);
}

.form-control-modern{
  width:100%;
  padding:0.9rem 1.2rem 0.9rem 3rem;
  border:2px solid var(--border);
  border-radius:12px;
  font-size:1rem;
}

.form-control-modern:focus{
  outline:none;
  border-color:var(--primary);
  box-shadow:0 0 0 4px rgba(99,102,241,0.1);
}

.password-toggle{
  position:relative;
}

.password-toggle-btn{
  position:absolute;
  right:1rem;
  top:50%;
  transform:translateY(-50%);
  background:none;
  border:none;
  cursor:pointer;
}

.btn-signup{
  width:100%;
  background:linear-gradient(135deg,var(--primary),var(--primary-dark));
  color:white;
  padding:1rem;
  border:none;
  border-radius:12px;
  font-weight:700;
  cursor:pointer;
}

.btn-signup:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 25px rgba(99,102,241,0.4);
}

.divider{
  display:flex;
  align-items:center;
  text-align:center;
  margin:1.5rem 0;
  color:var(--gray);
  font-size:0.85rem;
}

.divider::before,
.divider::after{
  content:'';
  flex:1;
  border-bottom:1px solid var(--border);
}

.divider span{
  padding:0 1rem;
}

.login-link{
  text-align:center;
  margin-top:1rem;
}

.login-link a{
  color:var(--primary);
  text-decoration:none;
  font-weight:600;
}

.login-link a:hover{
  text-decoration:underline;
}

.password-strength{
  margin-top:0.5rem;
  font-size:0.8rem;
}

.strength-bar{
  height:4px;
  background:var(--border);
  border-radius:2px;
  overflow:hidden;
}

.strength-fill{
  height:100%;
  width:0%;
}

</style>

</head>
<body>

<div class="signup-container">
<div class="signup-card">

<div class="signup-header">
<img src="../assets/logo.png" style="height:130px;border-radius:130px">
</div>

<div class="signup-body">

<form method="POST" action="student_signup_action.php" id="signupForm">

<div class="form-group">
<label class="form-label">Full Name</label>
<div class="input-group-custom">
<span class="input-icon">👤</span>
<input type="text" name="name" class="form-control-modern" placeholder="Enter your full name" required>
</div>
</div>

<div class="form-group">
<label class="form-label">Email Address</label>
<div class="input-group-custom">
<span class="input-icon">📧</span>
<input type="email" name="email" class="form-control-modern" placeholder="Enter your email" required>
</div>
</div>

<div class="form-group">
<label class="form-label">Password</label>

<div class="password-toggle">
<div class="input-group-custom">
<span class="input-icon">🔒</span>

<input type="password" name="password" id="password" class="form-control-modern"
placeholder="Create a strong password"
required
oninput="checkPasswordStrength()">

<button type="button" class="password-toggle-btn" onclick="togglePassword()" id="toggleBtn">
👁️
</button>

</div>
</div>

<div class="password-strength" id="passwordStrength" style="display:none">
<div class="strength-bar">
<div class="strength-fill" id="strengthFill"></div>
</div>
<div id="strengthText"></div>
</div>

</div>

<button type="submit" class="btn-signup">
Create Student Account
</button>

</form>

<div class="divider">
<span>Already have an account?</span>
</div>

<div class="login-link">
<a href="login.php">Sign in to your account →</a>
</div>

<!-- Tutor redirect section -->

<div class="divider">
<span>Want to register as a tutor?</span>
</div>

<div class="login-link">
<a href="../admin/create_tutor.php">Register as Tutor →</a>
</div>

</div>
</div>
</div>

<script>

function togglePassword(){
const passwordInput=document.getElementById('password');
const toggleBtn=document.getElementById('toggleBtn');

if(passwordInput.type==='password'){
passwordInput.type='text';
toggleBtn.textContent='🙈';
}else{
passwordInput.type='password';
toggleBtn.textContent='👁️';
}
}

function checkPasswordStrength(){

const password=document.getElementById('password').value;
const strengthDiv=document.getElementById('passwordStrength');
const strengthFill=document.getElementById('strengthFill');
const strengthText=document.getElementById('strengthText');

if(password.length===0){
strengthDiv.style.display='none';
return;
}

strengthDiv.style.display='block';

let strength=0;
let text='';
let color='';

if(password.length>=8) strength+=25;
if(password.length>=12) strength+=25;
if(/[a-z]/.test(password)) strength+=15;
if(/[A-Z]/.test(password)) strength+=15;
if(/[0-9]/.test(password)) strength+=10;
if(/[^a-zA-Z0-9]/.test(password)) strength+=10;

if(strength<40){
text='Weak';
color='#ef4444';
}
else if(strength<70){
text='Medium';
color='#f59e0b';
}
else{
text='Strong';
color='#10b981';
}

strengthFill.style.width=strength+'%';
strengthFill.style.background=color;
strengthText.textContent=text;
strengthText.style.color=color;

}

</script>

</body>
</html>
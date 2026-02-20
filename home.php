<?php
session_start();
require_once "config/supabase.php";
function getNotes(){

    $url = SUPABASE_URL .
    "/rest/v1/notes" .
    "?select=*,profiles(name),note_feedback(rating)" .
    "&order=created_at.desc";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "apikey: " . SUPABASE_ANON_KEY
        ]
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($res, true);

    return is_array($data) ? $data : [];
}


$notes = getNotes();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ONSP</title>
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
      --accent: #ec4899;
      --success: #10b981;
      --warning: #f59e0b;
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
      background: var(--light-bg);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--dark);
      line-height: 1.6;
    }

    /* NAVBAR */
    .navbar {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 1rem 0;
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .btn-login {
      border: 2px solid var(--border);
      padding: 0.5rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      color: var(--dark);
      transition: all 0.3s;
    }

    .btn-login:hover {
      border-color: var(--primary);
      color: var(--primary);
      background: rgba(99, 102, 241, 0.05);
    }

    .btn-tutor {
      background: var(--primary);
      color: white;
      padding: 0.5rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      border: none;
      transition: all 0.3s;
    }

    .btn-tutor:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
    }

    /* HERO SECTION */
    .hero {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 120px 0 100px;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 800;
      color: white;
      margin-bottom: 1.5rem;
      line-height: 1.2;
    }

    .hero .subtitle {
      font-size: 1.3rem;
      color: rgba(255, 255, 255, 0.95);
      margin-bottom: 2.5rem;
      font-weight: 500;
    }

    .btn-hero-primary {
      background: white;
      color: var(--primary);
      padding: 1rem 2.5rem;
      border-radius: 16px;
      font-weight: 700;
      font-size: 1.1rem;
      border: none;
      transition: all 0.3s;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .btn-hero-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
    }

    .btn-hero-secondary {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      color: white;
      padding: 1rem 2.5rem;
      border-radius: 16px;
      font-weight: 600;
      font-size: 1.1rem;
      border: 2px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s;
    }

    .btn-hero-secondary:hover {
      background: rgba(255, 255, 255, 0.3);
      border-color: white;
    }

    /* STATS */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 2rem;
      margin-top: 5rem;
    }

    .stat-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 2rem 1.5rem;
      border-radius: 20px;
      text-align: center;
      transition: all 0.3s;
    }

    .stat-card:hover {
      background: rgba(255, 255, 255, 0.25);
      transform: translateY(-5px);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      color: white;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
      font-size: 1rem;
    }

    /* FEATURES SECTION */
    .section {
      padding: 100px 0;
    }

    .section-title {
      font-size: 2.5rem;
      font-weight: 800;
      text-align: center;
      margin-bottom: 1rem;
    }

    .section-subtitle {
      text-align: center;
      color: var(--gray);
      font-size: 1.2rem;
      margin-bottom: 4rem;
    }

    .feature-card {
      background: white;
      border-radius: 24px;
      padding: 2.5rem;
      border: 1px solid var(--border);
      transition: all 0.4s;
      height: 100%;
    }

    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
      border-color: var(--primary);
    }

    .feature-icon {
      width: 70px;
      height: 70px;
      border-radius: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .feature-card h5 {
      font-size: 1.3rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .feature-card p {
      color: var(--gray);
      line-height: 1.7;
    }

    /* NOTES SECTION */
    .notes-section {
      background: white;
    }

    .note-card {
      background: white;
      border-radius: 20px;
      border: 1px solid var(--border);
      transition: all 0.3s;
      height: 100%;
      position: relative;
      overflow: hidden;
    }

    .note-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      transform: scaleX(0);
      transition: transform 0.3s;
    }

    .note-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
      border-color: var(--primary);
    }

    .note-card:hover::before {
      transform: scaleX(1);
    }

    .note-card-inner {
      padding: 2rem;
    }

    .note-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }

    .note-icon-wrapper {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
    }

    .note-views {
      background: rgba(99, 102, 241, 0.1);
      color: var(--primary);
      padding: 0.4rem 0.9rem;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.3rem;
    }

    .category-badge {
      display: inline-block;
      background: var(--warning);
      color: white;
      padding: 0.35rem 0.9rem;
      border-radius: 8px;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 1rem;
    }

    .note-title {
      font-size: 1.25rem;
      font-weight: 700;
      margin-bottom: 0.8rem;
      color: var(--dark);
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .note-description {
      color: var(--gray);
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
      line-height: 1.6;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .note-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 1.2rem;
      border-top: 1px solid var(--border);
      margin-bottom: 1.2rem;
    }

    .note-author-section {
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .author-avatar {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 0.85rem;
    }

    .author-info {
      display: flex;
      flex-direction: column;
    }

    .note-author {
      font-weight: 600;
      color: var(--dark);
      font-size: 0.85rem;
      line-height: 1.2;
    }

    .note-date {
      font-size: 0.75rem;
      color: var(--gray);
    }

    .btn-preview {
      width: 100%;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 0.85rem;
      border-radius: 12px;
      border: none;
      font-weight: 600;
      font-size: 0.95rem;
      transition: all 0.3s;
      text-decoration: none;
      display: block;
      text-align: center;
    }

    .btn-preview:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
      color: white;
    }

    /* CTA SECTION */
    .cta-section {
      background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
      padding: 100px 0;
      position: relative;
      overflow: hidden;
    }

    .cta-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .cta-content {
      position: relative;
      z-index: 1;
    }

    .cta-section h2 {
      color: white;
      font-size: 3rem;
      font-weight: 800;
      margin-bottom: 1.5rem;
    }

    .cta-section p {
      color: rgba(255, 255, 255, 0.8);
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn-cta-primary {
      background: white;
      color: var(--primary);
      padding: 1rem 2.5rem;
      border-radius: 14px;
      font-weight: 700;
      border: none;
      transition: all 0.3s;
      font-size: 1.1rem;
    }

    .btn-cta-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(255, 255, 255, 0.3);
      border: 1px solid white;
      color: #fff
    }

    .btn-cta-secondary {
      background: transparent;
      color: white;
      padding: 1rem 2.5rem;
      border-radius: 14px;
      font-weight: 600;
      border: 2px solid white;
      transition: all 0.3s;
      font-size: 1.1rem;
    }

    .btn-cta-secondary:hover {
      background: white;
      color: var(--dark);
    }

    /* FOOTER */
    footer {
      background: var(--dark);
      color: #94a3b8;
      padding: 80px 0 40px;
    }

    footer h5 {
      color: white;
      font-weight: 700;
      margin-bottom: 1.5rem;
      font-size: 1.1rem;
    }

    footer p {
      margin-bottom: 0.8rem;
      cursor: pointer;
      transition: color 0.3s;
    }

    footer p:hover {
      color: white;
    }

    .footer-brand {
      font-size: 1.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 1rem;
    }

    .footer-copyright {
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      margin-top: 3rem;
      padding-top: 2rem;
      text-align: center;
      color: #64748b;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.2rem;
      }

      .hero .subtitle {
        font-size: 1.1rem;
      }

      .section-title {
        font-size: 2rem;
      }

      .cta-section h2 {
        font-size: 2rem;
      }

      .btn-hero-primary,
      .btn-hero-secondary,
      .btn-cta-primary,
      .btn-cta-secondary {
        padding: 0.8rem 1.5rem;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between w-100">

      <a class="navbar-brand">ONSP</a>

      <div class="d-flex gap-2 align-items-center">

        <?php if(isset($_SESSION['user_id'])): ?>

            <span class="fw-bold">
              👋 <?= htmlspecialchars($_SESSION['name']) ?>
            </span>

            <a href="auth/logout.php" class="btn btn-danger">
              Logout
            </a>

        <?php else: ?>

            <a href="auth/login.php" class="btn btn-login">Login</a>
            <a href="auth/student_signup.php" class="btn btn-tutor">Sign Up</a>

        <?php endif; ?>

      </div>

    </div>
  </div>
</nav>


<!-- HERO SECTION -->
<section class="hero">
  <div class="container">
    <div class="hero-content text-center">
      <h1>Your Learning Journey<br>Starts Here</h1>
      <p class="subtitle">Access premium study notes from verified tutors and ace your exams</p>

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-number">10K+</div>
          <div class="stat-label">Students</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">500+</div>
          <div class="stat-label">Notes</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">100+</div>
          <div class="stat-label">Tutors</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">50+</div>
          <div class="stat-label">Subjects</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURES SECTION -->
<section class="section">
  <div class="container">
    <h2 class="section-title">Why Choose ONSP?</h2>
    <p class="section-subtitle">Everything you need to excel in your studies</p>

    <div class="row g-4">
      <div class="col-lg-3 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">📚</div>
          <h5>Quality Notes</h5>
          <p>Access expertly crafted PDF notes from verified tutors across all subjects and topics.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">👨‍🏫</div>
          <h5>Expert Tutors</h5>
          <p>Learn from experienced educators with proven track records and excellent ratings.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">🔐</div>
          <h5>Secure Access</h5>
          <p>Your learning materials are always accessible, protected, and available 24/7.</p>
        </div>
      </div>

      <div class="col-lg-3 col-md-6">
        <div class="feature-card">
          <div class="feature-icon">⚡</div>
          <h5>Always Updated</h5>
          <p>Get the latest notes and study materials as they become available in real-time.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURED NOTES SECTION -->
<section class="section notes-section" id="notes">
  <div class="container">
    <h2 class="section-title">Featured Study Notes</h2>
    <p class="section-subtitle">Browse our most popular and recently added notes</p>

    <div class="row g-4">
      <?php foreach($notes as $n): ?>
      <div class="col-lg-4 col-md-6">
        <div class="note-card">
          <div class="note-card-inner">
            
            <div class="note-top">
              <div class="note-icon-wrapper">📄</div>
              <div class="note-views">
                👁 <?= $n['view_count'] ?? 0 ?>
              </div>
            </div>

            <span class="category-badge">
              <?= htmlspecialchars($n['category']) ?>
            </span>

            <?php
$ratings = array_column($n['note_feedback'] ?? [], 'rating');
$avgRating = count($ratings)
    ? round(array_sum($ratings)/count($ratings),1)
    : 0;
?>

<div style="font-weight:600;color:#f59e0b">
⭐ <?= $avgRating ?>/5
</div>

            

            <h5 class="note-title">
              <?= htmlspecialchars($n['title']) ?>
            </h5>

            <p class="note-description">
              <?= htmlspecialchars(substr($n['description'], 0, 120)) ?>...
            </p>

          <div class="note-footer">
  <div class="note-author-section">

    <div class="author-avatar">
      <?= strtoupper(substr($n['profiles']['name'] ?? 'T', 0, 1)) ?>
    </div>

    <div class="author-info">
      <div class="note-author">
        <?= htmlspecialchars($n['profiles']['name'] ?? 'Unknown Tutor') ?>
      </div>

      <div class="note-date">
        <?= date('M d, Y', strtotime($n['created_at'])) ?>
      </div>
    </div>

  </div>
</div>


            <a href="preview.php?id=<?= $n['id'] ?>" class="btn btn-preview">
              View Preview →
            </a>

            <div class="d-flex gap-2">
            <button class="btn btn-outline-primary w-100 mt-2"
              onclick="openReviewModal('<?= $n['id'] ?>')">
          ⭐ Add Review
            </button>

            <button class="btn btn-light w-100 mt-2 border border-dark"
                    onclick="loadReviews('<?= $n['id'] ?>')">
              💬 View Reviews
            </button>
</div>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA SECTION -->
<section class="cta-section">
  <div class="container cta-content text-center">
    <h2>Ready to Start Learning?</h2>
    <p>
      Join thousands of students already using ONSP to ace their studies.<br>
      Get instant access to premium study materials today.
    </p>

    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="#" class="btn btn-cta-primary">Create Free Account</a>
      <a href="#notes" class="btn btn-cta-secondary">Browse Notes</a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="footer-brand">ONSP</div>
        <p>Premium study notes platform connecting students with expert tutors worldwide.</p>
      </div>

      <div class="col-md-4">
        <h5>Quick Links</h5>
        <p>Browse Notes</p>
        <p>Become a Tutor</p>
        <p>Login</p>
        <p>Sign Up</p>
      </div>

      <div class="col-md-4">
        <h5>Contact</h5>
        <p>Email: support@onsp.com</p>
        <p>Help Center</p>
        <p>Terms of Service</p>
        <p>Privacy Policy</p>
      </div>
    </div>

    <div class="footer-copyright">
      © 2026 ONSP. All rights reserved.
    </div>
  </div>
</footer>

<!-- REVIEW MODAL -->
<div class="modal fade" id="reviewModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Add Review</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="review_note_id">

        <label>Rating</label>
        <select id="rating" class="form-control mb-3">
          <option value="5">★★★★★</option>
          <option value="4">★★★★</option>
          <option value="3">★★★</option>
          <option value="2">★★</option>
          <option value="1">★</option>
        </select>

        <label>Feedback</label>
        <textarea id="feedback" class="form-control"></textarea>

        <button class="btn btn-primary w-100 mt-3" onclick="submitReview()">
          Submit Review
        </button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="reviewsListModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Student Reviews</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="reviewsContainer">
        Loading reviews...
      </div>
    </div>
  </div>
</div>


<script>

function openReviewModal(noteId){
    document.getElementById('review_note_id').value = noteId;
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
}

function submitReview(){

    const noteId = document.getElementById('review_note_id').value;
    const rating = document.getElementById('rating').value;
    const feedback = document.getElementById('feedback').value;

    fetch('submit_review.php', {
        method:'POST',
        headers:{ 'Content-Type':'application/json' },
        body: JSON.stringify({
            note_id: noteId,
            rating: rating,
            feedback: feedback
        })
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            alert('Review added!');
            location.reload();
        }else{
            alert('Failed to submit review');
        }
    });
}

function loadReviews(noteId){

    fetch('get_reviews.php?note_id=' + noteId)
    .then(res => res.json())
    .then(data => {

        let html = '';

        if(!Array.isArray(data) || data.length === 0){
            html = '<p>No reviews yet</p>';
        } else {

            data.forEach(r => {
                html += `
                    <div class="border-bottom pb-3 mb-3">
                        <b>${r.student_name ?? 'Student'}</b>
                        <div style="color:#f59e0b;font-weight:600">
                            ⭐ ${r.rating}/5
                        </div>
                        <p class="mb-1">${r.feedback ?? ''}</p>
                        <small class="text-muted">
                            ${new Date(r.created_at).toLocaleDateString()}
                        </small>
                    </div>
                `;
            });

        }

        document.getElementById('reviewsContainer').innerHTML = html;

        new bootstrap.Modal(document.getElementById('reviewsListModal')).show();

    })
    .catch(err => {
        console.log(err);
        alert("Error loading reviews");
    });
}


</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
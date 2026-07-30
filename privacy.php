<?php
// Privacy Policy Page in PHP
session_start();
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en-NG">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy | Ankabit Farm Nigeria</title>
  <meta name="description" content="Privacy Policy for Ankabit Farm. NDPR and data protection compliant lead capture for wholesale egg supply in Nigeria.">
  <link rel="canonical" href="https://ankabitfarm.com.ng/privacy">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="bg-[#F5F7FA] text-slate-900 antialiased">
  <header class="bg-white border-b border-slate-200 py-6">
    <div class="max-w-4xl mx-auto px-4 flex items-center justify-between">
      <a href="/" class="text-2xl font-bold font-serif-heading text-slate-900">AnkabitFarm</a>
      <a href="/" class="btn-pill-dark text-xs uppercase">Back to Home</a>
    </div>
  </header>
  <main class="max-w-4xl mx-auto px-4 py-12 space-y-6">
    <h1 class="text-4xl font-normal font-serif-heading text-slate-900">Privacy Policy</h1>
    <p class="text-sm text-slate-600">Effective Date: <?php echo date('F j, Y'); ?> | NDPR & Data Protection Compliant</p>
    <div class="p-8 bg-white border border-slate-200 space-y-4 text-sm leading-relaxed text-slate-800">
      <h2 class="text-xl font-bold text-slate-900">1. Data Transparency & Lead Capture</h2>
      <p>At <strong>Ankabit Farm</strong>, protecting your privacy is paramount. When submitting wholesale egg supply inquiries, our system records your Step 1 contact information (Full Name, Email, Phone Number) immediately to ensure our sales desk can contact you even if your connection drops.</p>

      <h2 class="text-xl font-bold text-slate-900">2. How Data Is Used</h2>
      <p>Data is used exclusively to contact you regarding wholesale egg price quotes, dispatch truck scheduling across Nigerian states, and preventing automated bot spam. We do NOT sell or trade your contact details.</p>

      <h2 class="text-xl font-bold text-slate-900">3. Your Rights under NDPR</h2>
      <p>You have the right to request access, correction, or deletion of your saved contact records by emailing <strong><?php echo ADMIN_EMAIL; ?></strong>.</p>
    </div>
  </main>
  <footer class="bg-[#050914] text-white py-6 text-center text-xs text-slate-400">
    <p>© <?php echo date('Y'); ?> Ankabit Farm Nigeria. All rights reserved.</p>
  </footer>
</body>
</html>

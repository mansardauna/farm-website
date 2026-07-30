<?php
// Terms & Conditions Page in PHP
session_start();
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en-NG">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms & Conditions | Ankabit Farm Nigeria</title>
  <meta name="description" content="Commercial Terms and Conditions for B2B wholesale egg supply in Nigeria. Minimum order 100 crates.">
  <link rel="canonical" href="https://ankabitfarm.com.ng/terms">
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
    <h1 class="text-4xl font-normal font-serif-heading text-slate-900">Terms & Conditions</h1>
    <p class="text-sm text-slate-600">Commercial Wholesale Agreement | Ankabit Farm Nigeria</p>
    <div class="p-8 bg-white border border-slate-200 space-y-4 text-sm leading-relaxed text-slate-800">
      <h2 class="text-xl font-bold text-slate-900">1. Minimum Order Quantity (100 Crates Enforced)</h2>
      <p>Ankabit Farm operates strictly as a commercial B2B egg supplier. The minimum allowable order size per transaction is <strong>100 crates</strong> (3,000 fresh eggs). Orders below 100 crates will not be processed under wholesale pricing.</p>

      <h2 class="text-xl font-bold text-slate-900">2. Pricing & Delivery</h2>
      <p>Prices are quoted in Nigerian Naira (NGN) based on daily farm-gate rates. Waybills are issued upon dispatch. Buyers inspect crates at offloading alongside our transport driver. Verified transit breakage exceeding 1% will be credited or replaced.</p>

      <h2 class="text-xl font-bold text-slate-900">3. Governing Law</h2>
      <p>These terms are governed by the commercial laws of the Federal Republic of Nigeria.</p>
    </div>
  </main>
  <footer class="bg-[#050914] text-white py-6 text-center text-xs text-slate-400">
    <p>© <?php echo date('Y'); ?> Ankabit Farm Nigeria. All rights reserved.</p>
  </footer>
</body>
</html>

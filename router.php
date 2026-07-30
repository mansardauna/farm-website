<?php
// PHP Built-in Server Router Script for Ankabit Farm (Pure HTML Frontend + processor.php Backend)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Determine file path
$filePath = __DIR__ . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false; // let PHP serve static asset directly with correct MIME type
}

// Check inside public/ directory if asset is requested
$publicPath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    $mime = mime_content_type($publicPath);
    if (str_ends_with($uri, '.css')) $mime = 'text/css';
    if (str_ends_with($uri, '.js')) $mime = 'application/javascript';
    header("Content-Type: $mime");
    readfile($publicPath);
    exit;
}

// Route API Requests to single processor.php
if (str_starts_with($uri, '/processor.php') || str_starts_with($uri, '/api/')) {
    require __DIR__ . '/processor.php';
    exit;
}

// Clean URL Routing to Pure HTML Pages
if ($uri === '/' || $uri === '/index' || $uri === '/index.html' || $uri === '/index.php') {
    readfile(__DIR__ . '/index.html');
    exit;
}

if ($uri === '/order' || $uri === '/order.html' || $uri === '/order.php') {
    readfile(__DIR__ . '/order.html');
    exit;
}

if ($uri === '/privacy' || $uri === '/privacy.html' || $uri === '/privacy.php') {
    readfile(__DIR__ . '/privacy.html');
    exit;
}

if ($uri === '/terms' || $uri === '/terms.html' || $uri === '/terms.php') {
    readfile(__DIR__ . '/terms.html');
    exit;
}

// Fallback to index.html
readfile(__DIR__ . '/index.html');
?>

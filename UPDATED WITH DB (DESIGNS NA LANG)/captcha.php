<?php
// captcha.php
// Gumagawa ng random na captcha code at i-output bilang SVG image.
// Ang code ay nakaimbak sa session para sa verification sa login/register.

session_start();

header('Content-Type: image/svg+xml');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// ── GENERATE RANDOM CODE ────────────────────────────────────────────────
$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // walang 0/O/1/I para iwas confusion
$length = 6;
$code = '';
for ($i = 0; $i < $length; $i++) {
    $code .= $chars[random_int(0, strlen($chars) - 1)];
}

// ── SAVE TO SESSION ──────────────────────────────────────────────────────
$_SESSION['captcha_code'] = $code;
$_SESSION['captcha_time'] = time();

// ── BUILD SVG ─────────────────────────────────────────────────────────────
$width  = 160;
$height = 56;

$bgColors    = ['#f5f9fe', '#eef2f7', '#fef7e8', '#eef5ee'];
$bgColor     = $bgColors[array_rand($bgColors)];
$lineColors  = ['#b5d4f4', '#cbdcd5', '#f4b942', '#9eb8ae'];
$textColors  = ['#0c447c', '#1a4d2e', '#5b21b6', '#804d00'];

$svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'">';
$svg .= '<rect width="100%" height="100%" rx="12" fill="'.$bgColor.'"/>';

// noise lines
for ($i = 0; $i < 5; $i++) {
    $x1 = random_int(0, $width);
    $y1 = random_int(0, $height);
    $x2 = random_int(0, $width);
    $y2 = random_int(0, $height);
    $color = $lineColors[array_rand($lineColors)];
    $svg .= '<line x1="'.$x1.'" y1="'.$y1.'" x2="'.$x2.'" y2="'.$y2.'" stroke="'.$color.'" stroke-width="1.5" opacity="0.6"/>';
}

// noise dots
for ($i = 0; $i < 25; $i++) {
    $cx = random_int(0, $width);
    $cy = random_int(0, $height);
    $r  = random_int(1, 2);
    $color = $lineColors[array_rand($lineColors)];
    $svg .= '<circle cx="'.$cx.'" cy="'.$cy.'" r="'.$r.'" fill="'.$color.'" opacity="0.5"/>';
}

// characters, each with slight rotation/offset
$spacing = $width / ($length + 1);
for ($i = 0; $i < $length; $i++) {
    $char  = htmlspecialchars($code[$i]);
    $x     = $spacing * ($i + 1);
    $y     = $height / 2 + random_int(-6, 6);
    $rot   = random_int(-20, 20);
    $size  = random_int(24, 30);
    $color = $textColors[array_rand($textColors)];

    $svg .= '<text x="'.$x.'" y="'.$y.'" font-size="'.$size.'" font-weight="700" '
          . 'font-family="Segoe UI, Roboto, sans-serif" fill="'.$color.'" '
          . 'text-anchor="middle" dominant-baseline="middle" '
          . 'transform="rotate('.$rot.' '.$x.' '.$y.')">'.$char.'</text>';
}

$svg .= '</svg>';

echo $svg;
?>
<?php
/**
 * Script to create placeholder image
 * Run this once: http://localhost/toko-online/create-placeholder.php
 */

$width = 800;
$height = 600;

// Create image
$image = imagecreatetruecolor($width, $height);

// Colors
$bgColor = imagecolorallocate($image, 230, 230, 230);
$textColor = imagecolorallocate($image, 100, 100, 100);
$borderColor = imagecolorallocate($image, 180, 180, 180);

// Fill background
imagefill($image, 0, 0, $bgColor);

// Draw border
imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);

// Add text
$text = "No Image Available";
$fontSize = 5;

// Calculate text position (center)
$textWidth = imagefontwidth($fontSize) * strlen($text);
$textHeight = imagefontheight($fontSize);
$x = ($width - $textWidth) / 2;
$y = ($height - $textHeight) / 2;

imagestring($image, $fontSize, $x, $y, $text, $textColor);

// Add dimensions text
$dimText = $width . " x " . $height;
$dimX = ($width - imagefontwidth($fontSize) * strlen($dimText)) / 2;
$dimY = $y + 20;
imagestring($image, $fontSize, $dimX, $dimY, $dimText, $textColor);

// Save image
$filePath = __DIR__ . '/public/images/placeholder.jpg';

// Create directory if not exists
$dir = dirname($filePath);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Save the image
if (imagejpeg($image, $filePath, 90)) {
    echo "✅ Placeholder image created successfully!<br>";
    echo "Location: " . $filePath . "<br>";
    echo '<img src="public/images/placeholder.jpg" style="max-width: 400px; border: 1px solid #ccc;">';
} else {
    echo "❌ Failed to create placeholder image.";
}

// Free memory
imagedestroy($image);
?>

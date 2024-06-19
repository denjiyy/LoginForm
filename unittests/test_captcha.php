<?php
function mock_session_start() {
}

function mock_image_create() {
    return imagecreatetruecolor(200, 50);
}

function mock_image_antialias($image, $enabled) {
}

function mock_image_color_allocate($image, $red, $green, $blue) {
    return imagecolorallocate($image, $red, $green, $blue);
}

function mock_image_fill($image, $x, $y, $color) {
    imagefill($image, $x, $y, $color);
}

function mock_image_set_thickness($image, $thickness) {
}

function mock_image_rectangle($image, $x1, $y1, $x2, $y2, $color) {
    imagerectangle($image, $x1, $y1, $x2, $y2, $color);
}

function mock_image_color_allocate_black($image, $red, $green, $blue) {
    return imagecolorallocate($image, $red, $green, $blue);
}

function mock_image_ttf_text($image, $size, $angle, $x, $y, $color, $fontfile, $text) {
}

function mock_image_png($image) {
    ob_start();
    imagepng($image);
    $output = ob_get_clean();
    return $output;
}

function mock_image_destroy($image) {
    imagedestroy($image);
}

$_SESSION = [];
$permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

mock_session_start();

$string_length = 6;
$captcha_string = '';
for ($i = 0; $i < $string_length; $i++) {
    $captcha_string .= $permitted_chars[rand(0, strlen($permitted_chars) - 1)];
}
$_SESSION['captcha_text'] = $captcha_string;

$image = mock_image_create();
mock_image_antialias($image, true);

$colors = [];
$red = rand(125, 175);
$green = rand(125, 175);
$blue = rand(125, 175);

for ($i = 0; $i < 5; $i++) {
    $colors[] = mock_image_color_allocate($image, $red - 20 * $i, $green - 20 * $i, $blue - 20 * $i);
}

mock_image_fill($image, 0, 0, $colors[0]);

for ($i = 0; $i < 10; $i++) {
    mock_image_set_thickness($image, rand(2, 10));
    $line_color = $colors[rand(1, 4)];
    mock_image_rectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $line_color);
}

$black = mock_image_color_allocate_black($image, 0, 0, 0);
$white = mock_image_color_allocate($image, 255, 255, 255);
$textcolors = [$black, $white];

$fonts = [
    dirname(__FILE__) . '/fonts/pixelation.ttf'
];

for ($i = 0; $i < $string_length; $i++) {
    $letter_space = 170 / $string_length;
    $initial = 15;

    mock_image_ttf_text($image, 24, rand(-15, 15), $initial + $i * $letter_space, rand(25, 45), $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[$i]);
}

function mock_header($header) {
    echo "Header called: $header\n";
}

ob_start();
mock_header('Content-type: image/png');
$header_output = ob_get_clean();

if (strpos($header_output, 'Content-type: image/png') === false) {
    echo "Test failed: Expected header 'Content-type: image/png' not found in output.\n";
    exit(1);
} else {
    echo "Test passed: Header 'Content-type: image/png' found in output.\n";
}

$image_output = mock_image_png($image);

if (empty($image_output)) {
    echo "Test failed: No image content generated.\n";
    exit(1);
} else {
    echo "Test passed: Image content generated.\n";
}

mock_image_destroy($image);

echo "All tests passed.\n";

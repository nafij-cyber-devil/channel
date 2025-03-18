<?php
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = $_GET['id'];
$streamUrl = "https://its-ferdos-alom.top/fredflix.fun/ayna/$id/playlist.m3u8";

// cURL setup to get the redirect URL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $streamUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, true); // Only get headers
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
curl_setopt($ch, CURLOPT_REFERER, "https://its-ferdos-alom.top/");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Origin: https://its-ferdos-alom.top/fredflix.fun/ayna/"
]);

curl_exec($ch);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

// Redirect to the final URL
if ($finalUrl) {
    header("Location: $finalUrl");
    exit();
} else {
    die("Failed to fetch the stream.");
}
?>
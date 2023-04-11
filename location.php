<?php
header('Content-Type: application/json');

$GOOGLE_API_KEY = "AIzaSyAM7RJE652x9vPDzyxQ33eFRf58xTdJVUY";
$lat = $_GET['lat'];
$lng = $_GET['lng'];

$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${GOOGLE_API_KEY}";

$response = file_get_contents($url);
echo $response;
?>
<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$fromLat = floatval($_GET["from_lat"]);
$fromLon = floatval($_GET["from_lon"]);
$from = $fromLat . "," . $fromLon;

$toLat = floatval($_GET["to_lat"]);
$toLon = floatval($_GET["to_lon"]);
$to = $toLat . "," . $toLon;

$latitudeFirst = $_GET["order"] == "lat,lon";

$url = "https://signal.eu.org/osm/eu/route/v1/train/" . $from . ";" . $to . "?overview=false&alternatives=true&steps=true";

$route = json_decode(file_get_contents($url));

$steps = array();
$steps[] = $latitudeFirst ? array($fromLat, $fromLon) : array($fromLon, $fromLat);
foreach($route->routes[0]->legs[0]->steps as $step){
    foreach($step->intersections as $intersection){
        $steps[] = $latitudeFirst ? array($intersection->location[1], $intersection->location[0]) : $intersection->location;
    }
}
$steps[] = $latitudeFirst ? array($toLat, $toLon) : array($toLon, $toLat);

echo json_encode($steps);

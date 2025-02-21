<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$from = $_GET["from"];
$to = $_GET["to"];
$url = "https://signal.eu.org/osm/eu/route/v1/train/" . $from . ";" . $_GET["to"] . "?overview=false&alternatives=true&steps=true";

$route = json_decode(file_get_contents($url));

$steps = array();
$steps[] = explode(",", $from);
foreach($route->routes[0]->legs[0]->steps as $step){
    foreach($step->intersections as $intersection){
        $steps[] = $intersection->location;
    }
}
$steps[] = explode(",", $to);

echo json_encode($steps);

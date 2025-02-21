<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$url = "https://signal.eu.org/osm/eu/route/v1/train/13.398937,52.510885;12.482932,41.89332?overview=false&alternatives=true&steps=true";

$route = json_decode(file_get_contents($url));

$steps = array();
foreach($route->routes[0]->legs[0]->steps as $step){
    foreach($step->intersections as $intersection){
        $steps[] = $intersection->location;
    }
}

print_r($steps);
?>


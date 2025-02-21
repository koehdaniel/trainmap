<?php
$db_name = $db_user = $db_pass = $db_name = "";
include_once ".env.php";

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

function searchForName($table, $name, $suggestable=1, $limit=20){
    global $conn;

    $sql = "";
    if($table == "stations"){
        $sql = "SELECT id, CONCAT(name, ' (', country, ')') as name, longitude, latitude FROM `$table` WHERE `name` LIKE ? AND is_suggestable=$suggestable LIMIT $limit";
    }
    elseif($table == "cities"){
        $sql = "SELECT id, CONCAT(name, ' (', country_code, ')') as name, longitude, latitude FROM `$table` WHERE `name` LIKE ? LIMIT $limit";
    }

    logging($sql . " => " . $name);

    ;
    $sql = $conn->prepare($sql);
    $sql->bind_param("s", $name);
    $sql->execute();
    return $sql->get_result();
}
function addToArray(&$array, $dbResult){
    while ($row = $dbResult->fetch_object()) {
        $array[] = $row;
    }
}
function logging($msg) {
        $stderr = fopen('php://stderr', 'w');
        fwrite($stderr,$msg);
        fclose($stderr);
}

// Create connection
$conn = new mysqli($db_name, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchName = $_GET["name"];
$city = "";
$country = "";
$withCountry = false;

if(str_contains($searchName, "(")){
    logging("searchWithCity");
    $withCountry = true;
    $cityAndCountry = explode("(", $searchName);
    $city = trim($cityAndCountry[0]);
    $country = trim(str_replace(")", "", $cityAndCountry[1]));
}

$search = trim($city) . "%";

if($search[0] == "*"){
    $search[0] = "%";
}

$results = array();
addToArray($results, searchForName("stations", $search, 1));

//Not enough results, try wildcard search if not yet done
if(count($results) < 2 && $search[0] != "%"){
    logging("searchForStation");
    $search = "%$search";
    addToArray($results, searchForName("stations", $search, 0));
}

//Not enough results, try city search
if(count($results) < 2 || $withCountry){
    logging("searchForCity");
    addToArray($results, searchForName("cities", $search));
}

echo json_encode($results);

// Close connection
$conn->close();



?>

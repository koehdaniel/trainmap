<?php
$db_name = $db_user = $db_pass = $db_name = "";
include_once ".env.php";

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

function searchForName($table, $name, $suggestable=NULL, $limit=20){
    global $conn;

    $suggest = "";
    if(is_numeric($suggestable)){
        $suggest = " AND is_suggestable=$suggestable";
    }

    $sql = "SELECT id, name, longitude, latitude FROM `$table` WHERE `name` LIKE ? $suggest LIMIT $limit";
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

// Create connection
$conn = new mysqli($db_name, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchName = $_GET["name"];
$search = $searchName . "%";

if($search[0] == "*"){
    $search[0] = "%";
}

$results = array();
addToArray($results, searchForName("stations", $search, 1));

//Not enough results, try wildcard search if not yet done
if(count($results) < 2 && $search[0] != "%"){
    $search = "%$search";
    addToArray($results, searchForName("stations", $search, 0));
}

//Not enough results, try city search with wildcard since already enforced anyway
if(count($results) < 2){
    addToArray($results, searchForName("cities", $search));
}

echo json_encode($results);

// Close connection
$conn->close();



?>

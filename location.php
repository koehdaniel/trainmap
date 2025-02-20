<?php
include_once ".env.php";

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// Create connection
$conn = new mysqli($db_name, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = "%" . $_GET["name"] . "%";

// Sample query
$sql = $conn->prepare("SELECT * FROM `stations` WHERE `name` LIKE ?");
$sql->bind_param("s", $search);
$sql->execute();
$result = $sql->get_result();

$results = array();
while ($row = $result->fetch_object()) {
    $results[] = $row;
}
echo json_encode($results);

// Close connection
$conn->close();

?>

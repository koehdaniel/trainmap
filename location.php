<?php
use .env.php;
// Create connection
$conn = new mysqli($db_name, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = "%" . $_GET["name"] . "%";

// Sample query
$sql = $conn->prepare("SELECT id, name FROM `stations` WHERE `name` LIKE ?");
$sql->bind_param("s", $search);
$sql->execute();
$result = $sql->get_result();

while ($row = $result->fetch_object()) {
    echo "ID: " . $row->id . " - Name: " . $row->name . "<br>";
}

// Close connection
$conn->close();

?>

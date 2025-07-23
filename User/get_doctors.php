<?php
include_once("../Database/connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['department'])) {
    $department = $_POST['department'];

    $stmt = $con->prepare("SELECT id, username, consultancy_fees FROM doctors WHERE departmentname = ?");
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = [
            "id" => $row['id'],
            "name" => $row['username'],
            "fees" => $row['consultancy_fees']
        ];
    }

    if (empty($doctors)) {
        echo json_encode(["error" => "No doctors found"]);
    } else {
        echo json_encode($doctors);
    }
}
?>

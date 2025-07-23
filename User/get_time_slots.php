<?php
include_once("../Database/connection.php");

if(isset($_POST['doctor_id'])) {
    $doctor_id = $_POST['doctor_id'];

    $stmt = $con->prepare("SELECT time_slot FROM doctor_time_slots WHERE doctor_id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="" disabled selected>Select Time Slot</option>';

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<option value='".$row['time_slot']."'>".$row['time_slot']."</option>";
        }
    } else {
        echo "<option value=''>No available slots</option>";
    }
}
?>

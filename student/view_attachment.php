<?php
    ob_start();
    session_start();
    if($_SESSION['role']!='student') header('location: ../index.php');
    include ('../database/LeaveRepository.php');

    $leave_repository = new LeaveRepository();
    if(isset($_GET['leave_id'])) {
        $row = $leave_repository->findByIdAndStudentId($_GET['leave_id'], $_SESSION['user_id']);
        if ($row != null) {
            header("Content-type: " . $row["attachment_type"]);
            echo base64_decode($row["attachment_data"]);
        }
        else {
            echo "Either Leave ID is incorrect or Not authorised to view";
        }
	}
?>
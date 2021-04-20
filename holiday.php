<?php
    ob_start();
    session_start();
    if($_SESSION['role']=='') header('location: index.php');
	include ('database/HolidayRepository.php');
	include ('utils.php');

	$holiday_repository = new HolidayRepository();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Holiday Calendar</title>
</head>
<body>
    <button type="button" onclick="location.href='<?php echo $_SESSION['role']; ?>/index.php';">Back</button>
    <h3> Academic Holiday Calendar <?php echo date("Y"); ?></h3>
    <table>
        <thead><tr><th>Date</th><th>Title</th></tr></thead>
        <tbody>
        <?php
            foreach ($holiday_repository->getAll() as $holiday) {
                echo "<tr><td>";
                echo $holiday['date'];
                echo "</td><td>";
                echo $holiday['title'];
                echo "</td></tr>";
            }
        ?>
    </div>
</body>
</html>

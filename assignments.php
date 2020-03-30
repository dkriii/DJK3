<?php 

session_start();
if(!isset($_SESSION["person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');   // go to login page
	exit;
}
$id = $_GET['id']; // for MyAssignments
$sessionid = $_SESSION['person_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
	
		<div class="row">
			<h3><?php if($id) echo 'My'; ?>Shifts</h3>
		</div>
		
		<div class="row">
			<p>Each shift is 4 hours.</p>
			<p>
				<?php if($_SESSION['person_title']=='Administrator')
					echo '<a href="assign_create.php" class="btn btn-primary">Add Assignment</a>';
				?>
				<a href="logout.php" class="btn btn-warning">Logout</a> &nbsp;&nbsp;&nbsp;
				<?php if($_SESSION['person_title']=='Administrator')
					echo '<a href="persons.php">Volunteers</a> &nbsp;';
				?>
				<a href="events.php">Shifts</a> &nbsp;
				<?php if($_SESSION['person_title']=='Administrator')
					echo '<a href="assignments.php">AllShifts</a>&nbsp;';
				?>
				<a href="assignments.php?id=<?php echo $sessionid; ?>">MyShifts</a>&nbsp;
				<?php if($_SESSION['person_title']=='Volunteer')
					echo '<a href="assign_create.php" class="btn btn-primary">Volunteer</a>';
				?>
			</p>
			
			<table class="table table-striped table-bordered" style="background-color: lightgrey !important">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Location</th>
						<th>Event</th>
						<th>Volunteer</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					include 'database.php';
					include 'functions.php';
					$pdo = Database::connect();
					
					if($id) 
						$sql = "SELECT * FROM assignments 
						LEFT JOIN persons ON persons.id = assignments.assign_per_id 
						LEFT JOIN events ON events.id = assignments.assign_event_id
						WHERE persons.id = $id 
						ORDER BY event_date ASC, event_time ASC, lname ASC, lname ASC;";
					else
						$sql = "SELECT * FROM assignments 
						LEFT JOIN persons ON persons.id = assignments.assign_per_id 
						LEFT JOIN events ON events.id = assignments.assign_event_id
						ORDER BY event_date ASC, event_time ASC, lname ASC, lname ASC;";

					foreach ($pdo->query($sql) as $row) {
						echo '<tr>';
						echo '<td>'. functions::dayMonthDate($row['event_date']) . '</td>';
						echo '<td>'. functions::timeAmPm($row['event_time']) . '</td>';
						echo '<td>'. $row['event_location'] . '</td>';
						echo '<td>'. $row['event_description'] . '</td>';
						echo '<td>'. $row['lname'] . ', ' . $row['fname'] . '</td>';
						echo '<td width=250>';
						# use $row[0] because there are 3 fields called "id"
						echo '<a class="btn" href="assign_read.php?id='.$row[0].'">Details</a>';
						if ($_SESSION['person_title']=='Administrator' )
							echo '&nbsp;<a class="btn btn-success" href="assign_update.php?id='.$row[0].'">Update</a>';
						if ($_SESSION['person_title']=='Administrator' 
							|| $_SESSION['person_id']==$row['assign_per_id'])
							echo '&nbsp;<a class="btn btn-danger" href="assign_delete.php?id='.$row[0].'">Delete</a>';
						if($_SESSION["person_id"] == $row['assign_per_id']) 		echo " &nbsp;&nbsp;Me";
						echo '</td>';
						echo '</tr>';
					}
					Database::disconnect();
				?>
				</tbody>
			</table>
    	</div>

    </div> <!-- end div: class="container" -->
	
</body>
</html>
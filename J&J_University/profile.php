<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'dbhandler.php';
$db = new DatabaseHandler();
$message = "";

if (session_status() === PHP_SESSION_NONE) {
    session_start();}

if (!isset($_SESSION['email'])) {
    die("User not logged in.");}

$email = $_SESSION['email'];

$sql = "SELECT * FROM tblUsers WHERE email = '$email'";
$result = $db->executeSelectQuery($sql);

if ($result && count($result) === 1) {
    $user = $result[0];} 
else {
    die("User data not found.");}

$passwordMessage = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
	$currentPassword = $_POST['current_password'];
	$newPassword = $_POST['new_password'];
	$confirmPassword = $_POST['confirm_password'];
	$sql = "SELECT password FROM tblUsers WHERE email = '$email'";
	$result = $db->executeSelectQuery($sql);

	if ($result && count($result) === 1) {
		$storedPassword = $result[0]['password'];
		if (!password_verify($currentPassword, $storedPassword)) {
			$passwordMessage = "Current password is incorrect."; }
		elseif ($newPassword !== $confirmPassword) {
			$passwordMessage = "New passwords do not match."; }
		else {
			$hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
			$sql = "UPDATE tblUsers SET password = '$hashedNewPassword' WHERE email = '$email'";
			if ($db->executeQuery($sql)) {
				$passwordMessage = "Password changed successfully."; }
			else {
				$passwordMessage = "Password change unsuccessful."; }
		}
	}
}
?>


<div class="page-content">
	<div class="doublecontent-container">
		<div class="sidebar">
			<h2><?php
				if (isset($_SESSION['username'])) {
   					echo "Welcome, " . $_SESSION['username'];
			}?></h2>
			<a href="index.php?page=profile" class="active">Personal Information</a>
			<a href="index.php?page=schedule">Schedule</a>
		</div>

		<div class="doublecontent-content">
			<table class="table table-bordered">
				<tr><th>First Name</th><td><?= $user['firstName'] ?></td></tr>
				<tr><th>Last Name</th><td><?= $user['lastName'] ?></td></tr>
				<tr><th>Email</th><td><?= $user['email'] ?></td></tr>
				<tr><th>Address</th><td><?= $user['address'] ?></td></tr>
				<tr><th>City</th><td><?= $user['city'] ?></td></tr>
				<tr><th>State</th><td><?= $user['state'] ?></td></tr>
				<tr><th>ZIP</th><td><?= $user['zip'] ?></td></tr>
			</table>

			<div class="container">
				<h3>Change Password</h3>
				<?php if ($passwordMessage): ?>
					<div class="alert alert-info"><?= htmlspecialchars($passwordMessage) ?></div>
				<?php endif; ?>

				<form method="post" action="">
					<input type="hidden" name="change_password" value="1" />
					<div class="form-group">
						<label for="current_password">Current Password:</label>
						<input type="password" name="current_password" id="current_password" class="form-control" required />
					</div>
					<div class="form-group">
						<label for="new_password">New Password:</label>
						<input type="password" name="new_password" id="new_password" class="form-control" required />
					</div>
					<div class="form-group">
						<label for="confirm_password">Confirm New Password:</label>
						<input type="password" name="confirm_password" id="confirm_password" class="form-control" required />
					</div>
					<button type="submit" class="btn btn-primary">Update Password</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'dbhandler.php';
$db = new DatabaseHandler();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $db->con->real_escape_string($_POST['email']);
	$password = $_POST['password'];
	$verifyPassword = $_POST['verify_password'];
    
	if ($password !== $verifyPassword) {
		$message = "Passwords do not match."; }
	else {
        	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		$firstName = $db->con->real_escape_string($_POST['first_name']);
		$lastName = $db->con->real_escape_string($_POST['last_name']);
		$address = $db->con->real_escape_string($_POST['address']);
		$city = $db->con->real_escape_string($_POST['city']);
		$state = $db->con->real_escape_string($_POST['state']);
		$zip = $db->con->real_escape_string($_POST['zip']);

		$sql = "SELECT id FROM tblUsers WHERE email = '$email'";
		$verifyUniqueEmail = $db->executeSelectQuery($sql);

		if ($verifyUniqueEmail && count($verifyUniqueEmail) > 0) {
			$message = "Email is already registered. Please use a different email or proceed to Login."; }
		else {
			$sql = "INSERT INTO tblUsers (email, password, firstName, lastName, address, city, state, zip)
				VALUES ('$email', '$hashedPassword', '$firstName', '$lastName', '$address', '$city', '$state', '$zip')";
			$message = $db->executeQuery($sql) 
				? "Registration successful!" 
				: $db->con->error;
		}
	}
}
?>

<div class="page-content container">
    <h2>User Registration</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" name="password" required>
        </div>
	<div class="form-group">
	    <label for="verify_password">Verify Password:</label>
	    <input type="password" class="form-control" name="verify_password" required>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" class="form-control" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" class="form-control" name="last_name" required>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" class="form-control" name="address">
        </div>
        <div class="form-group">
            <label for="city">City:</label>
            <input type="text" class="form-control" name="city">
        </div>
        <div class="form-group">
            <label for="state">State:</label>
            <input type="text" class="form-control" name="state">
        </div>
        <div class="form-group">
            <label for="zip">Zip:</label>
            <input type="text" class="form-control" name="zip">
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

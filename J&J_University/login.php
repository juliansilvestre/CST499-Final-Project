<?php error_reporting(E_ALL ^ E_NOTICE);
require_once 'dbhandler.php';
if (session_status() === PHP_SESSION_NONE) {
	session_start(); }
$db = new DatabaseHandler();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $db->con->real_escape_string($_POST['email']);
	$password = $_POST['password'];
	$sql = "SELECT email, password, firstName, role, id FROM tblUsers WHERE email = '$email'";
	$login = $db->executeSelectQuery($sql);

	if ($login && count($login) === 1) {
		$user = $login[0];
		if (password_verify($password, $user['password'])) {
			$_SESSION['username'] = $user['firstName'];
			$_SESSION['email'] = $user['email'];
			$_SESSION['is_admin'] = ($user['role'] == 'admin');
			$_SESSION['role'] = $user['role'];
			$_SESSION['userID'] = $user['id'];
			header('Location: index.php?page=profile', true, 303);
			exit; }
		else {
			$message = "Invalid password."; }}
	else {
		$message = "No user exists with this email address."; }}
?>

<div class="page-content container">
	<h2>Login</h2>
	<?php if ($message): ?>
		<div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
	<?php endif;?>

	<form method="post" action="index.php?page=login">
		<div class="form-group">
			<label>Email:</label>
			<input type="email" name="email" class="form-control" required>
		</div>
		<div class="form-group">
			<label>Password:</label>
			<input type="password" name="password" class="form-control" required>
		</div>
		<button type="submit" class="btn btn-primary">Login</button>
	</form>
</div>
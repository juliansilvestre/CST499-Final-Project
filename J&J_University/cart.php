<?php
require_once 'dbhandler.php';
$db = new DatabaseHandler();
require_once 'autowaitlist.php';
$auto = new AutoWaitlist($db);
$studentID = (int)$_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['register'])) {
		$sqlCartCourses = "SELECT courseOffering FROM tblCart WHERE studentID = $studentID";
		$cartCourses = $db->executeSelectQuery($sqlCartCourses);

		if ($cartCourses && count($cartCourses) > 0) {
			$messages = [];

			foreach ($cartCourses as $course) {
 				$courseOfferingID = $course['courseOffering'];
				$result = $auto->enroll($courseOfferingID, $studentID);

				if ($result === false) {
					$courseRow = $db->executeSelectQuery("SELECT courseID FROM tblCourseOfferings WHERE id = $courseOfferingID");
					$courseName = $courseRow ? $courseRow[0]['courseID'] : $courseOfferingID;
					$messages[] = "Could not register or waitlist for course offering $courseName."; }
				else {
					$messages[] = $result;}}

			$message = implode("<br>", $messages);}
		else {
			$message = "Your cart is empty. Add courses first."; }
	}

	if (isset($_POST['remove'])) {
		$courseOffering = (int)$_POST['courseOffering'];
		$studentID = (int)$_SESSION['userID'];
		try {
			$sqlRemove = "DELETE FROM tblCart WHERE courseOffering = $courseOffering AND studentID = $studentID";
			if ($db->executeQuery($sqlRemove)) {
				$message = "Course removed from cart."; }
			else {
				$message = "Could not remove course from your cart. Error: " . $db->con->error; }}
		catch (mysqli_sql_exception $e) {
			$message = "Could not remove course from your cart. Error: " . $e->getMessage(); }
	}
}

$sql = "SELECT co.*, c.name AS courseName, cart.*
	FROM tblCart AS cart
	JOIN tblCourseOfferings AS co ON cart.courseOffering = co.id
	JOIN tblCourses AS c ON co.courseID = c.courseID
	WHERE cart.studentID = $studentID";
$cart = $db->executeSelectQuery($sql);
?>

<div class="page-content">
	<h2>Course Registration Cart</h2>
	<?php if (!empty($message)): ?>
		<div class="alert alert-success"><?= $message ?></div>
	<?php endif; ?>
	<table class ="table" style="width:100%; border-collapse: collapse;">
		<?php if ($cart && count($cart) > 0): ?>
		<?php foreach ($cart as $cartClass): ?>
			<tr style="background-color:#9F35AC; color:white; font-weight:bold;">
				<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($cartClass['courseID']) ?></th>
				<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($cartClass['sectionID']) ?></th>
				<th style="padding:4px; font-size:16px;" colspan="2"><?= htmlspecialchars($cartClass['courseName']) ?></th>
				<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($cartClass['term'] . ' ' . $cartClass['year']) ?></th>
			</tr>

			<tr>
				<td style="padding:4px;"><?= htmlspecialchars($cartClass['startDate']) ?></td>
				<td style="padding:4px;"><?= htmlspecialchars($cartClass['endDate']) ?></td>
				<td style="padding:4px;"><?= htmlspecialchars($cartClass['classDays']) ?></td>
				<td style="padding:4px;"><?= htmlspecialchars($cartClass['startTime']) ?></td>
				<td style="padding:4px;"><?= htmlspecialchars($cartClass['endTime']) ?></td>
			</tr>
			<tr>
				<td colspan="5" style="text-align:right;">
					<form method="POST" action="">
						<input type="hidden" name="courseOffering" value="<?= htmlspecialchars($cartClass['courseOffering']) ?>">
						<button type="submit" name="remove" style="background-color:#9F35AC; color:white; padding:6px 12px; border:none; cursor:pointer;">Remove from Cart</button>
					</form>
				</td>
			</tr>
			<tr><td colspan="5">&nbsp;</td></tr>
		<?php endforeach; ?>
		<?php else: ?>
			<tr><td>No courses found.</td></tr>
		<?php endif; ?>
	</table>
	<div style="text-align: right; margin: 20px">
		<form method="POST" action="">
			<button type="submit" name="register" style="background-color:#9F35AC; color:white; padding:6px 12px; border:none; cursor:pointer;">Register</button>
		</form>
	</div>
</div>





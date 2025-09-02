<?php
require_once 'dbhandler.php';
$db = new DatabaseHandler();
require_once 'autowaitlist.php';
$auto = new AutoWaitlist($db);
$studentID = (int)$_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['unregister'])) {
		$courseOfferingID = (int)$_POST['courseOffering'];
		$studentID = (int)$_SESSION['userID'];
		try {
			$sqlRemove = "DELETE FROM tblCourseRegistration WHERE courseOfferingID = $courseOfferingID AND userID = $studentID";
			if ($db->executeQuery($sqlRemove)) {
				$message = "Course removed from schedule.";
				$auto->enrollFromUnregister($courseOfferingID); }
			else {
				$message = "Could not remove course from your schedule. Error: " . $db->con->error; }}
		catch (mysqli_sql_exception $e) {
			$message = "Could not remove course from your schedule. Error: " . $e->getMessage(); }
	}
	if (isset($_POST['unwaitlist'])) {
		$courseOfferingID = (int)$_POST['courseOffering'];
		$studentID = (int)$_SESSION['userID'];
		try {
			$sqlRemove = "DELETE FROM tblCourseWaitlist WHERE courseOfferingID = $courseOfferingID AND userID = $studentID";
			if ($db->executeQuery($sqlRemove)) {
				$message = "You were removed from the waitlist."; }
			else {
				$message = "Could not remove you from the waitlist. Error: " . $db->con->error; }}
		catch (mysqli_sql_exception $e) {
			$message = "Could not remove you from the waitlist. Error: " . $e->getMessage(); }
	}

}

$sqlRegistered = "SELECT co.*, c.name AS courseName, cr.courseOfferingID, cr.userID
	FROM tblCourseRegistration AS cr
	JOIN tblCourseOfferings AS co ON cr.courseOfferingID = co.id
	JOIN tblCourses AS c ON co.courseID = c.courseID
	WHERE cr.userID = $studentID
	ORDER BY co.courseID ASC, co.sectionID ASC";

$sqlWaitlisted = "SELECT co.*, c.name AS courseName, cw.courseOfferingID, cw.userID
	FROM tblCourseWaitlist AS cw
	JOIN tblCourseOfferings AS co ON cw.courseOfferingID = co.id
	JOIN tblCourses AS c ON co.courseID = c.courseID
	WHERE cw.userID = $studentID
	ORDER BY co.courseID ASC, co.sectionID ASC";

$registeredClasses = $db->executeSelectQuery($sqlRegistered);
$waitlistClasses = $db->executeSelectQuery($sqlWaitlisted);
?>

<div class="page-content">
	<div class="doublecontent-container">
		<div class="sidebar">
			<h2><?php
				if (isset($_SESSION['username'])) {
   					echo "Welcome, " . $_SESSION['username'];
			}?></h2>
			<a href="index.php?page=profile">Personal Information</a>
			<a href="index.php?page=schedule" class="active">Schedule</a>
		</div>

		<div class="doublecontent-content">
			<div>
				<h2>Registered Courses</h2>
				<table class="table" style="width:100%; border-collapse: collapse;">
					<?php if ($registeredClasses && count($registeredClasses) > 0): ?>
					<?php foreach ($registeredClasses as $class): ?>
						<tr style="background-color:#9F35AC; color:white; font-weight:bold;">
							<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($class['courseID']) ?></th>
							<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($class['sectionID']) ?></th>
							<th style="padding:4px; font-size:16px;" colspan="2"><?= htmlspecialchars($class['courseName']) ?></th>
							<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($class['term'] . ' ' . $class['year']) ?></th>
						</tr>

						<tr>
							<td style="padding:4px;"><?= htmlspecialchars($class['startDate']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['endDate']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['classDays']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['startTime']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['endTime']) ?></td>
						</tr>
						<tr>
						<td colspan="5" style="text-align:right; margin: 20px">
							<form method="POST" action="">
								<input type="hidden" name="courseOffering" value="<?= htmlspecialchars($class['courseOfferingID']) ?>">
								<button type="submit" name="unregister" style="background-color:#9F35AC; color:white; padding:6px 12px; border:none; cursor:pointer;">Remove from Schedule</button>
							</form>
						</td>
						</tr>
						<tr><td colspan="5">&nbsp;</td></tr>
					<?php endforeach; ?>
					<?php else: ?>
						<tr><td colspan="5">No course offerings found.</td></tr>
					<?php endif; ?>
				</table>

			</div>
			<div>
				<h2>Waitlisted Courses</h2>
				<table class="table" style="width:100%; border-collapse: collapse;">
					<?php if ($waitlistClasses && count($waitlistClasses) > 0): ?>
					<?php foreach ($waitlistClasses as $class): ?>
						<tr style="background-color:#9F35AC; color:white; font-weight:bold;">
							<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($class['courseID']) ?></th>
							<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($class['sectionID']) ?></th>
							<th style="padding:4px; font-size:16px;" colspan="2"><?= htmlspecialchars($class['courseName']) ?></th>
							<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($class['term'] . ' ' . $class['year']) ?></th>
						</tr>

						<tr>
							<td style="padding:4px;"><?= htmlspecialchars($class['startDate']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['endDate']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['classDays']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['startTime']) ?></td>
							<td style="padding:4px;"><?= htmlspecialchars($class['endTime']) ?></td>
						</tr>
						<tr>
						<td colspan="5" style="text-align:right; margin: 20px">
							<form method="POST" action="">
								<input type="hidden" name="courseOffering" value="<?= htmlspecialchars($class['courseOfferingID']) ?>">
								<button type="submit" name="unwaitlist" style="background-color:#9F35AC; color:white; padding:6px 12px; border:none; cursor:pointer;">Remove from Waitlist</button>
							</form>
						</td>
						</tr>
						<tr><td colspan="5">&nbsp;</td></tr>
					<?php endforeach; ?>
					<?php else: ?>
						<tr><td colspan="5">No course offerings found.</td></tr>
					<?php endif; ?>
				</table>
			</div>
		</div>
	</div>
</div>
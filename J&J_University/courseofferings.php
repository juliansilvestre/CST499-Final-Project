<?php
require_once 'dbhandler.php';
$db = new DatabaseHandler();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$courseOffering = $_POST['courseOffering'];
	$studentID = $_SESSION['userID'];

	try {
		$sqlCart = "INSERT INTO tblCart (courseOffering, studentID) VALUES ($courseOffering, $studentID)";
		if ($db->executeQuery($sqlCart)) {
			$message = "Course added to cart."; }
		else {
			$message = "Could not add course to your cart. Error: " . $db->con->error; }}
	catch (mysqli_sql_exception $e) {
		if ($e->getCode() == 1062) {
			$message = "Course is already in your cart."; }
		else {
			$message = "Could not add course to your cart. Error: " . $e->getMessage(); }}
}

$today = new DateTime();
$month = (int)$today->format('m');
$year = (int)$today->format('Y');

if ($month >= 1 && $month <= 4) {
	$currentTerm = 'Spring';
	$currentYear = $year;
	$nextTerm = 'Fall';
	$nextYear = $year; }
elseif ($month >= 5 && $month <= 7) {
	$currentTerm = 'Summer';
	$currentYear = $year;
	$nextTerm = 'Fall';
	$nextYear = $year; }
else {
	$currentTerm = 'Fall';
	$currentYear = $year;
	$nextTerm = 'Spring';
	$nextYear = $year + 1; }

$terms = [$currentTerm . ' ' . $currentYear, $nextTerm . ' ' . $nextYear ];
$selectedTerm = isset($_GET['term']) ? $_GET['term'] : $terms[0];
list($termFilter, $yearFilter) = explode(' ', $selectedTerm);

$sqlReg = "SELECT isOpen FROM tblRegistrationPeriods WHERE term = '$termFilter' AND year = '$yearFilter'";
$regStatus = $db->executeSelectQuery($sqlReg);
$isRegistrationOpen = ($regStatus && $regStatus[0]['isOpen'] == 1);

$sql = "SELECT co.*, c.name AS courseName FROM tblCourseOfferings AS co
	JOIN tblCourses AS c ON co.courseID = c.courseID
	WHERE co.term = '$termFilter' AND co.year = $yearFilter
	ORDER BY co.courseID ASC, co.sectionID ASC";
$offerings = $db->executeSelectQuery($sql);
?>

<div class='page-content'>
	<h2 class="text-center">Course Schedule</h2>
	
	<div class="doublecontent-container">
		<div class="sidebar">
			<h2>Term</h2>
			<?php
				foreach ($terms as $term) {
					$activeClass = ($term === $selectedTerm) ? 'active' : '';
					echo '<a href="?page=courseofferings&term=' . urlencode($term) . '" class="sidebar-link ' . $activeClass . '">' . htmlspecialchars($term) . '</a>'; }
			?>
		</div>

		<div class="doublecontent-content">
			<?php if (!empty($message)): ?>
				<div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
			<?php endif; ?>
			<table class="table" style="width:100%; border-collapse: collapse;">
				<?php if ($offerings && count($offerings) > 0): ?>
				<?php foreach ($offerings as $offering): ?>
					<tr style="background-color:#9F35AC; color:white; font-weight:bold;">
						<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($offering['courseID']) ?></th>
						<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($offering['sectionID']) ?></th>
						<th style="padding:4px; font-size:16px;" colspan="2"><?= htmlspecialchars($offering['courseName']) ?></th>
						<th style="padding:4px; font-size:16px;"><?= htmlspecialchars($offering['term'] . ' ' . $offering['year']) ?></th>
					</tr>

					<tr>
						<td style="padding:4px;"><?= htmlspecialchars($offering['startDate']) ?></td>
						<td style="padding:4px;"><?= htmlspecialchars($offering['endDate']) ?></td>
						<td style="padding:4px;"><?= htmlspecialchars($offering['classDays']) ?></td>
						<td style="padding:4px;"><?= htmlspecialchars($offering['startTime']) ?></td>
						<td style="padding:4px;"><?= htmlspecialchars($offering['endTime']) ?></td>
					</tr>
					<?php if (isset($_SESSION['username']) && $isRegistrationOpen): ?>
					<tr>
						<td colspan="5" style="text-align:right; margin: 20px">
							<form method="POST" action="">
								<input type="hidden" name="courseOffering" value="<?= htmlspecialchars($offering['id']) ?>">
								<input type="hidden" name="term" value="<?= htmlspecialchars($selectedTerm) ?>">
								<button type="submit" style="background-color:#9F35AC; color:white; padding:6px 12px; border:none; cursor:pointer;">Add to Cart</button>
							</form>
						</td>
					</tr>
					<?php endif; ?>
					<tr><td colspan="5">&nbsp;</td></tr>
				<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="5">No course offerings found.</td></tr>
				<?php endif; ?>
			</table>
		</div>
	</div>
</div>
<?php
require_once 'dbhandler.php';
$db = new DatabaseHandler();

$sql = "SELECT * FROM tblCourses ORDER BY courseId ASC";
$courses = $db->executeSelectQuery($sql);
?>

<div class="page-content">
	<h2>Course Catalog</h2>

	<table class ="table" style="width:100%; border-collapse: collapse;">
		<?php if ($courses && count($courses) > 0): ?>
		<?php foreach ($courses as $course): ?>
			<tr>
				<th style="text-align:left; padding:4px; font-size:16px;"><?= htmlspecialchars($course['courseID']) ?></th>
				<th style="text-align:left; padding:4px; font-size:16px;"><?= htmlspecialchars($course['name']) ?></th>
				<th style="text-align:left; padding:4px; font-size:16px;"><?= htmlspecialchars($course['credits']) ?> credits</th>
			</tr>
			<tr>
				<td colspan="3" style="padding:4px;"><?= htmlspecialchars($course['description']) ?></td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
		<?php endforeach; ?>
		<?php else: ?>
			<tr><td>No courses found.</td></tr>
		<?php endif; ?>
	</table>
</div>

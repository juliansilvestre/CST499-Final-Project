<?php
require_once 'dbhandler.php';
$db = new DatabaseHandler();

class AutoWaitlist {
	private $db;

	public function __construct($db) {
		$this->db = $db; }

	public function enrollFromUnregister($courseOfferingID) {
		$sqlCapacity = "SELECT capacityLimit, (SELECT COUNT(*) FROM tblCourseRegistration WHERE courseOfferingID = $courseOfferingID) AS currentCount
 			FROM tblCourseOfferings
  			WHERE id = $courseOfferingID";
		$result = $this->db->executeSelectQuery($sqlCapacity);
		if (!$result) return false;

		$capacity = $result[0]['capacityLimit'];
		$currentCount = $result[0]['currentCount'];

		if ($currentCount >= $capacity) return false;

		$sqlNext = "SELECT id, userID FROM tblCourseWaitlist WHERE courseOfferingID = $courseOfferingID ORDER BY waitlistTime ASC LIMIT 1";
		$waitlist = $this->db->executeSelectQuery($sqlNext);
		if (!$waitlist) return false;

		$nextStudentID = $waitlist[0]['userID'];
		$waitlistID = $waitlist[0]['id'];

		$sqlRegister = "INSERT INTO tblCourseRegistration (courseOfferingID, userID) VALUES ($courseOfferingID, $nextStudentID)";
		$this->db->executeQuery($sqlRegister);

		$sqlRemove = "DELETE FROM tblCourseWaitlist WHERE id = $waitlistID";
		$this->db->executeQuery($sqlRemove);

		return true;
	}

	public function enroll($courseOfferingID, $studentID) {
		$sqlCheckReg = "SELECT * FROM tblCourseRegistration WHERE courseOfferingID = $courseOfferingID AND userID = $studentID";
		$checkReg = $this->db->executeSelectQuery($sqlCheckReg);
		if ($checkReg && count($checkReg) > 0) return "Already registered for course.";

		$sqlCheckWait = "SELECT * FROM tblCourseWaitlist WHERE courseOfferingID = $courseOfferingID AND userID = $studentID";
		$checkWait = $this->db->executeSelectQuery($sqlCheckWait);
		if ($checkWait && count($checkWait) > 0) return "Already waitlisted for course.";

		$sqlCapacity = "SELECT capacityLimit, waitlistLimit, (SELECT COUNT(*) FROM tblCourseRegistration WHERE courseOfferingID = $courseOfferingID) AS currentCount, (SELECT COUNT(*) FROM tblCourseWaitlist WHERE courseOfferingID = $courseOfferingID) as currentWaitlistCount
 			FROM tblCourseOfferings
  			WHERE id = $courseOfferingID";
		$result = $this->db->executeSelectQuery($sqlCapacity);
		if (!$result) return false;

		$capacity = $result[0]['capacityLimit'];
		$currentCount = $result[0]['currentCount'];
		$waitlistCapacity = $result[0]['waitlistLimit'];
		$currentWaitlistCount = $result[0]['currentWaitlistCount'];

		if ($currentCount < $capacity) { 
			$sqlRegister = "INSERT INTO tblCourseRegistration (courseOfferingID, userID)
				VALUES ($courseOfferingID, $studentID)";
			$sqlClearCart = "DELETE FROM tblCart WHERE courseOffering = $courseOfferingID AND studentID = $studentID";
			$this->db->executeQuery($sqlRegister);
			$this->db->executeQuery($sqlClearCart);
			return "Successfully registered"; }
		elseif ($currentWaitlistCount < $waitlistCapacity) {
			$sqlWaitlist = "INSERT INTO tblCourseWaitlist (courseOfferingID, userID)
				VALUES ($courseOfferingID, $studentID)";
			$sqlClearCart = "DELETE FROM tblCart WHERE courseOffering = $courseOfferingID AND studentID = $studentID";
			$this->db->executeQuery($sqlWaitlist);
			$this->db->executeQuery($sqlClearCart);
			return "Class is full. Successfully waitlisted"; }
		else {
			return "Could not register or waitlist for course, both lists are full."; }
	}
}

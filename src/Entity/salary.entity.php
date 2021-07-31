<?php
include_once($_SERVER['HTTP_SERVER_ROOT'] . '/src/Entity/employee.entity.php');
include_once($_SERVER['HTTP_SERVER_ROOT'] . '/src/DataFixtures/PayChangeFixture.php');

/**
 *  Salary Entity (Model)
 */
class SalaryEntity extends EmployeeEntity
{
  private $db;

  function __construct($public_id = null) {
    parent::__construct($public_id, $email = null); // EmployeeEntity
    $this->db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
  }

  public function createPayChangeRequest($arr) {
    $employee_no = (int)$this->getEmployeeNo();
    $old_pay_rate = (int)$this->getPayRate();
    $new_pay_Rate = (int)urldecode($arr['new_pay_rate']);
    $pay_change_reason = urldecode($arr['pay_change_reason']);

    $payChangeObject = array(
      'employee_no' => $employee_no,
      'old_pay_rate' => $old_pay_rate,
      'new_pay_Rate' => $new_pay_Rate,
      'pay_change_reason' => $pay_change_reason
    );
    $payChangeClass = new PayChangeFixture();
    return $payChangeClass->load($payChangeObject);
  }


  /*
  * Get all pay change request by area name
  */
  public function getAreaPayChangeRequest($area_name) {
    $sql = "
      SELECT
        pay_change_request.request_id AS 'rid',
        employee.employee_no AS 'employee_no',
        employee.first_name AS 'fname',
        employee.last_name AS 'lname',
        restaurant.postal_code AS 'restaurant_postal_code',
        restaurant.area_name AS 'restaurant_area_name',
        restaurant.name AS 'restaurant_name',
        pay_change_request.old_pay_rate AS 'old_rate',
        pay_change_request.new_pay_rate AS 'new_rate',
        pay_change_request.pay_change_reason AS 'reason',
        pay_change_request.date_created AS 'datetime'
      FROM pay_change_request
      INNER JOIN employee ON employee.employee_no = pay_change_request.employee_no
      INNER JOIN restaurant ON restaurant.postal_code = employee.restaurant_postal
      WHERE restaurant.area_name = '$area_name'
      AND pay_change_request.decision_id == null
      ORDER BY pay_change_request.date_created DESC
    ";
    $qry = mysqli_query($this->db, $sql);
    $resArr = null;
    if ($qry && mysqli_num_rows($qry) > 0) {
      $resArr = array();
      while ($data = mysqli_fetch_assoc($qry)) {
        $data['decision'] = $this->getPayChangeDecision($data['rid']);
        $resArr[] = $data;
      }
    }
    return $resArr;
  }


  /*
  * Get all pay change request
  */
  public function getAllPayChangeRequest() {
    $sql = "
    SELECT
      pay_change_request.request_id AS 'rid',
      employee.employee_no AS 'employee_no',
      employee.first_name AS 'fname',
      employee.last_name AS 'lname',
      restaurant.postal_code AS 'restaurant_postal_code',
      restaurant.area_name AS 'restaurant_area_name',
      restaurant.name AS 'restaurant_name',
      pay_change_request.old_pay_rate AS 'old_rate',
      pay_change_request.new_pay_rate AS 'new_rate',
      pay_change_request.pay_change_reason AS 'reason',
      pay_change_request.date_created AS 'datetime'
    FROM pay_change_request
    INNER JOIN employee ON employee.employee_no = pay_change_request.employee_no
    INNER JOIN restaurant ON restaurant.postal_code = employee.restaurant_postal
    ORDER BY pay_change_request.date_created DESC
    ";
    $qry = mysqli_query($this->db, $sql);
    $resArr = null;
    if ($qry && mysqli_num_rows($qry) > 0) {
      $resArr = array();
      while ($data = mysqli_fetch_assoc($qry)) {
        $data['decision'] = $this->getPayChangeDecision($data['rid']);
        $resArr[] = $data;
      }
    } else {
      $resArr = $this->db->error;
    }
    return $resArr;
  }


  /*
  * Get all pay change request by Postal code
  */
  public function getPostalCodePayChangeRequest($postal_code) {
    $sql = "
      SELECT
        pay_change_request.request_id AS 'rid',
        employee.employee_no AS 'employee_no',
        employee.first_name AS 'fname',
        employee.last_name AS 'lname',
        restaurant.postal_code AS 'restaurant_postal_code',
        restaurant.area_name AS 'restaurant_area_name',
        restaurant.name AS 'restaurant_name',
        pay_change_request.old_pay_rate AS 'old_rate',
        pay_change_request.new_pay_rate AS 'new_rate',
        pay_change_request.pay_change_reason AS 'reason',
        pay_change_request.date_created AS 'datetime'
      FROM pay_change_request
      INNER JOIN employee ON employee.employee_no = pay_change_request.employee_no
      INNER JOIN restaurant ON restaurant.postal_code = employee.restaurant_postal
      WHERE restaurant.postal_code = '$postal_code'
      AND pay_change_request.decision_id == null
      ORDER BY pay_change_request.date_created DESC
    ";
    $qry = mysqli_query($this->db, $sql);
    $resArr = null;
    if ($qry && mysqli_num_rows($qry) > 0) {
      $resArr = array();
      while ($data = mysqli_fetch_assoc($qry)) {
        $data['decision'] = $this->getPayChangeDecision($data['rid']);
        $resArr[] = $data;
      }
    }
    return $resArr;
  }


  /*
  * Get pay change request by the request ID
  */
  public function getPayChangeRequest($paychangeID) {
    $nullVar = null;
    $sql = "
      SELECT
        pay_change_request.request_id AS 'rid',
        employee.employee_no AS 'employee_no',
        employee.first_name AS 'fname',
        employee.last_name AS 'lname',
        restaurant.postal_code AS 'restaurant_postal_code',
        restaurant.area_name AS 'restaurant_area_name',
        restaurant.name AS 'restaurant_name',
        pay_change_request.old_pay_rate AS 'old_rate',
        pay_change_request.new_pay_rate AS 'new_rate',
        pay_change_request.pay_change_reason AS 'reason',
        pay_change_request.date_created AS 'datetime'
      FROM pay_change_request
      INNER JOIN employee ON employee.employee_no = pay_change_request.employee_no
      INNER JOIN restaurant ON restaurant.postal_code = employee.restaurant_postal
      WHERE pay_change_request.request_id = '$paychangeID'
      AND pay_change_request.decision_id = '$nullVar'
      ORDER BY pay_change_request.date_created DESC
    ";
    $qry = mysqli_query($this->db, $sql);
    $resArr = null;
    if ($qry && mysqli_num_rows($qry) > 0) {
      $resArr = array();
      while ($data = mysqli_fetch_assoc($qry)) {
        $data['decision'] = $this->getPayChangeDecision($data['rid']);
        $resArr[] = $data;
      }
    }
    return $resArr;
  }


  /*
  * Get payroll
  */
  public function getPayRoll() {
    $sql = "
    SELECT
      employee.employee_no AS 'employee_no',
      employee.first_name AS 'fname',
      employee.last_name AS 'lname',
      employee.pay_rate AS 'pay_rate',
      restaurant.name AS 'restaurant_name',
      restaurant.area_name AS 'restaurant_area_name',
      restaurant.postal_code AS 'restaurant_postal_code'
    FROM employee
    INNER JOIN restaurant ON restaurant.postal_code = employee.restaurant_postal
    ORDER BY employee.pay_rate DESC
    ";
    $qry = mysqli_query($this->db, $sql);
    $resArr = null;
    if ($qry && mysqli_num_rows($qry) > 0) {
      $resArr = array();
      while ($data = mysqli_fetch_assoc($qry)) {
        $data['paychange_history'] = $this->getEmployeePayChangeRequests($data['employee_no']);
        $resArr[] = $data;
      }
    } else {
      $resArr = $this->db->error;
    }
    return $resArr;
  }


  /*
  * Create pay change decision
  */
  public function createPayChangeDecision($payChangeID, $approval) {
    $payChangeID = $this->db->escape_string($payChangeID);
    $payChangeData = $this->getPayChangeRequest($payChangeID);
    $resArr = array();
    $sql = "INSERT INTO pay_change_decision(approval, signed_by) VALUES('$approval', 'admin')";
    $qry = mysqli_query($this->db, $sql);
    if ($qry) {
      $decision_id = $this->db->insert_id;
      $sql = "UPDATE pay_change_request SET decision_id = '$decision_id' WHERE request_id = '$payChangeID'";
      $qry2 = mysqli_query($this->db, $sql);
      $resArr['res'] = ($qry2) ? true : false;
    } else {
      $resArr['res'] = false;
    }
    return $resArr;
  }


  public function getPayChangeDecision($payChangeID) {
    $sql = "
    SELECT
      employee.employee_no AS 'employee_no',
      pay_change_request.request_id AS 'rid',
      pay_change_decision.decision_id AS 'decision_id',
      pay_change_decision.approval AS 'approval',
      pay_change_decision.signed_by AS 'signed_by',
      pay_change_decision.approved_date AS 'approved_date',
      pay_change_decision.effective_date AS 'effective_date'
    FROM pay_change_decision
    INNER JOIN pay_change_request ON pay_change_request.decision_id = pay_change_decision.decision_id
    INNER JOIN employee ON employee.employee_no = pay_change_request.employee_no
    WHERE pay_change_request.request_id = '$payChangeID'
    ";
    $qry = mysqli_query($this->db, $sql);
    if ($qry) {
      $result = mysqli_fetch_assoc($qry);
      return $result;
    } else {
      echo $this->db->error;
    }
  }



  public function getEmployeePayChangeRequests($employee_no) {
    $sql = "
    SELECT
      employee.employee_no AS 'employee_no',
      pay_change_request.request_id AS 'rid',
      pay_change_request.old_pay_rate AS 'old_rate',
      pay_change_request.new_pay_rate AS 'new_rate',
      pay_change_request.pay_change_reason AS 'reason',
      pay_change_request.date_created AS 'datetime',
      pay_change_decision.decision_id AS 'decision_id',
      pay_change_decision.approval AS 'approval',
      pay_change_decision.signed_by AS 'signed_by',
      pay_change_decision.approved_date AS 'approved_date',
      pay_change_decision.effective_date AS 'effective_date'
    FROM pay_change_decision
    INNER JOIN pay_change_request ON pay_change_request.decision_id = pay_change_decision.decision_id
    INNER JOIN employee ON employee.employee_no = pay_change_request.employee_no
    WHERE pay_change_request.employee_no = '$employee_no'
    ORDER BY pay_change_request.date_created DESC
    ";
    $qry = mysqli_query($this->db, $sql);
    $resArr = null;
    if ($qry && mysqli_num_rows($qry) > 0) {
      $resArr = array();
      while ($data = mysqli_fetch_assoc($qry)) {
        $resArr[] = $data;
      }
    }
    return $resArr;
  }
}
?>

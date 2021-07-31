<?php
/**
 *  Employee Entity (Model)
 */
class EmployeeEntity
{
  private $db;
  private $public_id;
  private $firstname;
  private $lastname;
  private $payrate;
  private $email;
  private $restaurant_postal;
  private $employee_no;

  function __construct($public_id = null, $email = null) {
    $this->db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    if ($public_id !== null) $this->public_id = $this->db->escape_string($public_id);
    if ($email !== null) $this->email = $this->db->escape_string($email);
    $this->fetchEmplyeeProfile();
  }

  public function fetchEmplyeeProfile()
  {
    $sql = "SELECT * FROM employee WHERE email = '$this->email' OR public_id = '$this->public_id'";
    $qry = mysqli_query($this->db, $sql);
    if ($qry && mysqli_num_rows($qry) == 1) {
      $data = mysqli_fetch_assoc($qry);
      $this->setEmployeeNo($data['employee_no']);
      $this->setFirstName($data['first_name']);
      $this->setLastName($data['last_name']);
      $this->setEmail($data['email']);
      $this->setRestaurantPostal($data['restaurant_postal']);
      $this->setPublicId($data['public_id']);
      $this->setPayRate($data['pay_rate']);
    }
  }

  public function getEmployeeNo() {
    return $this->employee_no;
  }
  public function setEmployeeNo(string $employee_no) {
    $this->employee_no = $employee_no;
    return $this;
  }

  public function getPublicId() {
    return $this->public_id;
  }
  public function setPublicId(string $public_id) {
    $this->public_id = $public_id;
    return $this;
  }


  public function getPayRate() {
    return $this->payrate;
  }
  public function setPayRate(string $payrate) {
    $this->payrate = $payrate;
    return $this;
  }


  public function getFirstName() {
    return $this->firstname;
  }
  public function setFirstName(string $firstname) {
    $this->firstname = $firstname;
    return $this;
  }


  public function getLastName() {
    return $this->lastname;
  }
  public function setLastName(string $lastname) {
    $this->lastname = $lastname;
    return $this;
  }


  public function getEmail() {
    return $this->email;
  }
  public function setEmail(string $email) {
    $this->email = $email;
    return $this;
  }


  public function getRestaurantPostal() {
    return $this->restaurant_postal;
  }
  public function setRestaurantPostal(string $restaurant_postal) {
    $this->restaurant_postal = $restaurant_postal;
    return $this;
  }
}

 ?>

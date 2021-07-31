<?php
require $_SERVER['HTTP_SERVER_ROOT'] . '/vendor/autoload.php';

// use HemiFrame\Lib\AES;

/**
 *  Class to handle adding employee to database
 */
class EmployeeFixture
{
  private $db;

  public function __construct(array $foo = []) {
    $this->db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
  }

  public function random_strings($length_of_string) {
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($str_result), 0, $length_of_string);
  }

  public function load($EmployeeDataObject = null) {
    if ($EmployeeDataObject === null) return array('res' => null, 'employee_id' => null);

    $public_id  =  $this->random_strings(6);
    $firstname  =  $this->db->escape_string($EmployeeDataObject['firstname']);
    $lastname   =  $this->db->escape_string($EmployeeDataObject['lastname']);
    $email      =  $this->db->escape_string(urldecode($EmployeeDataObject['email']));
    $payrate    =  $this->db->escape_string($EmployeeDataObject['payrate']);
    $postalCode =  $this->db->escape_string(urldecode($EmployeeDataObject['restaurant_pc']));

    $res = array();

    $sql = "SELECT * FROM restaurant WHERE postal_code = '$postalCode'";
    $qry = mysqli_query($this->db, $sql);
    if ($qry && mysqli_num_rows($qry) == 1) {
      $sql = "INSERT INTO employee(public_id, first_name, last_name, email, pay_rate, restaurant_postal)
              VALUES('$public_id', '$firstname', '$lastname', '$email', '$payrate', '$postalCode')";
      $qry = mysqli_query($this->db, $sql);
      $res['res'] = ($qry) ? true : false;
      $res['employee_id'] = ($qry) ? $public_id : null;
    } else {
      $res['res'] = 'invalid_postal_code';
    }
    return $res;
  }
}
?>

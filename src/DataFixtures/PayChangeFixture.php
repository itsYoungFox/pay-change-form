<?php
require $_SERVER['HTTP_SERVER_ROOT'] . '/vendor/autoload.php';

// use HemiFrame\Lib\AES;

/**
 *  Class to handle adding Pay change requests to database
 */
class PayChangeFixture
{
  private $db;

  public function __construct($data = null) {
    $this->db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
  }

  public function random_strings($length_of_string) {
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($str_result), 0, $length_of_string);
  }

  public function load($DataObject = null) {
    if ($DataObject === null) return array('res' => null);

    $employee_no = $this->db->escape_string($DataObject['employee_no']);
    $old_pay_rate = $this->db->escape_string($DataObject['old_pay_rate']);
    $new_pay_Rate = $this->db->escape_string($DataObject['new_pay_Rate']);
    $pay_change_reason = $this->db->escape_string($DataObject['pay_change_reason']);

    $sql = "INSERT INTO pay_change_request(employee_no, old_pay_rate, new_pay_Rate, pay_change_reason)
            VALUES('$employee_no', '$old_pay_rate', '$new_pay_Rate', '$pay_change_reason')";

    $qry = mysqli_query($this->db, $sql);

    $res = array(
      'res' => ($qry) ? true : false
    );
    return $res;
  }
}
?>

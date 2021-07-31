<?php
require $_SERVER['HTTP_SERVER_ROOT'] . '/vendor/autoload.php';
require $_SERVER['HTTP_SERVER_ROOT'] . '/config/db_config.php';

foreach (glob($_SERVER['HTTP_SERVER_ROOT'] . '/src/DataFixtures/*.php') as $filename) {
  include($filename);
}
include($_SERVER['HTTP_SERVER_ROOT'] . '/src/Entity/employee.entity.php');
include($_SERVER['HTTP_SERVER_ROOT'] . '/src/Entity/salary.entity.php');

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


/**
 * Main app post request handler
 */
class RequestController
{
  private $path;
  private $request_array;

  function __construct(String $path = null) {
    $this->path = $path;
    $this->forbidden = array('error_handler', 'database');
    $this->request_array = (isset($_POST)) ? $_POST : null;

    switch ($path) {
      case null:
        $this->error_handler(405);
        break;

      default:
        return (method_exists($this, $path) && !in_array($path, $this->forbidden)) ? $this->$path() : $this->error_handler(404);
        break;
    }
  }

  /*
  * Validate Employee
  */
  public function validate_employee() {
    $userObject = $this->request_array;
    $employeeObj = null;
    $res = array();
    if (filter_var(urldecode($userObject['employee_data']), FILTER_VALIDATE_EMAIL)) {
      // Email received
      $data = [null, urldecode($userObject['employee_data'])];
      $employeeObj = new EmployeeEntity(...$data);
      $public_id = $employeeObj->getPublicId();
      $res['res'] = ( $public_id !== null) ? true : false;
    } else {
      // Employee ID received
      $data = [$userObject['employee_data'], null];
      $employeeObj = new EmployeeEntity(...$data);
      $email = $employeeObj->getEmail();
      $res['res'] = ($email !== null) ? true : false;
    }
    $res['firstname'] = $employeeObj->getFirstName();
    $res['lastname']  = $employeeObj->getLastName();
    $res['payrate']   = $employeeObj->getPayRate();
    $res['public_id'] = base64_encode($employeeObj->getPublicId());
    echo json_encode($res);
  }


  /*
  * Employee Pay Change
  */
  public function employee_pay_change() {
    $userObject = $this->request_array;
    $salaryObj = new SalaryEntity(base64_decode($userObject['PubID']));
    echo json_encode($salaryObj->createPayChangeRequest($userObject));
  }


  /*
  * Register Employee
  */
  public function register_employee() {
    $userObject = $this->request_array;
    $employeeFixture = new EmployeeFixture();
    $fire = $employeeFixture->load($userObject);
    echo json_encode($fire);
  }


  /*
  * Fetch local area manager inbox
  */
  public function load_area_manager_inbox() {
    $userObject = $this->request_array;
    $salaryObj = new SalaryEntity();

    // $postalPayChangeRequests = ($userObject['restaurant_postal_code'] !== null) ? $salaryObj->getPostalCodePayChangeRequest($userObject['restaurant_postal_code']) : null;
    // $areaPayChangeRequests = ($userObject['area_name'] !== null) ? $salaryObj->getAreaPayChangeRequest($userObject['area_name']) : null;
    $allPayChangeRequests = $salaryObj->getAllPayChangeRequest();

    echo json_encode($allPayChangeRequests);
  }


  /*
  * Set pay change decision
  */
  public function pay_change_decision() {
    $userObject = $this->request_array;
    $salaryObj = new SalaryEntity();
    $createDecision = $salaryObj->createPayChangeDecision($userObject['pid'], $userObject['approval']);
    echo json_encode($createDecision);
  }


  /*
  * Get pay change decision
  */
  public function get_pay_change_decision() {
    $userObject = $this->request_array;
    $salaryObj = new SalaryEntity();
    $getDecision = $salaryObj->getPayChangeDecision($userObject['pid']);
    echo json_encode($getDecision);
  }


  /*
  * Get payroll
  */
  public function get_payroll() {
    $userObject = $this->request_array;
    $salaryObj = new SalaryEntity();
    $getPayRoll = $salaryObj->getPayRoll();
    echo json_encode($getPayRoll);
  }
}

?>

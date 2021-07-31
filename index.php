<?php
 session_start();
 // perform user check
 if (isset($_COOKIE['dID'])) { // new visitor
   /**
   *  # Check if the device id is valid
   *  # Assign current session to user ID attached to this device
   *  # Proceed with further algorithm
   **/
 }

 // Include router class
 include( $_SERVER['DOCUMENT_ROOT'] . '/class/route.php');

  echo '<!DOCTYPE html><html lang="en" dir="ltr"><head>';
     require $_SERVER['DOCUMENT_ROOT'] . '/section/header.php';
  echo '</head>';



 // Add base route (startpage)
 Route::add('/',function(){
    require 'view/home.php';
 });


 // Simple test route that simulates static html file
 Route::add('/test.html',function(){
    echo 'Hello from test.html';
 });


 // Post route example
 Route::add('/contact-form',function(){
    echo '<form method="post"><input type="text" name="test" /><input type="submit" value="send" /></form>';
 },'get');


 // Post route example
 Route::add('/contact-form',function(){
    echo 'Hey! The form has been sent:<br/>';
     print_r($_POST);
 },'post');


 // Accept only numbers as parameter. Other characters will result in a 404 error
 Route::add('/foo/([0-9]*)/bar',function($var1){
    echo $var1.' is a great number!';
 });


 Route::run($_SERVER['DOCUMENT_ROOT']);

 require $_SERVER['DOCUMENT_ROOT'] . '/section/footer.php';


?>

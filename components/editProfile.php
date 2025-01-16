<?php
session_start();
include '../databaseConnection.php';

if (!isset($_SESSION['user_id'])){
  die("WE could not get session ID");
}

$user_id = $_SESSION['user_id'];

// Check if the user is logged in
if (isset($_POST['Save'])) {
  $firstName=$_POST['editfirstname'];
  $lastName=$_POST['editlastname'];
  $email=$_POST['editmail'];


  // Correct SQL query
  $updateQuery = "UPDATE Employees 
                  SET firstname = '$firstName', 
                      lastname = '$lastName', 
                      email = '$email' 
                  WHERE employee_id = $user_id";

  if (mysqli_query($conn, $updateQuery)) {
    header("Location: ../Pages/homepage.php?message=Profile updated successfully");
    exit;
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }
} 
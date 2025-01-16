<?php
// Include database connection
include "../databaseConnection.php";

if (!isset($_GET['id'])) {
    die("Employee ID is missing.");
}

$id = intval($_GET['id']); // Sanitize ID
echo "User ID: " . htmlspecialchars($id);

// Fetch the employee details
$query = "SELECT * FROM Employees WHERE employee_id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Employee not found.");
}

$employee = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = mysqli_real_escape_string($conn, $_POST['editfirstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['editlastname']);
    $email = mysqli_real_escape_string($conn, $_POST['editmail']);
    $password = mysqli_real_escape_string($conn, $_POST['editpassword']);
    $profile_pic = mysqli_real_escape_string($conn, $_POST['profile_pic']);

    $updateQuery = "UPDATE Employees 
                    SET firstname = '$firstname', 
                        lastname = '$lastname', 
                        email = '$email', 
                        password = '$password',
                        profile_pic = '$profile_pic'
                    WHERE employee_id = $id";

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: homepage.php?message=Employee updated successfully");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>
 
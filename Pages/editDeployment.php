<?php
// Include database connection
include "../databaseConnection.php";

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Project ID is missing.");
}

$id = intval($_GET['id']);

// Fetch the transaction details
$query = "SELECT * FROM Projects WHERE project_id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Project not found.");
}

$project = mysqli_fetch_assoc($result);

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = mysqli_real_escape_string($conn, $_POST['projectName']);
    $projectBudget = floatval($_POST['projectBudget']);
    $endDate = mysqli_real_escape_string($conn, $_POST['endDate']);
    $projectStatus = mysqli_real_escape_string($conn, $_POST['projectStatus']);
    $projectNotes = mysqli_real_escape_string($conn, $_POST['ProjectNotes']);

    $updateQuery = "UPDATE Projects
                    SET project_name = '$projectName', 
                        project_budget = $projectBudget, 
                        end_date = '$endDate', 
                        notes = '$projectNotes',
                        status = '$projectStatus'
                    WHERE project_id = $id";

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: Deployment.php?message=Project updated successfully");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Transaction</title>
    <link rel="stylesheet" href="../style.css" />
</head>

<body>
    <div class="overlay" id="overlay" style="display: block;"></div>
    <div style="width: 50%;
        margin: 5% 30%;
        position: relative;
        z-index: 999;
        padding: 1rem;
        background: white;
        border-radius: 8px;">

        <h1>Edit Project</h1>
        <form method="POST" class="form-row">
            <label for="transactionType">Project Name</label>
            <input type="text" name="projectName"
                value="<?= htmlspecialchars($project['project_name'] ?? '') ?>">



            <label for="amount">Budget:</label>
            <input type="number" name="projectBudget"
                value="<?= htmlspecialchars($project['project_budget'] ?? '') ?>">

            <label for="amount">End Date:</label>
            <input type="Date" name="endDate"
                value="<?= htmlspecialchars($project['end_date'] ?? '') ?>">

            <!-- Payment Method Dropdown -->
            <label for="projectStatus">Project Status</label>
            <?php $category = htmlspecialchars($project['status'] ?? ''); ?>
            <select style="border: 1px solid; padding: 5px;" id="category" name="projectStatus">
                <option value="Active" <?= $category === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Completed" <?= $category === 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="inactive" <?= $category === 'inactive' ? 'selected' : '' ?>>In Active</option>
            </select>



            <label for="notes">Description:</label>
            <input type="text" name="ProjectNotes"
                value="<?= htmlspecialchars($project['notes'] ?? '') ?>">
            <button type="submit">Update Project</button>
        </form>

    </div>
    </div>
</body>

</html>
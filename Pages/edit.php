<?php
// Include database connection
include "../databaseConnection.php";

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Transaction ID is missing.");
}

$id = intval($_GET['id']);

// Fetch the transaction details
$query = "SELECT * FROM Transactions WHERE transaction_id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Transaction not found.");
}

$transaction = mysqli_fetch_assoc($result);

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = floatval($_POST['amount']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $deployment = mysqli_real_escape_string($conn, $_POST['deployment']);

    $updateQuery = "UPDATE Transactions 
                    SET transaction_amount = $amount, 
                        transaction_type = '$type', 
                        transaction_category = '$category', 
                        notes = '$notes'
                    WHERE transaction_id = $id";

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: Transaction.php?message=Transaction updated successfully");
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
        <link rel="stylesheet" href="../style.css"/>
</head>
<body>
    <div class="overlay" id="overlay" style="display: block;"></div>
     <div  style="width: 50%;
    margin: 5% 30%;
    position: relative;
    z-index: 999;
    padding: 1rem;
    background: white;
    border-radius: 8px;">
      
       <h1>Edit Transaction</h1>
       <form method="POST" class="form-row">
    <label for="transactionType">Transaction Type</label>
    <?php $transactionType = htmlspecialchars($transaction['transaction_type'] ?? '') ?>
    <select style="border: 1px solid; padding: 5px;" id="transactionType" name="type" required>
        <option value="income" <?= $transactionType === 'income' ? 'selected' : '' ?>>Income</option>
        <option value="expense" <?= $transactionType === 'expense' ? 'selected' : '' ?>>Expense</option>
        <option value="investment" <?= $transactionType === 'investment' ? 'selected' : '' ?>>Investment</option>
    </select>



    <label for="amount">Amount:</label>
    <input type="number" name="amount" 
           value="<?= htmlspecialchars($transaction['transaction_amount'] ?? '') ?>" required>

    <!-- Payment Method Dropdown -->
       <label for="category">Payment Method</label>
        <?php $category = htmlspecialchars($transaction['transaction_category'] ?? ''); ?>
        <select style="border: 1px solid; padding: 5px;" id="category" name="category" required>
            <option value="Credit Card" <?= $category === 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
            <option value="Debit Card" <?= $category === 'Debit Card' ? 'selected' : '' ?>>Debit Card</option>
            <option value="Cash" <?= $category === 'Cash' ? 'selected' : '' ?>>Cash</option>
            <option value="UPI" <?= $category === 'UPI' ? 'selected' : '' ?>>UPI</option>
            <option value="Net Banking" <?= $category === 'Net Banking' ? 'selected' : '' ?>>Net Banking</option>
        </select>



    <label for="notes">Notes:</label>
    <input type="text" name="notes" 
           value="<?= htmlspecialchars($transaction['notes'] ?? '') ?>">

    <button type="submit">Update Transaction</button>
</form>
      
     </div>
    </div>
</body>
</html>
 
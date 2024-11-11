<?php
session_start();
include('config.php');

if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== 'user') {
    header("Location: index.php");
    exit();
}


$userID = $_SESSION["userID"];
$userInfoSQL = "SELECT * FROM User WHERE userID = ?";
$userInfoStmt = $conn->prepare($userInfoSQL);
$userInfoStmt->bind_param("i", $userID);
$userInfoStmt->execute();
$result = $userInfoStmt->get_result();
$userInfo = $result->fetch_assoc();

$latestOrderSQL = "SELECT * FROM Request WHERE userID = ? ORDER BY requestID DESC LIMIT 1";
$latestOrderStmt = $conn->prepare($latestOrderSQL);
$latestOrderStmt->bind_param("i", $userID);
$latestOrderStmt->execute();
$latestOrderResult = $latestOrderStmt->get_result();
$latestOrder = $latestOrderResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <header>Welcome, <?php echo $userInfo['firstName']; ?>!</header>
        </div>

        <div class="confirmation-container">
            <h2>Order Confirmation</h2>

            <p><strong>Order ID:</strong> <?php echo $latestOrder['requestID']; ?></p>
            <p><strong>Service Type:</strong> <?php echo $latestOrder['serviceType']; ?></p>
            <p><strong>Pickup Date & Time:</strong> <?php echo $latestOrder['pickupDateTime']; ?></p>
            <p><strong>Delivery Date & Time:</strong> <?php echo $latestOrder['deliveryDateTime']; ?></p>
            <p><strong>Weight:</strong> <?php echo $latestOrder['weight']; ?> kg</p>
            <p><strong>Type of Clothes:</strong> <?php echo $latestOrder['typeOfClothes']; ?></p>
            <p><strong>Detergent:</strong> <?php echo $latestOrder['detergent']; ?></p>
            <p><strong>Folding Style:</strong> <?php echo $latestOrder['foldingStyle']; ?></p>
            <p><strong>Address:</strong> <?php echo $latestOrder['address']; ?></p>
            <p><strong>Barangay:</strong> <?php echo $latestOrder['barangay']; ?></p>
            <p><strong>Delivery Fee:</strong> <?php echo $latestOrder['deliveryFee']; ?> pesos</p>
            <p><strong>Total Cost:</strong> <?php echo $latestOrder['totalCost']; ?> pesos</p>


            <span>
                <a href="user_dashboard.php">Return</a>
                <a href="logout.php">Logout</a>
            </span>
        </div>
    </div>
</body>

</html>
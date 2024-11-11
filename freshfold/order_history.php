<?php
session_start();
include('config.php');

if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== 'user') {
    header("Location: index.php");
    exit();
}


$userID = $_SESSION["userID"];
$sql = "SELECT * FROM User WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$historySql = "SELECT * FROM Request WHERE userID = ? ORDER BY requestDateTime DESC";
$historyStmt = $conn->prepare($historySql);
$historyStmt->bind_param("i", $userID);
$historyStmt->execute();
$historyResult = $historyStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Order History | Laundry Service</title>
    <link rel="stylesheet" href="style2.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>

<body>
    <div class="container">
        <nav>
            <ul>
                <li><a href="profile.php" class="logo">
                        <img src="user.png">
                        <span class="nav-item"><?php echo $user["username"]; ?></span>
                    </a></li>
                <li><a href="user_dashboard.php">
                        <i class="fas fa-menorah"></i>
                        <span class="nav-item">Dashboard</span>
                    </a></li>
                <li><a href="logout.php" class="logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-item">Log out</span>
                    </a></li>
            </ul>
        </nav>

        <section class="main">
            <div class="main-top">
                <h1>Order History</h1>
                <i class="fas fa-history"></i>
            </div>

            <section class="transactions">
                <div class="transaction-list">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Service Type</th>
                                <th>Pickup Date & Time</th>
                                <th>Delivery Date & Time</th>
                                <th>Weight</th>
                                <th>Type of Clothes</th>
                                <th>Detergent</th>
                                <th>Folding Style</th>
                                <th>Address</th>
                                <th>Barangay</th>
                                <th>Delivery Fee</th>
                                <th>Total Cost</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($order = $historyResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $order["requestID"]; ?></td>
                                    <td><?php echo $order["serviceType"]; ?></td>
                                    <td><?php echo $order["pickupDateTime"]; ?></td>
                                    <td><?php echo $order["deliveryDateTime"]; ?></td>
                                    <td><?php echo $order["weight"]; ?> kg</td>
                                    <td><?php echo $order["typeOfClothes"]; ?></td>
                                    <td><?php echo $order["detergent"]; ?></td>
                                    <td><?php echo $order["foldingStyle"]; ?></td>
                                    <td><?php echo $order["address"]; ?></td>
                                    <td><?php echo $order["barangay"]; ?></td>
                                    <td>₱<?php echo number_format($order["deliveryFee"], 2); ?></td>
                                    <td>₱<?php echo number_format($order["totalCost"], 2); ?></td>
                                    <td><?php echo $order["status"]; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    </div>
</body>

</html>
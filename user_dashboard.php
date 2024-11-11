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

$requestSql = "SELECT * FROM Request WHERE userID = ? ORDER BY requestDateTime DESC LIMIT 5";
$requestStmt = $conn->prepare($requestSql);
$requestStmt->bind_param("i", $userID);
$requestStmt->execute();
$requestResult = $requestStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>User Dashboard | Laundry Service</title>
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
                <li><a href="#">
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
                <h1>User Dashboard</h1>
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="users">
                <div class="card">
                    <img src="resume.png">
                    <h4>Profile</h4>
                    -------------
                    <button onclick="window.location.href='profile.php'">Go!</button>
                </div>
                <div class="card">
                    <img src="history-book.png">
                    <h4>History</h4>
                    -------------
                    <button onclick="window.location.href='order_history.php'">Go!</button>
                </div>
                <div class="card">
                    <img src="clipboard.png">
                    <h4>Order</h4>
                    -------------
                    <button onclick="window.location.href='submit_order.php'">Go!</button>
                </div>
            </div>

            <section class="transactions">
                <div class="transaction-list">
                    <h1>Recent Requests</h1>
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
                            while ($request = $requestResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $request["requestID"]; ?></td>
                                    <td><?php echo $request["serviceType"]; ?></td>
                                    <td><?php echo $request["pickupDateTime"]; ?></td>
                                    <td><?php echo $request["deliveryDateTime"]; ?></td>
                                    <td><?php echo $request["weight"]; ?> kg</td>
                                    <td><?php echo $request["typeOfClothes"]; ?></td>
                                    <td><?php echo $request["detergent"]; ?></td>
                                    <td><?php echo $request["foldingStyle"]; ?></td>
                                    <td><?php echo $request["address"]; ?></td>
                                    <td><?php echo $request["barangay"]; ?></td>
                                    <td>₱<?php echo number_format($request["deliveryFee"], 2); ?></td>
                                    <td>₱<?php echo number_format($request["totalCost"], 2); ?></td>
                                    <td><?php echo $request["status"]; ?></td>
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
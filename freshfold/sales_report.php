<?php
session_start();
include('config.php');


if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== 'admin') {
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

$salesSql = "SELECT DATE(deliveryDateTime) AS saleDate, COUNT(*) AS totalSales, SUM(totalCost) AS totalEarnings
             FROM Request
             WHERE status = 'completed'
             GROUP BY DATE(deliveryDateTime)
             ORDER BY DATE(deliveryDateTime) DESC";
$salesResult = $conn->query($salesSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Sales Report | Laundry Service</title>
    <link rel="stylesheet" href="style2.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>

<body>
    <div class="container">

        <nav>
            <ul>
                <li><a href="#" class="logo">
                        <img src="user.png">
                        <span class="nav-item"><?php echo $user["username"]; ?></span>
                    </a></li>
                <li><a href="admin_dashboard.php">
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
                <h1>Sales Report</h1>
                <i class="fas fa-chart-line"></i>
            </div>

            <section class="transactions">
                <div class="transaction-list">
                    <h1>Sales by Date</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Sales</th>
                                <th>Total Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($sale = $salesResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $sale["saleDate"]; ?></td>
                                    <td><?php echo $sale["totalSales"]; ?></td>
                                    <td>₱<?php echo number_format($sale["totalEarnings"], 2); ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php

                    $totalSalesEarningsSql = "SELECT COUNT(*) AS totalSales, SUM(totalCost) AS totalEarnings
                                              FROM Request
                                              WHERE status = 'completed'";
                    $totalSalesEarningsResult = $conn->query($totalSalesEarningsSql);
                    $totalSalesEarnings = $totalSalesEarningsResult->fetch_assoc();
                    ?>

                    <div class="total-earnings">
                        <h2>Total Sales</h2>
                        <p><?php echo $totalSalesEarnings["totalSales"]; ?></p>
                    </div>

                    <div class="total-earnings">
                        <h2>Total Earnings</h2>
                        <p>₱<?php echo number_format($totalSalesEarnings["totalEarnings"], 2); ?></p>
                    </div>
                </div>
            </section>
        </section>
    </div>
</body>

</html>
<?php
session_start();
include('config.php');


if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== 'employee') {
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


$requestSql = "SELECT r.*, u.username AS requestedBy, e.firstName AS handledFirstName, e.lastName AS handledLastName 
               FROM Request r 
               JOIN User u ON r.userID = u.userID 
               LEFT JOIN User e ON r.handledBy = e.userID 
               WHERE r.status = 'completed'";
$requestResult = $conn->query($requestSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Completed Requests | Laundry Service</title>
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
                <li><a href="employee_dashboard.php">
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
                <h1>Completed Requests</h1>
                <i class="fas fa-check-circle"></i>
            </div>

            <section class="transactions">
                <div class="transaction-list">
                    <h1>Requests</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Requested By</th>
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
                                <th>Handled By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($request = $requestResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $request["requestID"]; ?></td>
                                    <td><?php echo $request["requestedBy"]; ?></td>
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
                                    <td><?php echo $request["handledFirstName"] . ' ' . $request["handledLastName"]; ?></td>
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
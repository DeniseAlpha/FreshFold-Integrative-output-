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


$userSql = "SELECT * FROM User WHERE role IN ('employee', 'admin')";
$userResult = $conn->query($userSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard | Laundry Service</title>
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
                <h1>Admin Dashboard</h1>
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="users">
                <div class="card">
                    <img src="resume.png">
                    <h4>User Management</h4>
                    -------------
                    <button onclick="window.location.href='user_management.php'">Go!</button>
                </div>

                <div class="card">
                    <img src="money-bag.png">
                    <h4>Sales Report</h4>
                    -------------
                    <button onclick="window.location.href='sales_report.php'">Go!</button>
                </div>
            </div>

            <section class="transactions">
                <div class="transaction-list">
                    <h1>List of Employees and Admins</h1>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Contact Info</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Barangay</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($user = $userResult->fetch_assoc()) {
                            ?>
                                <tr>
                                    <td><?php echo $user["userID"]; ?></td>
                                    <td><?php echo $user["firstName"]; ?></td>
                                    <td><?php echo $user["lastName"]; ?></td>
                                    <td><?php echo $user["contactInfo"]; ?></td>
                                    <td><?php echo $user["email"]; ?></td>
                                    <td><?php echo $user["address"]; ?></td>
                                    <td><?php echo $user["barangay"]; ?></td>
                                    <td><?php echo $user["username"]; ?></td>
                                    <td><?php echo $user["role"]; ?></td>
                                    <td>
                                        <button onclick="window.location.href='edit_user.php?userID=<?php echo $user["userID"]; ?>'">Edit</button>
                                    </td>
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
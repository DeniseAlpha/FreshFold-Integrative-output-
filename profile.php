<?php
session_start();
include('config.php');

if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== 'user') {
    header("Location: index.php");
    exit();
}

$errors = [
    "emptyfields" => "Please fill in all fields.",
    "usertaken" => "Username is already taken.",
    "invalidfields" => "Invalid or mismatched input fields.",
    "registrationfailed" => "Registration failed. Please try again.",
];

$barangays = [
    "Bagong Nayon", "Barangka", "Calantipay", "Catulinan", "Concepcion", "Hinukay", "Makinabang",
    "Matangtubig", "Pagala", "Paitan", "Piel", "Pinagbarilan", "Poblacion", "Sabang", "San jose",
    "San Roque", "Santa Barbara", "Santo Cristo", "Santo Niño", "Subic", "Sulivan", "Tangos", "Tarcan",
    "Tiaong", "Tibag", "Tilapayong", "Virgen de las Flores"
];

$userID = $_SESSION["userID"];

$sql = "SELECT * FROM User WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$userDetails = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newEmail = filter_var($_POST["newEmail"], FILTER_VALIDATE_EMAIL);
    $newContactInfo = $_POST["newContactInfo"];
    $newPassword = $_POST["newPassword"];
    $newAddress = $_POST["newAddress"];
    $newBarangay = $_POST["newBarangay"];

    if (!preg_match('/^\d{11}$/', $newContactInfo)) {
        echo "<script>alert('Contact info must be numeric and exactly 11 digits.');</script>";
    } else {
        $updateUserSQL = "UPDATE User SET email=?, contactInfo=?, address=?, barangay=?";

        if (!empty($newPassword)) {
            $updateUserSQL .= ", password=?";
        }

        $updateUserSQL .= " WHERE userID=?";

        $updateUserStmt = $conn->prepare($updateUserSQL);

        $typeDefString = "ssss";
        $paramValues = [$newEmail, $newContactInfo, $newAddress, $newBarangay];

        if (!empty($newPassword)) {
            $typeDefString .= "s";
            $paramValues[] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $typeDefString .= "i";
        $paramValues[] = $userID;

        call_user_func_array([$updateUserStmt, 'bind_param'], array_merge([$typeDefString], $paramValues));

        $updateUserStmt->execute();

        header("Location: user_dashboard.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateContactNumber() {
            var contactNumber = document.getElementById("newContactInfo").value;
            var contactRegex = /^\d{11}$/;

            if (!contactRegex.test(contactNumber)) {
                alert("Contact number must be numeric and exactly 11 digits.");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="header">
            <header>Profile</header>
        </div>
        <div class="user-details">
            <p><strong>User ID:</strong> <?php echo $userDetails['userID']; ?></p>
            <p><strong>Email:</strong> <?php echo $userDetails['email']; ?></p>
            <p><strong>Contact Info:</strong> <?php echo $userDetails['contactInfo']; ?></p>
            <p><strong>Address:</strong> <?php echo $userDetails['address']; ?></p>
            <p><strong>Barangay:</strong> <?php echo $userDetails['barangay']; ?></p>
        </div>
        <div class="edit-form">
            <form action="profile.php" method="post" onsubmit="return validateContactNumber();">
                <div class="input-field">
                    <label for="newEmail">New Email</label>
                    <input type="email" name="newEmail" id="newEmail" value="<?php echo $userDetails['email']; ?>">
                </div>

                <div class="input-field">
                    <label for="newContactInfo">New Contact Info</label>
                    <input type="text" name="newContactInfo" id="newContactInfo" value="<?php echo $userDetails['contactInfo']; ?>">
                </div>

                <div class="input-field">
                    <label for="newAddress">New Address</label>
                    <input type="text" name="newAddress" id="newAddress" value="<?php echo $userDetails['address']; ?>">
                </div>

                <div class="input-field">
                    <label for="newBarangay">New Barangay</label>
                    <select name="newBarangay" id="newBarangay">
                        <?php
                        foreach ($barangays as $barangayOption) {
                            echo "<option value=\"$barangayOption\" " . ($userDetails['barangay'] == $barangayOption ? "selected" : "") . ">$barangayOption</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="input-field">
                    <label for="newPassword">New Password</label>
                    <input type="password" name="newPassword" id="newPassword">
                </div>

                <div class="input-field">
                    <input type="submit" class="submit" name="submit" value="Update Profile">
                </div>

                <span>
                    <a href="user_dashboard.php">Cancel</a>
                </span>
            </form>
        </div>
    </div>
</body>

</html>
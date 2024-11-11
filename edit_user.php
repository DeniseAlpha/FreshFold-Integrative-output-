<?php
session_start();
include('config.php');

if (!isset($_SESSION["userID"]) || $_SESSION["role"] !== 'admin') {
    header("Location: index.php");
    exit();
}

function getUserDetails($userID)
{
    global $conn;
    $query = "SELECT * FROM User WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

$userID = $_GET['userID'];

$userDetails = getUserDetails($userID);
$barangays = [
    "Bagong Nayon", "Barangka", "Calantipay", "Catulinan", "Concepcion", "Hinukay", "Makinabang",
    "Matangtubig", "Pagala", "Paitan", "Piel", "Pinagbarilan", "Poblacion", "Sabang", "San jose",
    "San Roque", "Santa Barbara", "Santo Cristo", "Santo Niño", "Subic", "Sulivan", "Tangos", "Tarcan",
    "Tiaong", "Tibag", "Tilapayong", "Virgen de las Flores"
];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    $newFirstName = $_POST["newFirstName"];
    $newLastName = $_POST["newLastName"];
    $newEmail = $_POST["newEmail"];
    $newContactInfo = $_POST["newContactInfo"];
    $newAddress = $_POST["newAddress"];
    $newBarangay = $_POST["newBarangay"];
    $newUsername = $_POST["newUsername"];
    $newPassword = ($_POST["newPassword"] !== "") ? password_hash($_POST["newPassword"], PASSWORD_DEFAULT) : $userDetails['password'];

    $updateQuery = "UPDATE User SET firstName=?, lastName=?, email=?, contactInfo=?, address=?, barangay=?, username=?, password=? WHERE userID=?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssssssssi", $newFirstName, $newLastName, $newEmail, $newContactInfo, $newAddress, $newBarangay, $newUsername, $newPassword, $userID);
    $updateStmt->execute();

    header("Location: user_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            <p><strong>First Name:</strong> <?php echo $userDetails['firstName']; ?></p>
            <p><strong>Last Name:</strong> <?php echo $userDetails['lastName']; ?></p>
            <p><strong>Email:</strong> <?php echo $userDetails['email']; ?></p>
            <p><strong>Contact Info:</strong> <?php echo $userDetails['contactInfo']; ?></p>
            <p><strong>Address:</strong> <?php echo $userDetails['address']; ?></p>
            <p><strong>Barangay:</strong> <?php echo $userDetails['barangay']; ?></p>
            <p><strong>Username:</strong> <?php echo $userDetails['username']; ?></p>
        </div>
        <div class="edit-form">
            <form action="edit_user.php?userID=<?php echo $userID; ?>" method="post" onsubmit="return validateContactNumber();">
                <input type="hidden" name="userID" value="<?php echo $userDetails['userID']; ?>">

                <div class="input-field">
                    <label for="newFirstName">New First Name</label>
                    <input type="text" name="newFirstName" id="newFirstName" value="<?php echo $userDetails['firstName']; ?>">
                </div>

                <div class="input-field">
                    <label for="newLastName">New Last Name</label>
                    <input type="text" name="newLastName" id="newLastName" value="<?php echo $userDetails['lastName']; ?>">
                </div>

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
                    <label for="newUsername">New Username</label>
                    <input type="text" name="newUsername" id="newUsername" value="<?php echo $userDetails['username']; ?>">
                </div>

                <div class="input-field">
                    <label for="newPassword">New Password</label>
                    <input type="password" name="newPassword" id="newPassword">
                </div>

                <div class="input-field">
                    <input type="submit" class="submit" name="submit" value="Update Profile">
                </div>

                <span>
                    <a href="user_management.php">Cancel</a>
                </span>
            </form>
        </div>
    </div>
</body>

</html>
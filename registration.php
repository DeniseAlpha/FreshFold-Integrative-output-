<?php
include('config.php');

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

$firstName = $lastName = $contactInfo = $email = $address = $barangay = $username = $password = $confirmPassword = $role = $rolePassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST["firstname"];
    $lastName = $_POST["lastname"];
    $contactInfo = $_POST["contactnumber"];
    $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
    $address = $_POST["address"];
    $barangay = $_POST["barangay"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm-password"];
    $role = $_POST["role"];
    $rolePassword = $_POST["role-password"];


    if (emptyFields([$firstName, $lastName, $contactInfo, $email, $address, $barangay, $username, $password, $confirmPassword])) {
        $errorCode = "emptyfields";
    } elseif (!isValidContactNumber($contactInfo) || !passwordsMatch($password, $confirmPassword)) {
        $errorCode = "invalidfields";
    } elseif (isUsernameTaken($conn, $username)) {
        $errorCode = "usertaken";
    } elseif (($role === "admin" || $role === "employee") && !checkRolePassword($rolePassword)) {
        $errorCode = "invalidfields";
    } else {
        if (registerUser($conn, $firstName, $lastName, $contactInfo, $email, $address, $barangay, $username, $password, $role)) {
            header("Location: index.php");
            exit();
        } else {
            $errorCode = "registrationfailed";
        }
    }

    header("Location: registration.php?error=$errorCode");
    exit();
}


function emptyFields($fields)
{
    return in_array("", $fields);
}

function isValidContactNumber($contactNumber)
{
    $contactRegex = '/^\d{11}$/';
    return preg_match($contactRegex, $contactNumber);
}

function passwordsMatch($password, $confirmPassword)
{
    return $password === $confirmPassword;
}

function isUsernameTaken($conn, $username)
{
    $checkUsernameSQL = "SELECT username FROM User WHERE username = ?";
    $checkUsernameStmt = $conn->prepare($checkUsernameSQL);
    $checkUsernameStmt->bind_param("s", $username);
    $checkUsernameStmt->execute();
    $checkUsernameStmt->store_result();

    return $checkUsernameStmt->num_rows > 0;
}

function checkRolePassword($rolePassword)
{
    $correctRolePassword = password_verify($rolePassword, password_hash("adminpassword", PASSWORD_DEFAULT));
    return $correctRolePassword;
}

function registerUser($conn, $firstName, $lastName, $contactInfo, $email, $address, $barangay, $username, $password, $role)
{
    $insertUserSQL = "INSERT INTO User (firstName, lastName, contactInfo, email, address, barangay, username, password, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertUserStmt = $conn->prepare($insertUserSQL);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertUserStmt->bind_param("sssssssss", $firstName, $lastName, $contactInfo, $email, $address, $barangay, $username, $hashedPassword, $role);

    return $insertUserStmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateContactNumber() {
            var contactNumber = document.getElementById("contactnumber").value;
            var contactRegex = /^\d{11}$/;

            if (!contactRegex.test(contactNumber)) {
                alert("Contact number must be numeric and exactly 11 digits.");
                return false;
            }
            return true;
        }

        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="header">
            <header>Register</header>
        </div>
        <form action="registration.php" method="post" onsubmit="return validateContactNumber() && validatePassword();">
            <div class="input-container">

                <div class="input-field">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" pattern="[A-Za-z]+" title="A-Z only" value="<?php echo $firstName; ?>" required>
                </div>


                <div class="input-field">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" pattern="[A-Za-z]+" title="A-Z only" value="<?php echo $lastName; ?>" required>
                </div>


                <div class="input-field">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo $email; ?>" required>
                </div>


                <div class="input-field">
                    <label for="contactnumber">Contact Number</label>
                    <input type="text" name="contactnumber" id="contactnumber" pattern="\d{11}" placeholder="Eg. 09123456789" value="<?php echo $contactInfo; ?>" required>
                </div>


                <div class="input-field">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?php echo $address; ?>" pattern="[a-zA-Z0-9\s\-,.#()]+">
                </div>



                <div class="input-field">
                    <label for="barangay">Barangay</label>
                    <select name="barangay" id="barangay" required>
                        <?php
                        foreach ($barangays as $barangayOption) {
                            echo "<option value=\"$barangayOption\" " . ($barangay == $barangayOption ? "selected" : "") . ">$barangayOption</option>";
                        }
                        ?>
                    </select>
                </div>


                <div class="input-field">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?php echo $username; ?>" pattern="[a-zA-Z0-9_]{3,20}" title="Alphanumeric characters and underscore only. Length: 3-20 characters." required>
                </div>


                <div class="input-field">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>


                <div class="input-field">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" name="confirm-password" id="confirm-password" required>
                </div>


                <div class="input-field">
                    <label for="role">Role</label>
                    <select name="role" id="role" onchange="handleRoleChange()" required>
                        <option value="user">User</option>
                        <option value="employee">Employee</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>


                <div class="input-field" id="password-field" style="display: none;">
                    <label for="role-password">Role Password</label>
                    <input type="password" name="role-password" id="role-password">
                </div>

                <script>
                    function handleRoleChange() {
                        var roleSelect = document.getElementById("role");
                        var passwordField = document.getElementById("password-field");

                        passwordField.style.display = (roleSelect.value === "admin" || roleSelect.value === "employee") ? "block" : "none";
                    }
                </script>


                <div class="input-field">
                    <input type="submit" class="submit" name="submit" value="Register">
                </div>


                <?php
                if (isset($_GET['error']) && isset($errors[$_GET['error']])) {
                    echo '<div class="error-message">' . $errors[$_GET['error']] . '</div>';
                }
                ?>

                <span>
                    <a href="index.php">Cancel</a>
                </span>
            </div>
        </form>
    </div>
</body>

</html>
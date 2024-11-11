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


$baseDeliveryFee = 50;
$additionalFeePerDistance = 10;
$pricePerKilogram = 15;


$deliveryFee = $baseDeliveryFee + ($additionalFeePerDistance * calculateDistanceFromShop($userInfo['barangay']));


function calculateDistanceFromShop($userBarangay)
{

    $barangayDistances = [
        'Bagong Nayon' => 2,
        'Barangka' => 3,
        'Calantipay' => 1,
        'Catulinan' => 4,
        'Concepcion' => 2,
        'Hinukay' => 5,
        'Makinabang' => 3,
        'Matangtubig' => 1,
        'Pagala' => 0,
        'Paitan' => 4,
        'Piel' => 2,
        'Pinagbarilan' => 3,
        'Poblacion' => 1,
        'Sabang' => 4,
        'San Jose' => 3,
        'San Roque' => 2,
        'Santa Barbara' => 5,
        'Santo Cristo' => 1,
        'Santo Niño' => 3,
        'Subic' => 2,
        'Sulivan' => 4,
        'Tangos' => 1,
        'Tarcan' => 3,
        'Tiaong' => 2,
        'Tibag' => 4,
        'Tilapayong' => 5,
        'Virgen de las Flores' => 3,
    ];


    if (isset($barangayDistances[$userBarangay])) {
        return $barangayDistances[$userBarangay];
    } else {

        return 0;
    }
}


function calculateTimeDifference($pickupDateTime, $deliveryDateTime)
{
    $pickupTime = new DateTime($pickupDateTime);
    $deliveryTime = new DateTime($deliveryDateTime);


    $interval = $pickupTime->diff($deliveryTime);
    $timeDifference = $interval->h + ($interval->days * 24);

    return $timeDifference;
}


function validateDeliveryDate($deliveryDateTime, $maxDays)
{
    $currentDateTime = new DateTime();
    $deliveryTime = new DateTime($deliveryDateTime);


    $interval = $currentDateTime->diff($deliveryTime);
    $daysDifference = $interval->days;

    return $daysDifference <= $maxDays;
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_request"])) {
    $serviceType = $_POST["serviceType"];
    $pickupDateTime = $_POST["pickupDateTime"];
    $deliveryDateTime = $_POST["deliveryDateTime"];
    $weight = $_POST["weight"];
    $typeOfClothes = $_POST["typeOfClothes"];
    $detergent = $_POST["detergent"];
    $foldingStyle = $_POST["foldingStyle"];
    $address = $_POST["address"];
    $barangay = $_POST["barangay"];
    $deliveryFee = $_POST["deliveryFee"];
    $totalCost = $_POST["totalCost"];


    $minimumInterval = 5;
    $timeDifference = calculateTimeDifference($pickupDateTime, $deliveryDateTime);

    if ($timeDifference < $minimumInterval) {

        header("Location: submit_order.php?error=InvalidTimeInterval");
        exit();
    }


    $maxDeliveryDays = 3;
    if (!validateDeliveryDate($deliveryDateTime, $maxDeliveryDays)) {

        header("Location: submit_order.php?error=InvalidDeliveryDate");
        exit();
    }


    $insertOrderSQL = "INSERT INTO Request (userID, serviceType, pickupDateTime, deliveryDateTime, weight, typeOfClothes, detergent, foldingStyle, address, barangay, deliveryFee, totalCost, requestDateTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $insertOrderStmt = $conn->prepare($insertOrderSQL);
    $insertOrderStmt->bind_param("isssdsssssss", $userID, $serviceType, $pickupDateTime, $deliveryDateTime, $weight, $typeOfClothes, $detergent, $foldingStyle, $address, $barangay, $deliveryFee, $totalCost);

    if ($insertOrderStmt->execute()) {

        header("Location: order_confirmation.php");
        exit();
    } else {

        echo "Error: " . $insertOrderStmt->error;
    }

    $insertOrderStmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Order</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <header>Welcome, <?php echo $userInfo['firstName']; ?>!</header>
        </div>


        <div class="input-container">
            <form action="submit_order.php" method="post">

                <div class="input-field">
                    <label for="serviceType">Service Type</label>
                    <select name="serviceType" id="serviceType" required>
                        <option value="Regular" data-price="0">Regular</option>
                        <option value="Express" data-price="10">Express (+10 pesos)</option>
                        <option value="Premium" data-price="15">Premium (+15 pesos)</option>
                    </select>
                </div>

                <div class="input-field">
                    <label for="pickupDateTime">Pickup Date & Time</label>
                    <input type="datetime-local" name="pickupDateTime" id="pickupDateTime" required>
                </div>

                <div class="input-field">
                    <label for="deliveryDateTime">Delivery Date & Time</label>
                    <input type="datetime-local" name="deliveryDateTime" id="deliveryDateTime" required>
                </div>

                <div class="input-field">
                    <label for="weight">Weight (Minimum 5kg)</label>
                    <input type="number" name="weight" id="weight" min="5" value="5" required>
                </div>

                <div class="input-field">
                    <label for="typeOfClothes">Type of Clothes</label>
                    <select name="typeOfClothes" id="typeOfClothes" required>
                        <option value="Casual" data-price="0">Casual</option>
                        <option value="Formal" data-price="5">Formal (+5 pesos)</option>
                        <option value="Sportswear" data-price="3">Sportswear (+3 pesos)</option>
                    </select>
                </div>

                <div class="input-field">
                    <label for="detergent">Detergent</label>
                    <select name="detergent" id="detergent" required>
                        <option value="Regular" data-price="0">Regular</option>
                        <option value="Eco-friendly" data-price="8">Eco-friendly (+8 pesos)</option>

                    </select>
                </div>

                <div class="input-field">
                    <label for="foldingStyle">Folding Style</label>
                    <select name="foldingStyle" id="foldingStyle" required>
                        <option value="Normal" data-price="0">Normal</option>
                        <option value="Neatly Folded" data-price="5">Neatly Folded (+5 pesos)</option>
                        <option value="Hanger" data-price="3">On Hanger (+3 pesos)</option>
                    </select>
                </div>

                <div class="input-field">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?php echo $userInfo['address']; ?>" readonly>
                </div>

                <div class="input-field">
                    <label for="barangay">Barangay</label>
                    <input type="text" name="barangay" id="barangay" value="<?php echo $userInfo['barangay']; ?>" readonly>
                </div>

                <div class="input-field">
                    <label for="deliveryFee">Delivery Fee</label>
                    <input type="text" name="deliveryFee" id="deliveryFee" value="<?php echo $deliveryFee; ?>" readonly>
                </div>

                <div class="input-field">
                    <label>Total Cost</label>
                    <input type="text" name="totalCost" id="totalCost" value="0" readonly>
                </div>

                <div class="input-field">
                    <input type="submit" class="submit" name="submit_request" value="Submit Request">
                </div>

                <?php
                if (isset($_GET['error'])) {
                    $errorMessage = '';
                    switch ($_GET['error']) {
                        case 'InvalidTimeInterval':
                            $errorMessage = 'Min interval of date & time is 5 hours';
                            break;
                        case 'InvalidDeliveryDate':
                            $errorMessage = 'Max allowed delivery date is 3 days in the future';
                            break;
                    }

                    echo '<p class="error-message">' . $errorMessage . '</p>';
                }
                ?>
            </form>
        </div>

        <span>
            <a href="user_dashboard.php">Cancel</a>
            <a href="logout.php">Logout</a>
        </span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const totalCostInput = document.getElementById('totalCost');


            function calculateTotalCost() {
                const basePrice = parseInt(document.getElementById('serviceType').selectedOptions[0].dataset.price || 0);
                const additionalPrices = Array.from(form.querySelectorAll('[data-price]'))
                    .filter(option => option.selected)
                    .reduce((total, option) => total + parseInt(option.dataset.price), 0);

                const weight = parseInt(document.getElementById('weight').value) || 0;
                const kilogramCost = weight * <?php echo $pricePerKilogram; ?>;

                const totalCost = basePrice + additionalPrices + <?php echo $deliveryFee; ?> + kilogramCost;
                totalCostInput.value = totalCost;
            }


            calculateTotalCost();

            form.addEventListener('input', calculateTotalCost);
        });
    </script>
</body>

</html>
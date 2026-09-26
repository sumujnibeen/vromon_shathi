<?php
include 'db.php';
session_start();
include 'auth.php';
include 'is_admin.php';

if (isset($_GET['amount']) && isset($_GET['booking_id'])) {
    $amount = $_GET['amount'];
    $booking_id = (int) $_GET['booking_id'];

    echo "<div style='text-align:center; margin-top:80px;'>
            <h2>Checkout Page</h2>
            <p>Amount to Pay: <strong>BDT $amount</strong></p>
            <form method='post'>
                <input type='hidden' name='booking_id' value='$booking_id'>
                <button type='submit' name='pay' 
                        style='background-color:green;color:white;padding:10px 25px;
                        border:none;border-radius:5px;cursor:pointer;'>
                    Pay Now
                </button>
            </form>
          </div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = (int) $_POST['booking_id'];

    // Fetch booking info from DB to pre-fill customer info
    $stmt = $conn->prepare("SELECT * FROM booking WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();

    $amount = $booking['pay_amount'];

    // Prepare POST data for SSLCOMMERZ
    $post_data = array();
    $post_data['store_id'] = "vromo69117208552f5";
    $post_data['store_passwd'] = "vromo69117208552f5@ssl";
    $post_data['total_amount'] = $amount;
    $post_data['currency'] = "BDT";
    $post_data['tran_id'] = "SSLCZ_TEST_" . uniqid();
    $post_data['success_url'] = "http://localhost/dbproject/payment/success.php";
    $post_data['fail_url'] = "http://localhost/dbproject/payment/fail.php";
    $post_data['cancel_url'] = "http://localhost/dbproject/payment/cancel.php";

    // Customer info
    $post_data['cus_name'] = "Test Customer"; // You can replace with actual user info
    $post_data['cus_email'] = "test@test.com";
    $post_data['cus_add1'] = "Dhaka";
    $post_data['cus_city'] = "Dhaka";
    $post_data['cus_postcode'] = "1000";
    $post_data['cus_country'] = "Bangladesh";
    $post_data['cus_phone'] = $booking['contact_number'];

    // Optional fields
    $post_data['value_a'] = $booking_id; // pass booking_id for reference

    // API URL
    $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $direct_api_url);
    curl_setopt($handle, CURLOPT_TIMEOUT, 30);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($handle, CURLOPT_POST, 1);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE);

    $content = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

    if ($code == 200 && !(curl_errno($handle))) {
        curl_close($handle);
        $sslcommerzResponse = json_decode($content, true);
        if (isset($sslcommerzResponse['GatewayPageURL']) && $sslcommerzResponse['GatewayPageURL'] != "") {
            header("Location: " . $sslcommerzResponse['GatewayPageURL']);
            exit;
        } else {
            echo "Failed to get SSLCOMMERZ Gateway URL!";
        }
    } else {
        curl_close($handle);
        echo "Failed to connect with SSLCOMMERZ API";
        exit;
    }
}
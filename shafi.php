this is Checkout.php

in this i suppose that the payment is successful and the booking status is updated to 1

now i am calling an actual payment gateway called sslecomorez sandbox


<?php
include 'navbar.php';
include 'auth.php';
include 'is_admin.php';

if (isset($_GET['amount']) && isset($_GET['booking_id'])) {
    $amount = $_GET['amount'];
    $booking_id = (int) $_GET['booking_id'];

    echo "<div style='text-align:center; margin-top:80px;'>
            <h2>Checkout Page</h2>
            <p>Amount to Pay: <strong>$$amount</strong></p>
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

    // Payment successful simulation
    $stmt = $conn->prepare("UPDATE booking SET booking_status = 1 WHERE booking_id = ?");
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment successful. Booking confirmed.'); 
              window.location='book_vehicle_manager.php?booking_id=$booking_id';</script>";
    } else {
        echo "<script>alert('Error updating payment status.'); 
              window.location='book_vehicle_manager.php?booking_id=$booking_id';</script>";
    }
}


and the api is 

<?php 
/* PHP */
$post_data = array();
$post_data['store_id'] = "vromo69117208552f5";
$post_data['store_passwd'] = "vromo69117208552f5@ssl";
$post_data['total_amount'] = $_GET['amount'];
$post_data['currency'] = "BDT";
$post_data['tran_id'] = "SSLCZ_TEST_".uniqid();
$post_data['success_url'] = "http://localhost/dbproject/payment/success.php";
$post_data['fail_url'] = "http://localhost/dbproject/payment/fail.php";
$post_data['cancel_url'] = "http://localhost/dbproject/payment/cancel.php";
# $post_data['multi_card_name'] = "mastercard,visacard,amexcard"; # DISABLE TO DISPLAY ALL AVAILABLE

# EMI INFO
$post_data['emi_option'] = "1";
$post_data['emi_max_inst_option'] = "9";
$post_data['emi_selected_inst'] = "9";

# CUSTOMER INFORMATION
$post_data['cus_name'] = "Test Customer";
$post_data['cus_email'] = "test@test.com";
$post_data['cus_add1'] = "Dhaka";
$post_data['cus_add2'] = "Dhaka";
$post_data['cus_city'] = "Dhaka";
$post_data['cus_state'] = "Dhaka";
$post_data['cus_postcode'] = "1000";
$post_data['cus_country'] = "Bangladesh";
$post_data['cus_phone'] = "01711111111";
$post_data['cus_fax'] = "01711111111";

# SHIPMENT INFORMATION
$post_data['ship_name'] = "testvromo2weu";
$post_data['ship_add1 '] = "Dhaka";
$post_data['ship_add2'] = "Dhaka";
$post_data['ship_city'] = "Dhaka";
$post_data['ship_state'] = "Dhaka";
$post_data['ship_postcode'] = "1000";
$post_data['ship_country'] = "Bangladesh";

# OPTIONAL PARAMETERS
$post_data['value_a'] = "ref001";
$post_data['value_b '] = "ref002";
$post_data['value_c'] = "ref003";
$post_data['value_d'] = "ref004";

# CART PARAMETERS
$post_data['cart'] = json_encode(array(
array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
));
$post_data['product_amount'] = "100";
$post_data['vat'] = "5";
$post_data['discount_amount'] = "5";
$post_data['convenience_fee'] = "3";


# REQUEST SEND TO SSLCOMMERZ
$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";

$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $direct_api_url );
curl_setopt($handle, CURLOPT_TIMEOUT, 30);
curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($handle, CURLOPT_POST, 1 );
curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


$content = curl_exec($handle );

$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if($code == 200 && !( curl_errno($handle))) {
curl_close( $handle);
$sslcommerzResponse = $content;
} else {
curl_close( $handle);
echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
exit;
}

# PARSE THE JSON RESPONSE
$sslcz = json_decode($sslcommerzResponse, true );

if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
# THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
# echo "<script>
    window.location.href = '". $sslcz['
    GatewayPageURL '] ."';
</script>";
echo "
<meta http-equiv='refresh' content='0;url=".$sslcz[' GatewayPageURL']."'>";
# header("Location: ". $sslcz['GatewayPageURL']);
exit;
} else {
echo "JSON Data parsing error!";
}

?>



now place the info perfectly


and the update in database must be done in success.php

this is success.php
<?php

$val_id = urlencode($_POST['val_id']);
$store_id = urlencode("vromo69117208552f5");
$store_passwd = urlencode("vromo69117208552f5@ssl");
$requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=" . $val_id . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json");

$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $requested_url);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); # IF YOU RUN FROM LOCAL PC
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # IF YOU RUN FROM LOCAL PC

$result = curl_exec($handle);

$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

if ($code == 200 && !(curl_errno($handle))) {

    # TO CONVERT AS ARRAY
    # $result = json_decode($result, true);
    # $status = $result['status'];

    # TO CONVERT AS OBJECT
    $result = json_decode($result);

    # TRANSACTION INFO
    $status = $result->status;
    $tran_date = $result->tran_date;
    $tran_id = $result->tran_id;
    $val_id = $result->val_id;
    $amount = $result->amount;
    $store_amount = $result->store_amount;
    $bank_tran_id = $result->bank_tran_id;
    $card_type = $result->card_type;

    # EMI INFO
    $emi_instalment = $result->emi_instalment;
    $emi_amount = $result->emi_amount;
    $emi_description = $result->emi_description;
    $emi_issuer = $result->emi_issuer;

    # ISSUER INFO
    $card_no = $result->card_no;
    $card_issuer = $result->card_issuer;
    $card_brand = $result->card_brand;
    $card_issuer_country = $result->card_issuer_country;
    $card_issuer_country_code = $result->card_issuer_country_code;

    # API AUTHENTICATION
    $APIConnect = $result->APIConnect;
    $validated_on = $result->validated_on;
    $gw_version = $result->gw_version;
} else {

    echo "Failed to connect with SSLCOMMERZ";
}



now do this two page



booking table attributes:
booking_id
user_id
service_type
vehicle_id
hotel_id
guide_id
event_id
booking_date
booking_time
destination
contact_number
transaction_id
pay_amount
due_amount
payment_time
payment_status
booking_status
booked_at
created_at
updated_at
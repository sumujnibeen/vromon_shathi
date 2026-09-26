<?php
include '../db.php'; // your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $val_id = urlencode($_POST['val_id']);
    $store_id = urlencode("vromo69117208552f5");
    $store_passwd = urlencode("vromo69117208552f5@ssl");

    $requested_url = "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id={$val_id}&store_id={$store_id}&store_passwd={$store_passwd}&v=1&format=json";

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $requested_url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);

    if ($code == 200 && !(curl_errno($handle))) {

        # TO CONVERT AS OBJECT
        $result = json_decode($result);

        # TRANSACTION INFO
        $status = $result->status;
        $tran_date = $result->tran_date;
        $tran_id = $result->tran_id;
        $val_id = $result->val_id;
        $amount = $result->amount;

        # OTHER INFO
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

        if ($status === 'VALID' || $status === 'VALIDATED') {

            $booking_id = $result->value_a; // booking_id passed via value_a
            $payment_status = 1;
            $booking_status = 1;

            // Update booking table
            $stmt = $conn->prepare("UPDATE booking SET transaction_id=?, pay_amount=?, payment_time=?, payment_status=?, booking_status=? WHERE booking_id=?");
            $stmt->bind_param("sdsiii", $tran_id, $amount, $tran_date, $payment_status, $booking_status, $booking_id);

            if ($stmt->execute()) {
                echo "<script>alert('Payment Successful! Booking Confirmed.'); 
                      window.location='../book_vehicle_manager.php?booking_id={$booking_id}';</script>";
            } else {
                echo "Failed to update booking info in database.";
            }
        } else {
            echo "Payment validation failed!";
        }
    } else {
        echo "Failed to connect with SSLCOMMERZ";
    }
} else {
    echo "Invalid request method!";
}
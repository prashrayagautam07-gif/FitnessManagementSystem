<?php
require_once "../config/db.php";

$today = date("Y-m-d");

$query = "SELECT * FROM members 
          WHERE expiry_date < '$today' 
          AND status = 'Active'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {

    while ($member = mysqli_fetch_assoc($result)) {

        $token = "YOUR_API_TOKEN";
        $from  = "FitnessClub"; // Your Sender ID
        $to    = $member['phone']; // Phone column in DB

        $message = "Dear {$member['name']}, your membership expired on {$member['expiry_date']}. Please renew soon.";

        $data = [
            'token'   => $token,
            'from'    => $from,
            'to'      => $to,
            'text'    => $message
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.sparrowsms.com/v2/sms/");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        // Update status to Expired
        mysqli_query($conn, "UPDATE members 
                             SET status='Expired' 
                             WHERE member_id={$member['member_id']}");

        echo "SMS sent to {$member['name']}<br>";
    }

} else {
    echo "No expired members found.";
}
?>
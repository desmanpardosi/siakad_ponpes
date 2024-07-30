<?php
$ch = curl_init();

// Set the URL and other options
curl_setopt($ch, CURLOPT_URL, "http://192.168.1.22/his/module/EMR/setupemr/setupemr.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "page=1&rp=100&sortname=JnsKajian&sortorder=asc&exe=fn_jenis_kajian");

// Set headers if needed
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded'
));

// Return the response instead of outputting it
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
}

// Close the cURL resource
curl_close($ch);
$response = json_decode($response, true);
// Handle the response
$data = $response['rows'];
foreach($data as $d){
    echo $d['cell']['Nama']."<br/>";
}
?>

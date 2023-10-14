<?php
error_reporting(0);
set_time_limit(0);
// Replace EMAIL/API_KEY/ZONE_ID with your details.
// Zone ID is on the dashboard for the domain in the bottom right.
// Api keys are generated from the account settings. You must give cache purge permissions
// Place this script on your webserver and point a Github Webhook at it, and you'll clear
// the Cloudflare cache every time you do a push to GH.

try {
        $head = [];
        $head[] = 'Content-Type: application/json';
        $head[] = 'X-Auth-Email: tech@webscoot.io';
        $head[] = 'Authorization: Bearer v7lKzdyYv9WDDMR9Obb-fdt_zAuu6hCR0BI3LRa0';
        $head[] = 'cache-control: no-cache';

        $url = 'https://api.cloudflare.com/client/v4/zones/76f845fad1c824782e1716bc35093415/purge_cache';
        // You can also purge files like:
        // $purge = ['files' => ['example.com/styles.css']]
        $purge = ['purge_everything' => true];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($purge));
        $result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode == 200){
			echo "<br><b>Cloud Flare cache has been purge successfully..</b>";
		}else{
			echo "Sothing went worng";
		}
        curl_close($ch);
}
catch(Exception $e) {
  print($e);
}

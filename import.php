<?php

// Validate the input data. We should get a year in the format YYYY and a month in the format MM.
if(!in_array($_POST['year'], array("2015", "2014", "2013", "2012", "2011")) || !in_array($_POST['month'], array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12")))
{
    // TODO - The data isn't valid, do something.
    exit;
}

// The cookie jar to use across requests.
$mmmCookies = tempnam(sys_get_temp_dir(), 'carwings-csv-import');

// Login to Nissan. UPDATE: URL updated to HTTPS as they finally fixed that.
$response = goFetch("https://www.nissan.co.uk/GB/en/YouPlus.html/j_security_check", array(
	"j_validate" => true,
	"j_username" => $_POST['username'],
	"j_password" => $_POST['password'],
	"_charset_" => "utf8"
	));

// Grabbed from Fiddler. You have to call these or nothing works. UPDATE: Added second link which is new.
$response = goFetch("https://www.nissan.co.uk/content/GB/en/YouPlus/private/home.processafterlogin.html");
$response = goFetch("https://www.nissan.co.uk/GB/en/YouPlus/private/carwings/flashdata.testusersession.html");
$response = goFetch("https://www.nissan.co.uk/GB/en/YouPlus/private/carwings/flashdata.routeplannerredirect.html?portalType=P0001");

// Fetch the actual CSV data. Needs the month and year in the format YYYYMM. 
$response = goFetch("https://www.ev.nissanconnect.eu/EV/mycar/electric_usage_download", array("TargetMonth" => $_POST['year'] . $_POST['month']));

// You can do as you please with the data now.
var_dump($response);

// Custom function to use cookies.
function goFetch($url, $params = null)
{
	// Our cookies.
	global $mmmCookies;
	// The cURL handle.
	$ch = curl_init($url);

	// If we got some POST params to use.
	if(is_array($params))
	{
		// Sort the params.
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
	}

	// Set a modern UA string.
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2540.0 Safari/537.36"));
	// Follow redirects if we get them.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	// Return the output as a string instead of outputting it.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// How long to wait before we give up on Nissan.
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	// Where to save cookies when we're done.
	curl_setopt($ch, CURLOPT_COOKIEJAR, $mmmCookies);
	// Which cookies to use.
	curl_setopt($ch, CURLOPT_COOKIEFILE, $mmmCookies);

	// Execute the request.
	$response = curl_exec($ch);

	// If cURL returned an error.
	if(curl_error($ch) != ""){
        // TODO - The request failed. Do something.
		exit;
	}
	
	// Return the response from Nissan.
	return $response;
}

?>

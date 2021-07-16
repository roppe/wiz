<?php

	session_start();

	echo "Getting Dispatch Location....<br>";

	define('CAPTCHA_SECRET', '#recaptchasecret'); 

	$EmailFrom = "leads@tubreglazingnj.com";

	$EmailTo = "info@tubreglazingnj.com";

	$Subject = "Lead from:  tubreglazingnj.com";

	

	

	$Name = Trim(stripslashes($_POST['Name'])); 

	$Phone = Trim(stripslashes($_POST['Phone'])); 

	$Email = Trim(stripslashes($_POST['Email'])); 

	$subject = Trim(stripslashes($_POST['Subject'])); 

	$Comments = Trim(stripslashes($_POST['Comments'])); 

	
	function verifyCaptcha(){

		$post_data = http_build_query(

			array(

				'secret' => CAPTCHA_SECRET,

				'response' => $_POST['g-recaptcha-response'],

				'remoteip' => $_SERVER['REMOTE_ADDR']

				)

		);

		$opts = array('http' =>

		array(

        'method'  => 'POST',

        'header'  => 'Content-type: application/x-www-form-urlencoded',

        'content' => $post_data

		)

		);

		$context  = stream_context_create($opts);

		$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);

		$result = json_decode($response);

		

		if ($result->success) {

			return true;

		} else {

			return false;

		}

	}

	$antispam = verifyCaptcha(); 

	

	function ip_info($ip = NULL, $purpose = "Dispatch Location", $deep_detect = TRUE) {

		$output = NULL;

		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {

			$ip = $_SERVER["REMOTE_ADDR"];

			if ($deep_detect) {

				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))

                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))

                $ip = $_SERVER['HTTP_CLIENT_IP'];

			}

		}

		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));

		$support    = array("country", "countrycode", "state", "region", "city", "Dispatch Location", "address");

		$continents = array(

        "AF" => "Africa",

        "AN" => "Antarctica",

        "AS" => "Asia",

        "EU" => "Europe",

        "OC" => "Australia (Oceania)",

        "NA" => "North America",

        "SA" => "South America"

		);

		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {

			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));

			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {

				switch ($purpose) {

					case "Dispatch Location":

                    $output = array(

					"city"           => @$ipdat->geoplugin_city,

					"state"          => @$ipdat->geoplugin_regionName,

					"country"        => @$ipdat->geoplugin_countryName,

					"country_code"   => @$ipdat->geoplugin_countryCode,

					"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],

					"continent_code" => @$ipdat->geoplugin_continentCode

                    );

                    break;

					case "address":

                    $address = array($ipdat->geoplugin_countryName);

                    if (@strlen($ipdat->geoplugin_regionName) >= 1)

					$address[] = $ipdat->geoplugin_regionName;

                    if (@strlen($ipdat->geoplugin_city) >= 1)

					$address[] = $ipdat->geoplugin_city;

                    $output = implode(", ", array_reverse($address));

                    break;

					case "city":

                    $output = @$ipdat->geoplugin_city;

                    break;

					case "state":

                    $output = @$ipdat->geoplugin_regionName;

                    break;

					case "region":

                    $output = @$ipdat->geoplugin_regionName;

                    break;

					case "country":

                    $output = @$ipdat->geoplugin_countryName;

                    break;

					case "countrycode":

                    $output = @$ipdat->geoplugin_countryCode;

                    break;

				}

			}

		}

		return $output;

	}

	

?>

<?

	echo ip_info("Visitor", "Address");

	echo "<br><br>"

?>



<?php

	

	

	// validation

	$validationOK=true;

	if (Trim($Name)=="") $validationOK=false;

	if (Trim($Email)=="") $validationOK=false;

	if (Trim($Phone)=="") $validationOK=false;

	if (Trim($Comments)=="") $validationOK=false;

	if (!$antispam) $validationOK=false;

	if (!$validationOK) {

		print "<meta http-equiv=\"refresh\" content=\"0;URL=error.html\">";

		exit;

	}

	// prepare email body text

	$Body = "";

	$Body .= "Name: ";

	$Body .= $Name;

	$Body .= "\n";

	$Body .= "\n";

	$Body .= "Phone: ";

	$Body .= $Phone;

	$Body .= "\n";

	$Body .= "\n";

	$Body .= "Email: ";

	$Body .= $Email;

	$Body .= "\n";

    $Body .= "\n";
    
	$Body .= "Subject: ";

	$Body .= $subject;

	$Body .= "\n";

	$Body .= "\n";

	$Body .= "Questions/Comments: ";

	$Body .= $Comments;

	$Body .= "\n";

	$Body .= "\n";

	$Body .= "Visitor's Dispatch Location: ";

	$Body .= ip_info("Visitor", "Address");

	echo "Submitting form...<br>";

	

	

	// send email 

	$success = mail($EmailTo, $Subject, $Body, "From: <$EmailFrom>");

	

	// redirect to success page 

	if ($success){

		print "<meta http-equiv=\"refresh\" content=\"0;URL=contactthanks.php\">";

	}

	else{

		print "<meta http-equiv=\"refresh\" content=\"0;URL=error.html\">";

	}

?>
<?php
require_once "recaptchalib.php";
$secret = "6LcUZ7EUAAAAANdb5ZwYL4wASJ5n1B2AxDLvOCdr";
$response = null;
$reCaptcha = new ReCaptcha($secret);
if ($_POST["g-recaptcha-response"]) {
    $response = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $_POST["g-recaptcha-response"]
    );
}
?>

<?php
if ($response != null && $response->success) {

$EmailFrom = "feedback";
$EmailTo = "PLACE EMAL";
$Subject = "Lead From http://blueplanetexterminating.com";
$Name = Trim(stripslashes($_POST['Name'])); 
$Email = Trim(stripslashes($_POST['Email'])); 
$Phone = Trim(stripslashes($_POST['Phone'])); 
$Time = Trim(stripslashes($_POST['Time'])); 
$Date = Trim(stripslashes($_POST['Date'])); 
$Message = Trim(stripslashes($_POST['Message'])); 

	// validation
	$validationOK=true;
	if (Trim($Name)=="") $validationOK=false;
	if (Trim($Email)=="") $validationOK=false;
	if (Trim($Phone)=="") $validationOK=false;
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
$Body .= "Email: ";
$Body .= $Email;
$Body .= "\n";
$Body .= "\n";
$Body .= "Phone: ";
$Body .= $Phone;
$Body .= "\n";
$Body .= "\n";
$Body .= "Time: ";
$Body .= $Time;
$Body .= "\n";
$Body .= "\n";
$Body .= "Date: ";
$Body .= $Date;
$Body .= "\n";
$Body .= "\n";
$Body .= "Message: ";
$Body .= $Message;
$Body .= "\n";
$Body .= "\n";

// send email 
$success = mail($EmailTo, $Subject, $Body, "From: <$Email>");

	// redirect to success page 
	if ($success){
	  print "<meta http-equiv=\"refresh\" content=\"0;URL=contact-thanks.html\">";
	}
	else{
	  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.html\">";
		}

  } else {
  
  echo "you are not human"; die;
}
	
 ?>

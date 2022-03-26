<?php
polyfill();
if (empty($_GET)) {
	echo "<meta charset=\"utf-8\">
			<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
			<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css\">
			<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js\"></script>
			<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js\"></script>
			<title>Let Me Sleep - Course Schedule Generator</title>
	<form action=\"\" method=\"get\"><br><br>
		<center><img src='inline.jpg'><br><br><h1 style='color: #115740'>Let Me Sleep</h1><h3 style='color: #B9975B'>Course Schedule Optimization For Night Owls At W&M</h3>
		<label style='color: #115740' for='name'>Name: (Your Name)</label>
		<input class='form-control' type='text' name='name' id='name' style='width:50%' required></input>
		<label style='color: #115740' for='term'>Term: (Select One)</label>
		<select class='form-control' name='term' id='term' style='width:50%' required>";
		$token = login();
		$terms = getData("https://openapi.it.wm.edu/courses/production/v1/activeterms", "", "GET", $token);
		$terms = json_decode($terms);
		foreach ($terms as $term) {
			echo "<option value='".$term->{'TERM_CODE'}."'>".$term->{'TERM_DESC'}."</option>";
		}
		echo "</select>
		<label style='color: #115740' for='subject'>Subjects: (Select All That Apply)</label>
		<select class='form-control' name='subject[]' id='subject' style='width:50%' multiple required>";
		$terms = getData("https://openapi.it.wm.edu/courses/production/v1/subjectlist", "", "GET", $token);
		$terms = json_decode($terms);
		foreach ($terms as $term) {
			echo "<option value='".$term->{'STVSUBJ_CODE'}."'>".$term->{'STVSUBJ_DESC'}." [".$term->{'STVSUBJ_CODE'}."]</option>";
		}
		echo "</select>
		<label style='color: #115740' for='level'>Level: (Select One)</label>
		<select class='form-control' name='level' id='level' style='width:50%' required>
			<option value='UG'>Undergraduate</option>
			<option value='GA'>Graduate - Arts & Sciences</option>
			<option value='GB'>Graduate - Business</option>
			<option value='GE'>Graduate - Education</option>
			<option value='GM'>Graduate - Marine Science</option>
			<option value='LW'>Law</option>
		</select>
		<label style='color: #115740' for='time'>Earliest Start Time: (Select One)</label>
		<select class='form-control' name='time' id='time' style='width:50%' required>
			<option value='0800'>8:00 AM</option>
			<option value='0900'>9:00 AM</option>
			<option value='1000'>10:00 AM</option>
			<option value='1100'>11:00 AM</option>
			<option value='1200'>12:00 PM</option>
			<option value='1300'>1:00 PM</option>
		</select>
		<br>
		<input class='form-control' type='submit' style='width:50%' value='Generate Schedule'/>
	</form>
	</center>
	";
} else {

require('libraries/fpdf/fpdf.php');

class PDF extends FPDF {

	function Footer() {
		$this->SetY(-0.5,true);
		$this->SetFont('Arial','I',8);
		$this->Image('WM.png',0.35,10.25,0.5);
		$this->Cell(0,0.25,'LET ME SLEEP',0,0,'L');
		$this->SetX(0);
		$this->Cell(0,0.25,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		$this->Cell(0,0.25,'Generated '.date("m/d/Y H:i:s T"),0,0,'R');
	}
}

date_default_timezone_set('America/New_York');

$pdf = new PDF('P','in','Letter');
$pdf->AliasNbPages();
$pdf->SetTitle('Let Me Sleep: '.date("m/d/Y"));
$pdf->SetAuthor('LetMeSleep');
$pdf->SetSubject('No more early morning classes.');
$pdf->SetCreator('LetMeSleep v1.0.0/FPDF 1.84');
$pdf->setMargins(0.25,0.25);
$pdf->AddPage();
$pdf->SetFont('Arial','B',32);
$pdf->Cell(0,0.75,'Let Me Sleep',"L");
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,0.75,$_GET["name"],0,1,"R");
$pdf->Cell(0,0.25,'Course Schedule',"L",1);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(0,0.25,'Optimized for night owls',"L");
$pdf->Image('primary.jpg',2.25,3.75,4);

//pages go here
generateSched($pdf);

$pdf->Output('F','C:/wamp64/www/letmesleep/out/latest.pdf');
$pdf->Output('F','C:/wamp64/www/letmesleep/out/'.uniqid().'.pdf');
$pdf->Output();
sendReport();
}
function generateSched($pdf) {
	$selected_courses = array();
	$warnings = array();
	$token = login();
	$term = $_GET["term"];
	$level = $_GET["level"];
	$time = $_GET["time"];
	foreach ($_GET["subject"] as $sub) {
		$found = false;
		$courses = getData("https://openapi.it.wm.edu/courses/production/v1/opencourses/".$sub."/".$term, "", "GET", $token);
		$courses = json_decode($courses);
		foreach ($courses as $course) {
			if ($course->{'OPEN_CLOSED'} == "OPEN" && $course->{'CRS_DAYTIME'} != "Not Available") {
				if (str_contains($course->{'CRS_LEVL'}, $level)) {
					if (explode("-", explode(":", $course->{'CRS_DAYTIME'})[1])[0] >= $time) {
						$found = true;
						$selected_courses["$sub"][] = $course;
					}
				}
			}
		}
		if (!$found) {
			$warnings[] = "No Courses Found For ".$sub;
		}
	}
	//var_dump($selected_courses);
	$pdf->AddPage();
	//content of schedule goes here
	$pdf->SetTextColor(255,0,0);
	$pdf->SetFont('Arial','B',8);
	foreach ($warnings as $warning) {
		$pdf->Cell(0,0.25,$warning,0,1);
	}
	$pdf->SetTextColor(0,0,0);
	$selected = array();
	foreach ($selected_courses as $sub) {
		$sc = array();
		foreach ($sub as $course) {
			$sc[] = array("Name"=>$course->{'TITLE'}, "ID"=>$course->{'CRN_ID'}, "Subject"=>$course->{'SUBJECT_CODE'}, "Time"=>$course->{'CRS_DAYTIME'}, "Credits"=>$course->{'CREDIT_HRS'}, "Instructor"=>$course->{'INSTRUCTOR'});
		}
		$selected[] = $sc;
	}
	$selected = selectFinal(null,$selected);
	outputDay($selected, "Monday", "M", $pdf);
	outputDay($selected, "Tuesday", "T", $pdf);
	outputDay($selected, "Wensday", "W", $pdf);
	outputDay($selected, "Thursday", "R", $pdf);
	outputDay($selected, "Friday", "F", $pdf);
}

function selectFinal ($options,$remaining,$selected = array()) {
	if ($options != null) {
		foreach ($options as $option) {
			if (!isConflicting($option, $selected)) {
				$selected[] = $option;
				break;
			}
		}
	}
	if (is_array($remaining) && (count($remaining) > 1)) {
		$options = $remaining[0];
		$remaining = array_slice($remaining,1);
		return selectFinal($options,$remaining,$selected);
	} elseif ($remaining != null) {
		$options = $remaining[0];
		return selectFinal($options,null,$selected);
	}
	return $selected;
}
function isConflicting($course, $selected) {
	foreach (str_split(explode(":", $course['Time'])[0]) as $day) {
		foreach ($selected as $select) {
			if (in_array($day, str_split(explode(":", $select['Time'])[0]))) {
				$selstart = explode("-", explode(":", $select['Time'])[1])[0];
				$selend = explode("-", explode(":", $select['Time'])[1])[1];
				$courstart = explode("-", explode(":", $course['Time'])[1])[0];
				$courend = explode("-", explode(":", $course['Time'])[1])[1];
				if ($courstart <= $selend && $courstart >= $selstart) {
					return true;
				}
				if ($courend <= $selend && $courend >= $selstart) {
					return true;
				}
				if ($courstart <= $selstart && $courend >= $selend) {
					return true;
				}
			}
		}
	}
	return false;
}
function outputDay($courses, $dayTitle, $day, $pdf) {
	$found = false;
	$pdf->SetFont('Arial','BU',12);
	$pdf->Cell(0,0.25,"Courses for ".$dayTitle,0,1);
	$pdf->SetFont('Arial','B',8);
	$courseStart = array();
	foreach ($courses as $course) {
		$courseStart[] = explode("-", explode(":", $course['Time'])[1])[0];
	}
	array_multisort($courseStart, SORT_ASC, $courses);
	foreach ($courses as $course) {
		$days = explode(":", $course['Time'])[0];
		if (str_contains($days, $day)) {
			$found = true;
			$pdf->Cell(0,0.25,$course['Name']." [".$course['Time']."]",0,1);
		}
	}
	if (!$found) {
		$pdf->SetTextColor(255,0,0);
		$pdf->Cell(0,0.25,"No courses scheduled for ".$dayTitle,0,1);
		$pdf->SetTextColor(0,0,0);
	}
}

function login() {
	$login = "
		{
			\"client_id\": \"tribehacks\",
			\"secret_id\": \"E74euRJIhUcoJAf2nCDXDM8hE45KQPBOvuq7bkRRisKxb\"
		}
	";
	$token = getData("https://openapi.it.wm.edu/auth/v1/login", $login);
	$token = json_decode($token);
	return $token->{'access_token'};
}

function getData($url, $body, $type = "POST", $token = "") {
	$curl = curl_init();
	$headers = array(
		'Accept: application/json',
		'Content-Type: application/json',
		'Authorization: Bearer '.$token.'',
    );
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, 'LetMeSleep/1');
	curl_setopt($curl, CURLOPT_AUTOREFERER, true); //we don't really need this
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 180);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type); 
	curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

	$html = curl_exec($curl);
	if(curl_error($curl)) {
		echo "ERROR ERROR ERROR<br>";
		echo curl_error($curl);
	}
	curl_close($curl);

	return $html;
}

function polyfill() {
	//polyfill
	if (!function_exists('str_contains')) {
		function str_contains($haystack, $needle) {
			return $needle !== '' && mb_strpos($haystack, $needle) !== false;
		}
	}
}
?>
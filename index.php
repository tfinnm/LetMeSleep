<?php
require('libraries/fpdf/fpdf.php');

class PDF extends FPDF {

	var $angle=0;
	function Rotate($angle,$x=-1,$y=-1) {
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0) {
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function Footer() {
		$this->SetY(-0.5,true);
		$this->SetFont('Arial','I',8);
		$this->Image('WM.png',0.5,10,0.5);
		$this->Cell(0,0.25,'LET ME SLEEP',0,0,'L');
		$this->SetX(0);
		$this->Cell(0,0.25,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		$this->Cell(0,0.25,'Generated '.date("m/d/Y H:i:s T (O)"),0,0,'R');
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
$pdf->Cell(0,0.75,'Let Me Sleep',"L",1);
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,0.25,'Course Schedule',"L");
$pdf->SetFont('Arial','B',8);
$pdf->Cell(0,0.25,'Optimized for night owls',"L");

//pages go here
generateSched($pdf);

$pdf->Output('F','C:/wamp64/www/letmesleep/latest.pdf');
$pdf->Output('F','C:/wamp64/www/letmesleep/'.uniqid().'.pdf');
$pdf->Output();
sendReport();

function generateSched($pdf) {
	$login = "
		{
			\"client_id\": \"tribehacks\",
			\"secret_id\": \"E74euRJIhUcoJAf2nCDXDM8hE45KQPBOvuq7bkRRisKxb\"
		}
	";
	$token = getData("https://openapi.it.wm.edu/auth/v1/login", $login);
	$token = json_decode($token);
	$token = $token->{'access_token'};
	$courses = getData("https://openapi.it.wm.edu/courses/development/v1/coursesections/202220", "", "GET", $token);
	var_dump($courses);
	$xml = json_decode($courses);
	die();
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	$pdf->AddPage();
	//content of schedule goes here
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
?>
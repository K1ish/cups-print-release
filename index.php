<?php
// CUPS print release kiosk with PHP
// (C)2019 Keegan Harris

?>
<!DOCTYPE html>
<html lang="en">
<!-- MADE BY KEEGAN -->
<head>
<meta charset="UTF-8">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>PrintRelease</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
<link rel='stylesheet' href='https://bradfrost.github.com/this-is-responsive/styles.css'>
<link rel="stylesheet" href="css/style.css">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<?php
// MADE BY KEEGAN
// test
$n=10;
function getName($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
}

$randuri = getName($n);
if(isset($_POST['release'])) {
echo "<meta http-equiv='refresh' content='2;url=/index.php?$randuri' />";
} else {
echo "<meta http-equiv='refresh' content='5;url=/index.php?$randuri' />";
}
?>
</head>
<body>
<table class="dataTable">
        <thead>
                <tr>
                        <th>ID</th>
                        <th>File Name</th>
                        <th>User</th>
                        <th>Action</th>
                </tr>
        </thead>
        <tbody>
<?php

include 'vendor/autoload.php';

use Smalot\Cups\Builder\Builder;
use Smalot\Cups\Manager\JobManager;
use Smalot\Cups\Manager\PrinterManager;
use Smalot\Cups\Transport\Client;
use Smalot\Cups\Transport\ResponseParser;

$client = new Client();
$builder = new Builder();
$responseParser = new ResponseParser();
$printerManager = new PrinterManager($builder, $client, $responseParser);
// end init
$printer = $printerManager->findByUri('ipp://localhost:631/printers/MHS-1600-LJ4250');
$cprinter = $printerManager->findByUri('ipp://localhost:631/printers/MHS2-1600-CLJ4700');

$jobManager = new JobManager($builder, $client, $responseParser);
// $printers = $printerManager->getList();

$it = 0;


if ($it == 0) {
$it = 1;

// Get jobs from normal printer
$jobs = $jobManager->getList($printer, false, 0, 'active');
// Get jobs from color printer
$cjobs = $jobManager->getList($cprinter, false, 0, 'active');

// Loop through jobs
if (empty($jobs) && empty($cjobs)) {
echo "To print: tap the share arrow, and hit print, select the BW PRINTER! Unless you NEED COLOR! Then wait for it to show here.";
}
foreach ($jobs as $job) {
    echo '<tr>';
    echo '<td>'.$job->getId().'</td><td>'.$job->getName().'</td><td>'.$job->getUsername() . '</td><td><form method="post"><input type="hidden" name="release" value="'. $job->getId()  . '"><input type="hidden" name="printer" value="1"><input type="submit" value="Release"> </form></td>';
    $jobarr[$job->getId()] = $job;
    echo '</tr>';
}
foreach ($cjobs as $cjob) {
    echo '<tr>';
    echo '<td>' . $cjob->getId() . '</td><td>' . $cjob->getName() . '</td><td>' . $cjob->getUsername() . '</td><td><form method="post"><input type="hidden" name="printer" value="2"><input type="hidden" name="release" value="' . $cjob->getId() . '"><input type="submit" value="release"></form>';
    $cjobarr[$cjob->getId()] = $cjob;
    echo '</tr>';
}

}

if(isset($_POST['release'])) {
$thajob = $_POST['release'];
if ($_POST['printer'] == "1") {
$thisjob = $jobarr[$thajob];
}
if ($_POST['printer'] == "2") {
$thisjob = $cjobarr[$thajob];
}
// Hostname lookup
//$gamarr = $thisjob->getAttributes();
//$gam2 = $gamarr['job-originating-host-name'];
//$ip = $gam2[0];
//$ex = "nslookup " . $ip;

//echo "<p>";

//$out = exec($ex, $outp);
//$out2 = $outp[0];
//$outp2 = explode(' = ', $out2);

// $host1 = $out.split("=", $out);
// print_r($outp2);
//echo $outp2[1];
//echo "</p>";
// print_r($gam2[0]);
$jobManager->release($thisjob);

}
?>
</tbody>
</table>
</body>
</HTML>

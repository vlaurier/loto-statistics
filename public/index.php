<?php

require __DIR__.'/../private/bootstrap.php';
exit;
/*
  $file = 'https://media.fdj.fr/generated/game/loto/nouveau_loto.zip';
  $newfile = 'zip/nouveau_loto.zip';

  if (!copy($file, $newfile)) {
  echo "La copie $file du fichier a échoué...\n";
  }
  $zip = new ZipArchive;
  $res = $zip->open('loto.zip');
  if ($res === TRUE) {
  echo 'ok';
  $zip->extractTo('csv');
  $zip->close();
  } else {
  echo 'échec, code:' . $res;
  } */


// Try to open the csv file
if (($handle = fopen("csv/nouveau_loto.csv", "r")) === FALSE) {
    throw new Exception('Could not open file');
}

// Fetch informations from file
$aCsvLines = array();
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $aCsvLines[] = $data;
}
// ... and close connection.
fclose($handle);

// Retrieve fields in array
$aFields = array_shift($aCsvLines);
$aFields = explode(';', $aFields[0]);

// Store all results
$aResults = array();

foreach ($aCsvLines as $result) {
    $stringValue = '';
    foreach ($result as $line) {
        $stringValue.=$line;
    }
    $aResults[] = array_combine($aFields, explode(';', $stringValue));
}

echo "<pre>";
var_dump($aResults);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        ?>
    </body>
</html>

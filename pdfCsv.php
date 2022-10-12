
<?php

include 'vendor/autoload.php';


function pdfToCsv($sourceFile, $outputFilename)
{

    $config = new \Smalot\PdfParser\Config();
    $config->setFontSpaceLimit(-60);
    $config->setRetainImageContent(false);
    $config->setHorizontalOffset("\t");

    $parser = new \Smalot\PdfParser\Parser([], $config);

    // $pdf = $parser->parseFile('./Ordre_de_transport__29.06.2022_Définitif(1)_date_01_07_2022_62bf0908232a9.pdf');
    $pdf = $parser->parseFile($sourceFile);


    $text = $pdf->getText();
    // $pdfText = nl2br($text);


    $rows = explode("\n", $text);

    // print_r($rows);
    //
    $data = array();


    //header 
    array_push($data, ['Commande du :', $rows[3], '', '']);
    array_push($data, ['A effectuer le : :', $rows[4], '', '']);
    array_push($data, [$rows[5], '', '', '', '', '']);

    $titleINrow6 = explode("\t", $rows[6]);
    // $titleINrow7 = explode("\t", $rows[7]);

    // print_r($titleINrow6);

    //get title
    array_push($data, $titleINrow6);


    // the numbers are according to pdfParser array 
    for ($i = 7; $i < count($rows) - 5; $i += 2) {
        $value1 = explode("\t", $rows[$i]);
        $value2 = explode("\t", $rows[$i + 1]);


        if (is_numeric($value2[0])) {
            array_push($data, [$value1[0],  $value2[0], $value2[1], $value2[2]]);
        }
        if (!is_numeric($value2[0])) {
            array_push($data, [$value1[0],  '', '', '']);
            $i -= 1;
        }
        if ($value1[0] == 'LONAY') {
            break;
        }
    }

    // print_r($data);
    $dataJson = json_encode($data);
    $jsonData = json_decode($dataJson, true);

    // CSV file name => filename.csv
    $csv = $outputFilename . '.csv';

    // File pointer in writable mode
    $file_pointer = fopen($csv, 'w');

    // Traverse through the associative
    // array using for each loop
    foreach ($jsonData as $i) {

        // Write the data to the CSV file
        fputcsv($file_pointer, $i);
    }

    // Close the file pointer.
    fclose($file_pointer);
}

// pdfToCsv($sourceFile = './Ordre de transport  27.01.2021 Définitif.pdf', $outputFilename = 'smaple3');


// will get sample.pdf and extract it to sample.csv

foreach (glob("./*.pdf") as $file) {
    $outputFilename =  substr($file, 0, -4);  // remove .pdf from file name

    pdfToCsv($sourceFile = $file, $outputFilename);
}
?>
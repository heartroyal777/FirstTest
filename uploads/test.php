// sample
<?php

$url = "https://2022.myanmarexam.org/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);

$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$tableRows = $xpath->query("//table[@class='table']//tbody/tr");

$result = array();

foreach ($tableRows as $row) {
    $rowData = new stdClass();
    $rowData->no = $row->childNodes[0]->textContent;
    $rowData->name = $row->childNodes[1]->textContent;
    $regin_url = $row->childNodes[1]->childNodes[0]->getAttribute('href');
    
    $cities = array();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,  $url.$regin_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    $tableRows = $xpath->query("//table[@class='table']//tbody/tr");
    
    if ($tableRows->length == 0) {
        $cities[] = $regin_url;
    } else {
        foreach ($tableRows as $row) {
            $cityData = new stdClass();
            $cells = $row->getElementsByTagName('td');
            $cityData->no = $cells->item(1)->textContent;
            $cityData->name = $cells->item(2)->textContent;
            $cityData->table_no = $cells->item(3)->textContent;
            $lastCell = $cells->item(4);
            $a = $lastCell->getElementsByTagName('a')->item(0);
            $cityData->pdf_url = $a->getAttribute('href');
            $cities[] = $cityData;
        }
    }
    $rowData->cities = $cities;
    
    $result[] = $rowData;
}

$json = json_encode($result, JSON_PRETTY_PRINT);
echo '<pre>' . $json . '</pre>';

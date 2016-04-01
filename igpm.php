<?php
libxml_use_internal_errors(true);
$f = file_get_contents('http://www.portalbrasil.net/igpm.htm');
if($f === false) {
	throw new Exception("Não foi possível recuperar a tabela em http://www.portalbrasil.net/igpm.htm", 1);	
}

$d = new DOMDocument();
$d->loadHTML($f);


$elements = $d->getElementsByTagName('table');
$ultimo = null;
foreach($elements as $element) {
	$ultimo = $element;
}

$trs = $ultimo->getElementsByTagName('tr');

$dados = [];
foreach($trs as $tr) {
	$tds = $tr->getElementsByTagName('td');
	
	$anual = [];
	foreach($tds as $i=>$td) {
		$anual[$i] = trim($td->nodeValue);
	}
	$dados[] = $anual;
}

$linhas = "";
//remove cabeçalho
unset($dados[0]);
foreach($dados as $dado) {
	for($i = 1; $i <= 12; $i++) {
		$taxa = str_replace(',', '.', $dado[$i]) * 1;
		$linhas .= $dado[0] . "\t" . $i . "\t" . number_format(round($taxa / 100, 8), 8, ",", "") . "\n";
	}
}

file_put_contents('igpm.csv', $linhas);
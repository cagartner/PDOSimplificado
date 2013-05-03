<?php

require_once "PDOS.class.php";

$host = 'localhost';
$user = 'root';
$pass = 'java2012';
$dbname = 'carlos';

$pdos = new PDOS($host, $user, $pass, $dbname);

$tabelas = array(
	'name' => 'usuario AS a',
	'join' => array(
		0 => array(
			'name' => 'cargo AS b',
			'on' => 'a.id = b.id',
			'type' => 'left outer'
		)
	)
);

$tabelas = "usuario";

//$where = array('nom_estado' => '%santa%');

// try {
// 	$results = $pdos->getAll($tabelas, array('id' => 1, 'nome' => '%Carlos'), array('nome'), 'object');

// 	$pdos->getDebug();

// 	echo "<pre>";
// 	print_r($results);
// 	echo "</pre>";
// } catch (Exception $e) {
// 	echo $e->getMessage();
// }
// 
// 
$dados = array('nome' => 'Carlos Augusto', 'email' => 'carlos@we3onlinecom.br');
try {
	$results = $pdos->add($tabelas, $dados);

	$pdos->getDebug();

	echo "<pre>";
	print_r($results);
	echo "</pre>";
} catch (Exception $e) {
	echo $e->getMessage();
}
exit;



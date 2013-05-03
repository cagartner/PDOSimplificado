<?php

require_once "PDOS.class.php";

$host   = 'host';
$user   = 'usuario';
$pass   = 'sua_senha';
$dbname = 'nome_bd';

$pdos = new PDOS($host, $user, $pass, $dbname);

/*================================================
=            Busca todos os registros            =
================================================*/
$tabela     = "usuario";
$colunas    = array('nome', 'email');
$parametros = array('status' => 1);
$retorno    = 'object'; // Pode usar 'coluna', 'object', 'array'

try {
	$results = $pdos->getAll($tabela, $parametros, $colunas, $retorno);

	echo "<pre>";
	print_r($results);
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}

echo "<hr>";

/*================================================
=            Busca apenas um registro            =
================================================*/
$tabela     = "usuario";
$colunas    = array('nome', 'email');
$parametros = array('status' => 1);
$retorno    = 'collumn'; // Pode usar 'collumn', 'object', 'array'

try {
	$results = $pdos->getOne($tabela, $parametros, $colunas, $retorno);

	echo "<pre>";
	print_r($results);
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}



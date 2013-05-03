<?php

require_once "PDOS.class.php";

$host   = 'host';
$user   = 'usuario';
$pass   = 'sua_senha';
$dbname = 'nome_bd';

$pdos = new PDOS($host, $user, $pass, $dbname);

/*================================================
=           		Método delete          		 =
================================================*/
$tabela     = "usuario";
$condicoes = array(
	'id' => 1
);

try {
	$results = $pdos->delete($tabela, $condicoes);

	echo "<pre>";
	print_r($results); // Retorna o true caso sucesso
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}

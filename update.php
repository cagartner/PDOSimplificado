<?php

require_once "PDOS.class.php";

$host   = 'host';
$user   = 'usuario';
$pass   = 'sua_senha';
$dbname = 'nome_bd';

$pdos = new PDOS($host, $user, $pass, $dbname);

/*================================================
=           		Método update          		 =
================================================*/
$tabela     = "usuario";
$dados      = array(
	'nome'    => 'João',
	'email'   => 'joao@email.com',
	'idcargo' => 2
);
$condicoes = array(
	'id' => 1
);

try {
	$results = $pdos->update($tabela, $dados, $condicoes);

	echo "<pre>";
	print_r($results); // Retorna o true caso sucesso
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}

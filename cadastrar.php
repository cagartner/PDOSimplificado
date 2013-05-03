<?php

require_once "PDOS.class.php";

$host   = 'host';
$user   = 'usuario';
$pass   = 'sua_senha';
$dbname = 'nome_bd';

$pdos = new PDOS($host, $user, $pass, $dbname);

/*================================================
=           		Função add           		 =
================================================*/
$tabela     = "usuario";
$dados      = array(
	'nome'    => 'João',
	'email'   => 'joao@email.com',
	'idcargo' => 2
);

try {
	$results = $pdos->add($tabela, $dados);

	echo "<pre>";
	print_r($results); // Retorna o id do registro inserido
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}

echo "<hr>";

/*================================================
=            		Método save 	             =
================================================*/
// Esse método verifica primeiro se existe algum registro com os dados inseridos, se existir ele 
// faz update, caso não exista, faz o cadastro
$tabela     = "usuario";
$dados      = array(
	'nome'    => 'João',
	'email'   => 'joao@email.com',
	'idcargo' => 2
);

try {
	$results = $pdos->save($tabela, $dados, $dados); // Nesse caso é preciso passar um segundo parametros para verificação.

	echo "<pre>";
	print_r($results); // Retorna o id do registro inserido, caso seja o caso.
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}
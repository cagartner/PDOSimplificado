<?php

require_once "PDOS.class.php";

$host   = 'host';
$user   = 'usuario';
$pass   = 'sua_senha';
$dbname = 'nome_bd';

$pdos = new PDOS($host, $user, $pass, $dbname);

/*===============================================
=            Busca avancada com join            =
===============================================*/
$tabela     = array(
	'name' => 'usuario AS a',
	'join' => array(
		0 => array(
			'name' => 'cargo AS b',
			'type' => 'inner', // 'left', 'left outer', 'right', 'right outer'
			'on' => 'a.idcargo = b.id'
		)
	)	
);
$colunas    = array(
	'a.nome AS nomeUsuario', 
	'a.email', 
	'b.nome AS nomeCargo'
);
$parametros = array('a.status' => 1);
$retorno    = 'object'; // Pode usar 'coluna', 'object', 'array'

try {
	$results = $pdos->getAll($tabela, $parametros, $colunas, $retorno);
	echo "<pre>";
	echo "<h3>Busca avancada com join</h3>";
	print_r($results);
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}

echo "<hr>";

/*=====================================================
=         Busca em intervalo de tempo 'between'       =
=====================================================*/

$tabela     = array(
	'name' => 'usuario AS a',
	'join' => array(
		0 => array(
			'name' => 'cargo AS b',
			'type' => 'inner', // 'left', 'left outer', 'right', 'right outer'
			'on' => 'a.idcargo = b.id'
		)
	)	
);
$colunas    = array('a.nome AS nomeUsuario', 'a.email', 'b.nome AS nomeCargo', 'a.datacadastro');
$parametros = array(
	'a.status' => 1,
	'a.datacadastro' => array('between' => array('2013-01-01', '2014-01-01'))
);
$retorno    = 'object'; // Pode usar 'coluna', 'object', 'array'

try {
	$results = $pdos->getAll($tabela, $parametros, $colunas, $retorno);
	echo "<pre>";
	echo "<h3>Busca em intervalo de tempo 'between'</h3>";
	print_r($results);
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}

echo "<hr>";

/*=====================================================
=   Verificação com vários valores válidos 'in'       =
=====================================================*/

$tabela     = array(
	'name' => 'usuario AS a',
	'join' => array(
		0 => array(
			'name' => 'cargo AS b',
			'type' => 'inner', // 'left', 'left outer', 'right', 'right outer'
			'on' => 'a.idcargo = b.id'
		)
	)	
);
$colunas    = array(
	'a.nome AS nomeUsuario', 
	'a.email', 
	'b.nome AS nomeCargo', 
	'a.datacadastro'
);
$parametros = array(
	'a.status' => array('in' => array(1, 2, 3))
);
$retorno    = 'object'; // Pode usar 'coluna', 'object', 'array'

try {
	$results = $pdos->getAll($tabela, $parametros, $colunas, $retorno);
	echo "<pre>";
	echo "<h3>Verificacao com varios valores validos 'in'</h3>";
	print_r($results);
	echo "</pre>";

} catch (Exception $e) {
	echo $e->getMessage();
}
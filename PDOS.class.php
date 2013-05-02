<?php

class PDOS extends PDO {

	private $query;
	private $tables;
	private $joins;
	private $conditions;
	private $collumns;
	private $values;
	private $parametros = array();
	private $PDOParam   = array();
	private $order;
	private $action = 'SELECT';
	private $limit;


	private $charset = 'utf8';
	private $port    = 3306;
	private $config  = array();

	const debug = false;

	public function __construct($host, $user, $pass, $dbname, $port=null, $dbtype='mysql')
	{
		$this->config['host']   = $host;
		$this->config['user']   = $user;
		$this->config['pass']   = $pass;
		$this->config['port']   = empty($port) ? $this->port : $port;
		$this->config['dbtype'] = $dbtype;
		$this->config['dbname'] = $dbname;
		try {
			parent::__construct($this->getStringConection(), $this->config['user'], $this->config['pass'], $this->getAttr());
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

	public function getAll($table, $condition=array(), $collumns="*", $return="array", $debug=false)
	{
		return $this->getOne($table,  $condition, $collumns, array('fetchAll' => $return), $debug);
	}

	public function getOne($table, $condition=array(), $collumns="*", $return="array", $debug=false)
	{

	}

	public function add($table, $values=array(), $debug=false)
	{
		
	}

	public function update($table, $values=array(), $condition=array(), $debug=false)
	{
		
	}

	public function save($table, $values=array(), $condition=array(), $debug=false)
	{
		
	}

	public function delete($table, $condition=array(), $debug=false)
	{
		
	}

	/*========================================
	=            Métodos privados            =
	========================================*/	
	private function setCollumns($collumns)
	{
		if (is_array($collumns)) 
			$this->collumns .= implode(',', $collumn);
		else
			$this->collumns = $collumn;
	}

	private function setConditions() {
		
	}

	/**
	 * Cria string das informações tabela
	 *  
	 * @param string|array $tables Configurações da tabela.
	 *
	 * O Formato das configurações podem ser de duas formas:
	 *
	 * 1 - string - 'nome_da_tabela';
	 * 2 - array - array(
	 * 		'name' => 'tabela',
	 * 		'join' => array(
	 * 			[0] => array(
	 * 				'type' => 'inner join',
	 * 				'name' => 'tabela',
	 * 				'on' => 'a.id = b.id'
	 * 			)
	 * 		)
	 * );
	 *
	 * @return void
	 */
	private function setTables($tables)
	{
		if (is_array($tables)) {
			if (isset($tables['name'])) {
				$this->tables = $tables['name'];
			} else {
				throw new Exception("Table name not defined", 100);				
			}

			if (isset($tables['join'])) {
				if (is_string($tables['join'][0])) {
					$this->setJoin($tables['join']);
				} else {
					foreach ($tables['join'] as $join) {
						$this->setJoin($join);
					}
				}
			}
				
 		} else {
 			if (!empty($tables)) {
 				$this->tables = $tables;
 			} else {
 				throw new Exception("Table name not defined", 100);				
 			}
 		}
	}

	/**
	 * Cria a string de join de acordo com as configurações.
	 * 
	 * @param array $options Configurações do join
	 *
	 * Formato de configuração recebido:
	 *
	 *  array(
	 * 			'type' => 'inner', // Tipos permitidos 'inner' , 'left' , 'left outer' , 'right', 'right outer'
	 * 			'name' => 'nome_tabela',
	 * 			'on' => 'a.id = b.id'
	 * 		)
	 * 	
	 * @return void
	 */
	private function setJoin($options=array())
	{
		if (is_array($options)) {
			if (isset($options['type'])) {
				switch ($options['type']) {
					case 'inner':
						$this->joins .= " INNER JOIN ";
						break;

					case 'left':
						$this->joins .= " LEFT JOIN ";
						break;

					case 'left outer':
						$this->joins .= " LEFT OUTER JOIN ";
						break;

					case 'right':
						$this->joins .= " RIGHT JOIN ";
						break;

					case 'right outer':
						$this->joins .= " RIGHT OUTER JOIN ";
						break;
					
					default:
						throw new Exception("Type of join not valid", 120);
						break;
				}
			} else {
				// Join padrão
				$this->joins .= " INNER JOIN ";	
			}

			if (isset($options['name'])) {
				$this->joins .= $options['name'];
			} else {
				throw new Exception("Table name of join not defined", 130);				
			}

			if (isset($tables['on'])) {
				$this->tables .= " ON " . $tables['on'];
			}
		} else {
			throw new Exception("Join must be a array", 110);
		}
	}

	private function getParametros()
	{
		/* Conteúdo da função */
	}

	private function getStringConection()
	{
		return $this->config['dbtype'] . ':host=' . $this->config['host'] . ';dbname=' . $this->config['dbname'] . ';port=' . $this->config['port'];
	}

	private function getAttr() {
		if (!empty($this->config['charset'])) {
			$this->charset = $this->config['charset'];
		}
		return array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET " . $this->charset);
	}

	private function setLimit($init=0, $end=1000)
	{
		return $this->limit = " LIMIT {$init}, {$end} ";
	}

	// private function getFields($data)
	// {
	// 	$fields = array();
	// 	if (is_int(key($data))) {
	// 		$fields = implode(',', $data);
	// 	} else if (!empty($data)) {
	// 		$fields = implode(',', array_keys($data));
	// 	} else {
	// 		$fields = '*';
	// 	}
	// 	return $fields;
	// }

}

$host = 'localhost';
$user = 'root';
$pass = 'java2012';
$dbname = 'ficbic';

$pdos = new PDOS($host, $user, $pass, $dbname);


$results = $pdos->getAll('fic_estados', array('cod_estado' => 1));
<?php

/**
 * Classe para facilitar o CRUD com o banco de dados.
 *
 * A intenção dessa classe é simplificar a conexão com o banco de dados usando a classe PDO
 * mas sem perder as segurança do PDO, conforme outras classes semelhantes.
 *
 * @author Carlos Augusto Gartner <carlos@we3online.com.br>
 * @link https://github.com/cagartner/PDOSimplificado/
 */
class PDOS extends PDO {

	private $_query;
	private $tables;
	private $joins;
	private $conditions;
	private $collumns;
	private $values;
	private $parametros     = '';
	private $PDOParam       = array();
	private $order          = false;
	private $action         = 'select';
	private $limit          = false;
	private $isWhere        = false;   
	private $return         = PDO::FETCH_BOTH;
	private $returnFunction = 'fetch';
	
	
	private $charset        = 'utf8';
	private $port           = 3306;
	private $config         = array();
	
	private $debug          = false;

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
		} catch (Exception $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

	/**
	 * Busca todas os registros de uma tabela informada.
	 * 	
	 * @param  string|array  $table     Nome da tabela em formato de string ou array
	 * @param  array   		 $condition Condições da busca.
	 * @param  string|array  $collumns  Colunas que serão retornada. Padrão: *
	 * @param  string  		 $return    Tipo de retorno, pode ser 'object', 'collumn', 'array'
	 * @param  boolean|array $order     Ordem da query
	 * @return array
	 */
	public function getAll($table, $condition=array(), $collumns="*", $return="array", $order=false)
	{	
		return $this->getOne($table,  $condition, $collumns, $return, $order, true);
	}

	/**
	 * Busca todas os registros de uma tabela informada.
	 * 	
	 * @param  string|array  $table     Nome da tabela em formato de string ou array
	 * @param  array   		 $condition Condições da busca.
	 * @param  string|array  $collumns  Colunas que serão retornada. Padrão: *
	 * @param  string  		 $return    Tipo de retorno, pode ser 'object', 'collumn', 'array'
	 * @param  boolean|array $order     Ordem da query
	 * @param  boolean 		 $all 		Parametro interno para configurar retorno de uma ou vária.
	 * @return array|object
	 */
	public function getOne($table, $condition=array(), $collumns="*", $return="array", $order=false, $all=false)
	{
		$this->clearQuery();
		if ($all) 
			$this->returnFunction = 'fetchAll';
		$this->setTables($table);
		$this->setConditions($condition);
		$this->setCollumns($collumns);
		$this->setReturn($return);
		if ($order)
			$this->setOrder($order);
		$this->createQuery();
		return $this->execute();
	}

	/**
	 * Método para adicionar registro a tabela
	 * 
	 * @param string $table  Nome da tabela
	 * @param array  $values Dados a ser inseridos
	 * @return integer|boolean
	 */
	public function add($table, $values=array())
	{
		$this->clearQuery();
		$this->setTables($table);
		$this->setAction('insert');
		$this->setValues($values);
		$this->createQuery();
		return $this->execute();
	}

	/**
	 * Método para editar registro em uma tabela
	 * 
	 * @param  string $table     Nome da tabela
	 * @param  array  $values    Colunas e valores a serem editados
	 * @param  array  $condition Condição da edição
	 * @return boolean           'true' para sucesso e 'false' para erro
	 */
	public function update($table, $values=array(), $condition=array())
	{
		$this->clearQuery();
		$this->setTables($table);
		$this->setAction('update');
		$this->setValues($values);
		$this->setConditions($condition);
		$this->createQuery();
		return $this->execute();
	}

	/**
	 * Método para salvar, esse método primeiro verifica se com as condições ele encontra algum registro, 
	 * se ele encontrar ele edita o mesmo, caso contrário ele cadastra um novo registro
	 * 
	 * @param  string $table     Nome da tabela
	 * @param  array  $values    Valores a editar|cadastrar
	 * @param  array  $condition Condição para verificar se já existe e depois alterar
	 * @return boolean|int       'true' para quando editar com sucesso e caso de novo registro 'int' com o id do mesmo.
	 */
	public function save($table, $values=array(), $condition=array())
	{
		$verifica = $this->getOne($table,$condition);
		if ($verifica) {
			return $this->update($table, $values, $condition);
		} else {
			return $this->add($table, $values);
		}
	}

	/**
	 * Método para excluir um registro
	 * 
	 * @param  string $table     Nome da tabela
	 * @param  array  $condition Condição da exclusão
	 * @return boolean           'true' para sucesso e 'false' para erro
	 */
	public function delete($table, $condition=array())
	{
		$this->clearQuery();
		$this->setTables($table);
		$this->setAction('delete');
		$this->setConditions($condition);
		$this->createQuery();
		return $this->execute();
	}

	public function getDebug()
	{
		return $this->showDebugInfo();
	}

	/*========================================
	=            Métodos privados            =
	========================================*/	

	/**
	 * Cria a query de acordo com a acao definida.
	 * 
	 * @return void 
	 */
	private function createQuery()
	{
		switch ($this->action) {
			case 'select':
				$this->_query = "SELECT ". $this->collumns . " FROM " . $this->tables . $this->joins . $this->parametros . $this->order . $this->limit;
				break;

			case 'insert':
				$this->_query = "INSERT INTO " . $this->tables . " (" . $this->collumns . ") VALUES (" .  $this->parametros . ")";
				break;

			case 'update':
				$this->_query = "UPDATE " . $this->tables . " SET " . $this->values . $this->parametros;
				break;

			case 'delete':
				$this->_query = "DELETE FROM " . $this->tables . $this->parametros;
				break;
			
			default:
				throw new Exception("Type os query not defined", 200);				
				break;
		}
	}

	private function execute() {
		$stmnt = $this->prepare($this->_query);
		try {
			$this->beginTransaction();
			$exe = $stmnt->execute($this->PDOParam);
			if ($this->action == 'insert')
				$lastId = $this->lastInsertId();
			$this->commit();
			if ($exe) {
				switch ($this->action) {
					case 'insert':
						return $lastId;
						break;

					case 'select':
						return $stmnt->{$this->returnFunction}($this->return);
						break;
					
					default:
						return true;
						break;
				}	
			} else {
				$error = $stmnt->errorInfo();				
				throw new Exception($error[2], $error[1]);	
				$this->rollBack();				
			}
		} catch(PDOException $e) {
			echo $e->getMessage() . "<br/>\n";
			$this->rollBack();
		}					
			
	}

	/**
	 * Cria string com as colunas passadas.
	 * 
	 * @param string|array $collumns Nome das colunas
	 *
	 * As colunas podem vir em duas formas:
	 *
	 * 1 - String : '*'
	 * 2 - Array: array('coluna1','coluna2');
	 *
	 * @return void
	 */
	private function setCollumns($collumns=array())
	{
		if (is_array($collumns)) 
			$this->collumns .= implode(', ', $collumns);
		else
			$this->collumns = $collumns;
	}

	private function setValues($values)
	{
		if (is_array($values)) {
			$i = 1;
			foreach ($values as $coluna => $valor) {
				$variavelPDO = $this->ramdomString(strlen($coluna)); 
				if ($this->action == 'insert') {
					$this->collumns .= ($i > 1 ? ', ' : '') . "{$coluna}";
					$this->parametros .= ($i > 1 ? ', ' : '') . ":{$variavelPDO}";
					$this->PDOParam[":{$variavelPDO}"] = $valor;
				} else {
					$this->values .= ($i > 1 ? ', ' : '') . " {$coluna} = :{$variavelPDO}";
					$this->PDOParam[":{$variavelPDO}"] = $valor;
				}
				$i++;
			}			
		} else {
			throw new Exception("Values must be a array", 150);			
		}
	}

	/**
	 * Configura e cria string de condições de uma query.
	 * 
	 * @param array $conditions Configuração de condições no seguinte formato:
	 *
	 * array(
	 * 		'col_int' => 1,
	 * 		'col_string' => 'Fulano',
	 * 		'data_equal' => '2012-03-20',
	 * 		'data_maior' => array('>', '2012-03-20'),
	 * 		'data_between' => array('between', array('2012-03-20',2012-04-20))
	 * );
	 *
	 * @return void
	 * 
	 */
	private function setConditions($conditions) {

		foreach ($conditions as $coluna => $valor) {
			if (!$this->isWhere) {
				$this->parametros .= " WHERE";
				$this->isWhere  = true;
			} else {
				$this->parametros .= " AND";
			}
			
			if (strstr($coluna, ',')) { // Em caso de uma busca de um mesmo valor em várias colunas "OR"
				$x = 1;
				$coluna = explode(',', $coluna);
				foreach ($coluna as $col) {
					$col = trim($col);
					$this->parametros .= ($x > 1 ? " OR" : "") . " {$col} ";
				
					if (is_numeric($valor)) {
						$operador = "=";
					} else {
						$operador = "LIKE";
					}
					$variavelPDO    = $this->ramdomString(strlen($col)); 				
					$this->parametros .= "{$operador} :{$variavelPDO}";
					$this->PDOParam[":{$variavelPDO}"] = $valor;
					$x++;
				}
			} else { // Em caso de busca de um valor em uma coluna "AND" ou "WHERE"
				
				$this->parametros .= " {$coluna} ";
				
				if (is_array($valor)) { // Verifica se é apenas um valor ou vários, ou um tipo  de verificação diferente de "=" ou "LIKE"
					foreach ($valor as $tipo => $valTmp) {			
						switch ($tipo) {
							case 'between':
								$variavelPDO1 = $this->ramdomString(strlen($coluna)); 
								$variavelPDO2 = $this->ramdomString(strlen($coluna));				
								$this->PDOParam[":{$variavelPDO1}"] = $valTmp[0];
								$this->PDOParam[":{$variavelPDO2}"] = $valTmp[1];
								$this->parametros .= "BETWEEN :{$variavelPDO1} AND :{$variavelPDO2}";
								break;

							case 'in':
								$this->parametros .= "IN (";
								$i = 1;
								foreach ($valTmp as $val) {
									$variavelPDO = $this->ramdomString(strlen($coluna)); 			
									$this->PDOParam[":{$variavelPDO}"] = $val;
									$this->parametros .= ($i>1 ? "," : "") . ":{$variavelPDO}";
									$i++;
								}
								$this->parametros .= ")";
								break;
							
							default:
								$variavelPDO = $this->ramdomString(strlen($coluna)); 			
								$this->PDOParam[":{$variavelPDO}"] = $valTmp;
								$this->parametros .= "{$tipo} :{$variavelPDO}";
								break;
						}
					}
				} else { // Caso de ser apenas um valor
					if (is_numeric($valor)) { // Verifica se é numérico ou string
						$operador = "=";
					} else {
						$operador = "LIKE";
					}
					$variavelPDO    = $this->ramdomString(strlen($coluna)); 				
					$this->parametros .= "{$operador} :{$variavelPDO}";
					$this->PDOParam[":{$variavelPDO}"] = $valor;
	 			}
			}

			
		}
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

			if (isset($options['on'])) {
				$this->joins .= " ON " . $options['on'];
			}
		} else {
			throw new Exception("Join must be a array", 110);
		}
	}

	/**
	 * Seta tipo de retorno
	 * 
	 * @param string $retorno Tipo de retorno, pode ser 'collumn', 'object', 'number'
	 */
	private function setReturn($retorno='array')
	{
		switch ($retorno) {
			case 'collumn':
				$this->return = PDO::FETCH_ASSOC;
				break;

			case 'object':
				$this->return = PDO::FETCH_OBJ;
				break;

			case 'number':
				$this->return = PDO::FETCH_NUM;
				break;
			
			default:
				$this->return = PDO::FETCH_BOTH;
				break;
		}
	}

	/**
	 * Gera string da conexão PDO
	 * 
	 * @return string 
	 */
	private function getStringConection()
	{
		return $this->config['dbtype'] . ':host=' . $this->config['host'] . ';dbname=' . $this->config['dbname'] . ';port=' . $this->config['port'];
	}

	/**
	 * Cria um attr para executar no inicio da conexao
	 * 					
	 * @return array
	 */
	private function getAttr() {
		if (!empty($this->config['charset'])) {
			$this->charset = $this->config['charset'];
		}
		return array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET " . $this->charset);
	}

	/**
	 * Seta limit da classe
	 * 		
	 * @param integer $init Linha inicial
	 * @param integer $end  Linha final
	 * @return void
	 */
	private function setLimit($init=0, $end=1000)
	{
		return $this->limit = " LIMIT {$init}, {$end} ";
	}

	/**
	 * Seta ordenacao.		
	 * 
	 * @param array|string $order Pode ser em dois formatos:
	 *
	 * 1 - `coluna` ASC
	 * 2 - array('coluna' => 'ASC')
	 *
	 * @return void
	 */
	private function setOrder($order)
	{
		if (is_array($order)) {
			$i = 1;
			foreach ($order as $coluna => $ordem) {
				$this->order .= ($i>1 ? ", " : "") . "`{$coluna}` {$ordem}";
				$i++;
			}
		} else {
			$this->order = $order;
		}		
	}

	/**
	 * Seta tipo de query
	 * 
	 * @param string $action 
	 */
	private function setAction($action='select')
	{
		return $this->action = $action;
	}

	/**
	 * Exibe informações de DEbug da classe
	 * 			
	 * @return html|string
	 */
	private function showDebugInfo()
	{

		ob_start();  
		print_r($this->PDOParam);
		$pdoParam=ob_get_contents();  
		ob_end_clean(); 

		echo '<strong>Query:</strong><br>
			<pre>'.$this->_query.'</pre></pre><hr>
			<strong>Tabelas:</strong><br>
			<pre>'.$this->tables.'</pre><hr>
			<strong>Colunas:</strong><br>
			<pre>'.$this->collumns.'</pre><hr>
			<strong>Joins:</strong><br>
			<pre>'.$this->joins.'</pre><hr>
			<strong>Parametros:</strong><br>
			<pre>'.$this->parametros.'</pre><hr>
			<strong>Parametros PDO:</strong><br>
			<pre>'.$pdoParam.'</pre><hr>';
	}

	/**
	 * Gera string randomica com o tamanho informado
	 * 
	 * @param  integer $tamanho Tamanho em caracteres 
	 * @return string        
	 */
	private function ramdomString($tamanho=5) {
		$retorno = '';
		$caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$len = strlen($caracteres);
		for ($n = 1; $n <= $tamanho; $n++) {
			$rand = mt_rand(1, $len);
			$retorno .= $caracteres[$rand-1];
		}
		return $retorno;
	}

	/**
	 * Método para limpar query
	 * 
	 * @return void 
	 */
	private function clearQuery() {
		$this->tables         = null;
		$this->joins          = null;
		$this->conditions     = null;
		$this->collumns       = null;
		$this->values         = null;
		$this->PDOParam       = array();
		$this->parametros     = null;
		$this->order          = false;
		$this->limit          = false;
		$this->isWhere        = false;   
		$this->return         = PDO::FETCH_BOTH;
		$this->returnFunction = 'fetch';
	}

}
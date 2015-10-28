<?php
namespace ORC\DAO;
class Dao {
	protected $pdo;
	protected $statements;
	protected $index = 0;
	protected $current;
	protected $current_index;
	protected $sqls = array();
	
	protected $debug = false;
	
	protected $_error_codes = array();
	protected $_error_infos = array();
	
	public function __construct(array $server_info) {
		$this->pdo = $this->createPDO($server_info);
		$this->debug($server_info['debug']);
	}
	
	public function switchStatement($index) {
		$this->setCurrentStatement($this->getSatementByIndex($index));
		return true;
	}

	public function getCurrentStatementIndex() {
		return $this->current_index;
	}
	
	public function isDebug() {
		return $this->debug;
	}
	public function debug($debug) {
		$this->debug = (bool)$debug;
		return $this;
	}
	/***************************************************                  PDO Public Methods   ****************************************************************************/

	
	public function beginTransaction() {
		return $this->pdo->beginTransaction();
	}
	
	public function commit() {
		return $this->pdo->commit();
	}
	
	public function errorCode() {
		return array_pop($this->_error_codes);
	}
	
	public function errorInfo() {
		return array_pop($this->_error_infos);
	}
	
	public function exec($statement) {
		$result = $this->pdo->exec($statement);
		$this->logSql(0, $statement);
		if ($result === false) {
			$this->_error_codes[] = $this->pdo->errorCode();
			$error_info = $this->pdo->errorInfo();
			$error_info['sql'] = $statement;
			$this->_error_infos[] = $error_info;
		}
		return $result;
	}
	
	public function getAttribute($attribute) {
		return $this->pdo->getAttribute($attribute);
	}
	
	public function getAvailableDrivers() {
		return $this->pdo->getAvailableDrivers();
	}
	
	public function inTransaction() {
		return $this->pdo->inTransaction();
	}
	
	public function lastInsertId($name = null) {
		//@todo add special support for db doesn't support this
		//如果当前 PDO 驱动不支持此功能，则 PDO::lastInsertId() 触发一个 IM001 SQLSTATE 。 
		return $this->pdo->lastInsertId($name);
	}
	
	/**
	 * 
	 * @param string $statement
	 * @param array $driver_options
	 * @throws \ORC\DAO\Exception\Exception
	 * @return int
	 */
	public function prepare($statement, array $driver_options = array()) {
		$st = $this->pdo->prepare($statement, $driver_options);
		if ($st === false) {
			throw new \ORC\DAO\Exception\SQLException('prepare statement failed', $this, $statement);
		}
		$index = $this->appendPDOStatement($st);
		$this->logSql($index, $statement);
		return $index;
	}
	
	/**
	 * 
	 * @param string $statement
	 * @param string $arg1
	 * @param string $arg2
	 * @param string $arg3
	 * @throws \ORC\DAO\Exception\Exception
	 * @return index the index of all statements
	 * @see PDO::query
	 */
	public function query($statement, $arg1 = null, $arg2 = null, $arg3 = null) {
		if ($arg1 == null && $arg2 == null && $arg3 == null) {
			$st = $this->pdo->query($statement);
		} else if ($arg3 == null) {
			$st = $this->pdo->query($statement, $arg1, $arg2);
		} else {
			$st = $this->pdo->query($statement, $arg1, $arg2, $arg3);
		}
		if ($st == false) {
			$this->_error_codes[] = $this->pdo->errorCode();
			$error_info = $this->pdo->errorInfo();
			$error_info['sql'] = $statement;
			$this->_error_infos[] = $error_info;
			$this->logSql(0, $statement);
			throw new \ORC\DAO\Exception\Exception('query failed', $this);
		}
		$index = $this->appendPDOStatement($st);
		$this->logSql($index, $statement);
		return $index;
	}
	
	public function quote($string, $parameter_type = self::PARAM_STR) {
		return $this->pdo->quote($string, $parameter_type);
	}
	
	public function rollBack() {
		return $this->pdo->rollBack();
	}
	
	public function setAttribute($attribute, $value) {
		return $this->pdo->setAttribute($attribute, $value);
	}
	
	/***************************************************                PDOStatement Public Methods   ****************************************************************************/
	public function bindParam($parameter, &$variable, $data_type = self::PARAM_STR, $length = null, $driver_options = null) {
		return $this->getCurrentStatement()->bindParam($parameter, $variable, $data_type, $length, $driver_options);
	}
	
	public function bindValue($parameter, $variable, $data_type = self::PARAM_STR) {
		return $this->getCurrentStatement()->bindValue($parameter, $variable, $data_type);
	}
	
	public function columnCount($index = null) {
		if (!$index) {
			return $this->getCurrentStatement()->columnCount();
		} else {
			return $this->getSatementByIndex($index)->columnCount();
		}
	}
	
	public function execute(array $input_parameters = array()) {
		if (count($input_parameters)) {
			$result = $this->getCurrentStatement()->execute($input_parameters);
		} else {
			$result = $this->getCurrentStatement()->execute();
		}
		if ($result) {
			return $result;
		} else {
			$this->_error_codes[] = $this->getCurrentStatement()->errorCode();
			$error_info = $this->getCurrentStatement()->errorInfo();
			ob_start();
			$this->getCurrentStatement()->debugDumpParams();
			$error_info['dump'] = ob_get_clean();
			$this->_error_infos[] = $error_info;
			throw new \ORC\DAO\Exception\Exception('query failed', $this);
		}
	}
	
	public function executeSpecial($index, array $input_parameters = array()) {
		$old_st = $this->getCurrentStatement();
		$this->switchStatement($index);
		$result = $this->execute($input_parameters);
		$this->setCurrentStatement($old_st);
		return $result;
	}
	
	public function fetch($fetch_style = self::FETCH_ASSOC, $cursor_orientation = self::FETCH_ORI_NEXT, $cursor_offset = 0) {
		return $this->getCurrentStatement()->fetch($fetch_style, $cursor_orientation, $cursor_offset);
	}
	
	/**
	 * 从某个特定的PDOStatement中获取内容
	 * @param int $index Statement index
	 * @param unknown $fetch_style
	 * @param unknown $cursor_orientation
	 * @param number $cursor_offset
	 * @return mixed
	 */
	public function fetchSpecial($index, $fetch_style = self::FETCH_ASSOC, $cursor_orientation = self::FETCH_ORI_NEXT, $cursor_offset = 0) {
		$old_st = $this->getCurrentStatement();
		$this->switchStatement($index);
		$result = $this->fetch($fetch_style, $cursor_orientation, $cursor_offset);
		$this->setCurrentStatement($old_st);
		return $result;
	}
	
	public function fetchAll($fetch_style = self::FETCH_ASSOC, $fetch_argument = null, array $ctor_args = array()) {
		if ($fetch_argument === null) {
			return $this->getCurrentStatement()->fetchAll($fetch_style);
		} elseif (empty($ctor_args)) {
			return $this->getCurrentStatement()->fetchAll($fetch_style, $fetch_argument);
		}
		return $this->getCurrentStatement()->fetchAll($fetch_style, $fetch_argument, $ctor_args);
	}
	
	public function fetchAllSepcial($index, $fetch_style = self::FETCH_ASSOC, $fetch_argument = null, array $ctor_args = array()) {
		$old_st = $this->getCurrentStatement();
		$this->switchStatement($index);
		$result = $this->fetchAll($fetch_style, $fetch_argument, $ctor_args);
		$this->setCurrentStatement($old_st);
		return $result;
	}
	
	public function fetchAllToObj($class_name = "\\ORC\\DAO\\Table\\DataRow") {
	    if (is_object($class_name)) {
	        $class_name = get_class($class_name);
	    }
	    \ORC\DAO\Table\Util::checkValidDataRowClass($class_name);
	    $data = $this->getCurrentStatement()->fetchAll(DAO::FETCH_CLASS | DAO::FETCH_PROPS_LATE, $class_name);
	    return $data;
	}
	
	public function fetchAllSpecialToObj($index, $class_name = "\\ORC\\DAO\\Table\\DataRow") {
	    if (is_object($class_name)) {
	        $class_name = get_class($class_name);
	    }
	    \ORC\DAO\Table\Util::checkValidDataRowClass($class_name);
	    $old_st = $this->getCurrentStatement();
	    $this->switchStatement($index);
	    $data = $this->getCurrentStatement()->fetchAll(DAO::FETCH_CLASS | DAO::FETCH_PROPS_LATE, $class_name);
	    $this->setCurrentStatement($old_st);
	    return $data;
	}
	
	public function rowCount($index = null) {
		if (!$index) {
			return $this->getRowCount($this->getCurrentStatement());
		} else {
			return $this->getRowCount($this->getSatementByIndex($index));
		}
	}
	
	public function setFetchMode($mode, $arg2 = null, $arg3 = null) {
		if ($arg2 === null) {
			return $this->getCurrentStatement()->setFetchMode($mode);
		} elseif ($arg3 === null) {
			return $this->getCurrentStatement()->setFetchMode($mode, $arg2);
		} else {
			return $this->getCurrentStatement()->setFetchMode($mode, $arg2, $arg3);
		}
	}
	/***************************************************                Other Protected Methods   ****************************************************************************/
	protected function createPDO($server_info) {
	    return new \PDO($server_info['dsn'], $server_info['dbuser'], $server_info['dbpass'], $server_info['options']);
	}
	
	protected function appendPDOStatement(\PDOStatement $st) {
		$this->index ++;
		$this->statements[$this->index] = $st;
 		$this->setCurrentStatement($st);
		return $this->index;
	}
	
	/**
	 * 
	 * @param int $index
	 * @return \PDOStatement
	 * @throws \ORC\DAO\Exception\Exception
	 */
	protected function getSatementByIndex($index) {
		if (is_int($index) && isset($this->statements[$index])) {
			return $this->statements[$index];
		} else {
			throw new \ORC\DAO\Exception\Exception('Unknown Statement!', $this);
		}
	}
	
	protected function getRowCount(\PDOStatement $st) {
		//@todo could do more
		return $st->rowCount();
	}
	
	protected function getCurrentStatement() {
		return $this->current;
	}
	
	protected function setCurrentStatement(\PDOStatement $st) {
		if (false !== ($key = array_search($st, $this->statements))) {
			$this->current = $st;
			$this->current_index = $key;
		} else {
			throw new \ORC\DAO\Exception\Exception('Illegal Statement', $this);
		}
	}
	
	/**
	 * 
	 * @param int $index
	 * @param string $sql
	 * @todo not called at all..
	 */
	protected function logSql($index, $sql) {
		if ($this->debug) {
			$args = func_get_args();
			array_shift($args);
			array_shift($args);
			$this->sqls[$index][] = array(
					'sql' => $sql,
					'params' => $args,
					'time' => \ORC\Util\Util::getNow()
			 );
		}
	}
	
	/***************************************************                Constants   ****************************************************************************/
	
	const PARAM_BOOL = \PDO::PARAM_BOOL;
	const PARAM_NULL = \PDO::PARAM_NULL;
	const PARAM_INT = \PDO::PARAM_INT;
	const PARAM_STR = \PDO::PARAM_STR;
	const PARAM_LOB = \PDO::PARAM_LOB;
	const PARAM_STMT = \PDO::PARAM_STMT;
	const PARAM_INPUT_OUTPUT = \PDO::PARAM_INPUT_OUTPUT;
	const PARAM_EVT_ALLOC = \PDO::PARAM_EVT_ALLOC;
	const PARAM_EVT_FREE = \PDO::PARAM_EVT_FREE;
	const PARAM_EVT_EXEC_PRE = \PDO::PARAM_EVT_EXEC_PRE;
	const PARAM_EVT_EXEC_POST = \PDO::PARAM_EVT_EXEC_POST;
	const PARAM_EVT_FETCH_PRE = \PDO::PARAM_EVT_FETCH_PRE;
	const PARAM_EVT_FETCH_POST = \PDO::PARAM_EVT_FETCH_POST;
	const PARAM_EVT_NORMALIZE = \PDO::PARAM_EVT_NORMALIZE;
	const FETCH_LAZY = \PDO::FETCH_LAZY;
	const FETCH_ASSOC = \PDO::FETCH_ASSOC;
	const FETCH_NUM = \PDO::FETCH_NUM;
	const FETCH_BOTH = \PDO::FETCH_BOTH;
	const FETCH_OBJ = \PDO::FETCH_OBJ;
	const FETCH_BOUND = \PDO::FETCH_BOUND;
	const FETCH_COLUMN = \PDO::FETCH_COLUMN;
	const FETCH_CLASS = \PDO::FETCH_CLASS;
	const FETCH_INTO = \PDO::FETCH_INTO;
	const FETCH_FUNC = \PDO::FETCH_FUNC;
	const FETCH_GROUP = \PDO::FETCH_GROUP;
	const FETCH_UNIQUE = \PDO::FETCH_UNIQUE;
	const FETCH_KEY_PAIR = \PDO::FETCH_KEY_PAIR;
	const FETCH_CLASSTYPE = \PDO::FETCH_CLASSTYPE;
	const FETCH_SERIALIZE = \PDO::FETCH_SERIALIZE;
	const FETCH_PROPS_LATE = \PDO::FETCH_PROPS_LATE;
	const FETCH_NAMED = \PDO::FETCH_NAMED;
	const ATTR_AUTOCOMMIT = \PDO::ATTR_AUTOCOMMIT;
	const ATTR_PREFETCH = \PDO::ATTR_PREFETCH;
	const ATTR_TIMEOUT = \PDO::ATTR_TIMEOUT;
	const ATTR_ERRMODE = \PDO::ATTR_ERRMODE;
	const ATTR_SERVER_VERSION = \PDO::ATTR_SERVER_VERSION;
	const ATTR_CLIENT_VERSION = \PDO::ATTR_CLIENT_VERSION;
	const ATTR_SERVER_INFO = \PDO::ATTR_SERVER_INFO;
	const ATTR_CONNECTION_STATUS = \PDO::ATTR_CONNECTION_STATUS;
	const ATTR_CASE = \PDO::ATTR_CASE;
	const ATTR_CURSOR_NAME = \PDO::ATTR_CURSOR_NAME;
	const ATTR_CURSOR = \PDO::ATTR_CURSOR;
	const ATTR_ORACLE_NULLS = \PDO::ATTR_ORACLE_NULLS;
	const ATTR_PERSISTENT = \PDO::ATTR_PERSISTENT;
	const ATTR_STATEMENT_CLASS = \PDO::ATTR_STATEMENT_CLASS;
	const ATTR_FETCH_TABLE_NAMES = \PDO::ATTR_FETCH_TABLE_NAMES;
	const ATTR_FETCH_CATALOG_NAMES = \PDO::ATTR_FETCH_CATALOG_NAMES;
	const ATTR_DRIVER_NAME = \PDO::ATTR_DRIVER_NAME;
	const ATTR_STRINGIFY_FETCHES = \PDO::ATTR_STRINGIFY_FETCHES;
	const ATTR_MAX_COLUMN_LEN = \PDO::ATTR_MAX_COLUMN_LEN;
	const ATTR_EMULATE_PREPARES = \PDO::ATTR_EMULATE_PREPARES;
	const ATTR_DEFAULT_FETCH_MODE = \PDO::ATTR_DEFAULT_FETCH_MODE;
	const ERRMODE_SILENT = \PDO::ERRMODE_SILENT;
	const ERRMODE_WARNING = \PDO::ERRMODE_WARNING;
	const ERRMODE_EXCEPTION = \PDO::ERRMODE_EXCEPTION;
	const CASE_NATURAL = \PDO::CASE_NATURAL;
	const CASE_LOWER = \PDO::CASE_LOWER;
	const CASE_UPPER = \PDO::CASE_UPPER;
	const NULL_NATURAL = \PDO::NULL_NATURAL;
	const NULL_EMPTY_STRING = \PDO::NULL_EMPTY_STRING;
	const NULL_TO_STRING = \PDO::NULL_TO_STRING;
	const ERR_NONE = \PDO::ERR_NONE;
	const FETCH_ORI_NEXT = \PDO::FETCH_ORI_NEXT;
	const FETCH_ORI_PRIOR = \PDO::FETCH_ORI_PRIOR;
	const FETCH_ORI_FIRST = \PDO::FETCH_ORI_FIRST;
	const FETCH_ORI_LAST = \PDO::FETCH_ORI_LAST;
	const FETCH_ORI_ABS = \PDO::FETCH_ORI_ABS;
	const FETCH_ORI_REL = \PDO::FETCH_ORI_REL;
	const CURSOR_FWDONLY = \PDO::CURSOR_FWDONLY;
	const CURSOR_SCROLL = \PDO::CURSOR_SCROLL;
// 	const MYSQL_ATTR_USE_BUFFERED_QUERY = \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY;
// 	const MYSQL_ATTR_LOCAL_INFILE = \PDO::MYSQL_ATTR_LOCAL_INFILE;
// 	const MYSQL_ATTR_INIT_COMMAND = \PDO::MYSQL_ATTR_INIT_COMMAND;
// 	const MYSQL_ATTR_MAX_BUFFER_SIZE = \PDO::MYSQL_ATTR_MAX_BUFFER_SIZE;
// 	const MYSQL_ATTR_READ_DEFAULT_FILE = \PDO::MYSQL_ATTR_READ_DEFAULT_FILE;
// 	const MYSQL_ATTR_READ_DEFAULT_GROUP = \PDO::MYSQL_ATTR_READ_DEFAULT_GROUP;
// 	const MYSQL_ATTR_COMPRESS = \PDO::MYSQL_ATTR_COMPRESS;
// 	const MYSQL_ATTR_DIRECT_QUERY = \PDO::MYSQL_ATTR_DIRECT_QUERY;
// 	const MYSQL_ATTR_FOUND_ROWS = \PDO::MYSQL_ATTR_FOUND_ROWS;
// 	const MYSQL_ATTR_IGNORE_SPACE = \PDO::MYSQL_ATTR_IGNORE_SPACE;
// 	const MYSQL_ATTR_SSL_KEY = \PDO::MYSQL_ATTR_SSL_KEY;
// 	const MYSQL_ATTR_SSL_CERT = \PDO::MYSQL_ATTR_SSL_CERT;
// 	const MYSQL_ATTR_SSL_CA = \PDO::MYSQL_ATTR_SSL_CA;
// 	const MYSQL_ATTR_SSL_CAPATH = \PDO::MYSQL_ATTR_SSL_CAPATH;
// 	const MYSQL_ATTR_SSL_CIPHER = \PDO::MYSQL_ATTR_SSL_CIPHER;
}
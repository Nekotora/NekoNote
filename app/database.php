<?php
/**
 * Database
 *
 * @author NekoTora <i@flag.moe>
 * 
 * 哞菇的测试型数据库调教姬。
 * 
 */

require_once 'config.php';
class DB {

	//有没有db!有没有db!有而且耐用五毛的!
	public $_db;


	function __construct() {
		//建立连接
		$this->_db = new mysqli(DB_SERVER, DB_USER, DB_PWD, DB_NAME);
		//噫~竟然连不上
		if ($this->_db->connect_error) {
			die(json_encode(array(
				'status' => 'faild',
				'message' => 'Database Connect Error (' . DB::$_db->connect_errno . ') ' . DB::$_db->connect_error
			)));
		}
	}

	//查找
	function find($table, $conditions = [], $page = NULL, $size = NULL) {
		//准备一个sql模版
		$template = ' SELECT * FROM `' . $table . '` ';
		//看看有几个条件扔进语句，用?代替一下值
		$args = [];
		if(count($conditions)) {
			$template .= ' WHERE ';
			foreach ($conditions as $key => $value) {
				if(count($args)) $template .= ' AND ';
				if(is_array($vaule)) {
					$template .= ' `' . $key . '` ' . $value[0] . ' ? ';
					$args[] = $vaule[1];
				}else{
					$template .= ' `' . $key . '` = ? ';
					$args[] = $value;
				}
			}
		}
		//想分页吗？
		if ($page != NULL && $size != NULL) {
			$template .= ' LIMIT ' . ($page - 1) * $size . ',' . $size;
		}
		//丢过去执行
		return $this->query($template, $args);
	}

	//添加
	function insert($table, $items = []) {
		//准备一个sql模版
		$template = ' INSERT INTO `' . $table . '` ';
		//看看都有啥数据要存，同理
		$args = [];
		if(count($items)) {
			$insert = ' ( ';
			$template .= '(';
			foreach ($items as $key => $value) {
				if (count($args)) {
					$template .= ',';
					$insert .= ',';
				}
				$template .= ' `' . $key . '` ';
				$insert .= ' ? ';
				$args[] = $value;
			}
			$insert .= ' ) ';
			$template .= ' ) VALUES ' . $insert;
		}
		//丢过去
		return $this->query($template, $args);
	}



	//检查数据，搞一个大新闻
	function query($query, $args = []) {
		// SELECT * FROM `xxx` WHERE `xx` = '12' AND `xx` = ?
		$flag = 0;
		while($pos = strpos($query, '?')) {
			//没吃药的话会参数不够
			if (!isset($args[$flag])) die(json_encode(array(
				'status' => 'faild',
				'message' => 'Agrument Error ( No more argument can fill into query )'
			)));

			//安全第一
			$query = substr($query, 0, $pos) . '\'' .
			$this->_db->real_escape_string($args[$flag]) . 
				'\'' . substr($query, $pos + 1);
			$flag++;
		}
		//走你
		return $this->pack($this->_db->query($query));
	}

	//三期食堂派物流代收
	function pack($query_result){
		var_dump($query_result);
		//卧槽没结果？死ね！
		if (!$query_result) die(json_encode(array(
			'status' => 'faild',
			'message' => 'Query Error (' . DB::$_db->error . ')'
		)));
		//快递打包
		$result = new StdClass;
		//object的话就翻译成json
		if (is_object($query_result)) {
			$result->flag = $query_result->num_rows;
			$result->objs = [];
			while($t = $query_result->fetch_object()) {
				$result->objs[] = $t;
			}
			$result->json = json_encode($result->objs);
			$result->raw = $query_result;
		} else {
			$result->flag = 1;
			$result->objs = ['status' => 'success'];
			$result->json = json_encode($result->objs);
			$result->raw = $query_result;
		}
		//出炉啦~
		return $result;
	}

	//事后……断开连接
	function __destruct() {
		$this->_db->close();
	}
}

$test = new DB();

var_dump($test->insert('note', [
		'time' => time(),
		'content' => htmlspecialchars('try'),
		'user' => 'adminre'
	]));

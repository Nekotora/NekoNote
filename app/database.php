<?php
/*
* 简易易懂的数据库调教类
*/
class DB {
	public static $_db;
	public static $_prefix;
	/* 建立魔法链接 */
	public static function connect() {
		// duang duang duang
		DB::$_db = new mysqli(
			Config::$DB['server'], 
			Config::$DB['username'], 
			Config::$DB['password'], 
			Config::$DB['dbname'], 
			Config::$DB['port']
		);
		// 处理错误的duang
		if (DB::$_db->connect_error) {
			// 报个错
			die(json_encode((Object) [
				'status' => "faild", 
				'message' => 'Database Connect Error (' . DB::$_db->connect_errno . ') ' . DB::$_db->connect_error
			]));
		}
		// 把前缀存起来备用
		$_prefix = Config::$DB['prefix'];
	}
	/* 请了个球 */
	public static function query($query, $args = []) {
		// 记录flag个数都记数君
		$count = 0;
		// 开始查找flag
		while($pos = strpos($query, '?')) {
			// 啊呀，参数不够了
			if(!isset($args[$count])) die(json_encode((Object) [
				'status' => 'faild', 
				'message' => 'Agrument Error (No more argument can fill into query)'
			]));
			// 套上安全X
			$query = substr($query, 0, $pos) . '\'' .
				DB::$_db->real_escape_string($args[$count]) . 
				'\'' . substr($query, $pos + 1);
			// 计数君上线
			$count++;
		}
		// 打包派送
		return DB::pack(DB::$_db->query($query));
	}
	/* 查找数据 */
	public static function find($table, $conditions = [], $page = NULL, $size = NULL) {
		// 准备个基本的语句
		$template = ' SELECT * FROM `' . $_prefix . $table . '` ';
		// 准备参数列表
		$args = [];
		// 如果有查询条件的话
		if (count($conditions)) {
			// 所以说先接上个WHERE
			$template .= ' WHERE ';
			// 然后开始拼装条件
			foreach($conditions as $key => $value) {
				// 如果不是第一个参数的话就拼个AND连接起来
				if (count($args)) $template .= ' AND ';
				// 如果是个数组的话，咱们约定第一个是符号，当然直接写数值的话就用等号盘点咯
				if(is_array($value)) {
					$template .= ' `' . $key . '` ' . $value[0] .' ? ';
					$args[] = $value[1];
				} else {
					$template .= ' `' . $key . '` = ? ';
					$args[] = $value;
				}
			}
		}
		// 如果你想分页的话
		if ($page != NULL && $size != NULL) {
			$template .= ' LIMIT ' . ($page - 1) * $size . ',' . $size;
		}
		// 打包派送，开门啊你的快递
		return DB::query($template, $args);
	}
	/* 插入 */
	public static function insert($table, $items = []) {
		// 和上面差不多啦
		$template = ' INSERT INTO `' . $_prefix . $table . '` ';
		$args = [];
		if(count($items)) {
			$insert = ' ( ';
			$template .= ' ( ';
			foreach($items as $key => $value) {
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
		return DB::query($template, $args);
	}
	/* 东风快递 */
	public static function pack($query_result) {
		// 结果错了就去死吧
		if (!$query_result) die(json_encode((Object) [
			'status' => "faild", 
			'message' => 'Query Error (' . DB::$_db->error . ')'
		]));
		// 打包
		$result = new StdClass;
		if (is_object($query_result)) {
			$result->count = $query_result->num_rows;
			$result->objs = [];
			while($t = $query_result->fetch_object()) {
				$result->objs[] = $t;
			}
			$result->json = json_encode($result->objs);
			$result->raw = $query_result;
		} else {
			$result->count = 1;
			$result->objs = ['status' => 'success'];
			$result->json = json_encode($result->objs);
			$result->raw = $query_result;
		}
		return $result;
	}
	/* 断线 */
	public static function disconnect() {
		$_db->close();
	}
}
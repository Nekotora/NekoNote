<?php 
// $_SERVER['REQUEST_URI'];
class Router {
	//搞一个路由出来
	public static $_router = [];
	public static function add($method, $uri, $callback, $verify = [], $json = false) {
		$method = strtolower($method);
		if (!isset(Router::$_router[$method])) Router::$_router[$method] = [];
		Router::$_router[$method][$uri] = ['callback' => $callback, 'verify' => $verify, 'json' => $json];
	}
	//走你
	public static function run() {
		//get访问的路径和后面的参数还有提交过来的一坨东西
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		//路径扒出来
		$uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?') ? strpos($_SERVER['REQUEST_URI'], '?') : strlen($_SERVER['REQUEST_URI']));
		//如果是根域名就让他根去
		if ($uri=='') $uri = '/';
		//然后如果找到相应的路由了就继续
		if (isset(Router::$_router[$method]) && isset(Router::$_router[$method][$uri])) {
			//这一条是把匹配的路由装起来方便使用
			$match = Router::$_router[$method][$uri];
			//建一个空的stdClass存get/post的数据
			$req = new stdClass();
			//试一试提交的数据是不是json
			if (($post_data = json_decode(file_get_contents("php://input"))) != NULL) {
				//解析json丢到数组里去
				foreach ($post_data as $k => $v) {
					$_POST[$k] = $v;
				}
			}
			//这个就是从匹配的路由的参数列表里面筛选数据了
			foreach ($match['verify'] as $key) {
				//考虑到有些情况下可以设置默认值
				if (is_array($key)) {
					$default = $key[1];
					$key = $key[0];
				}
				if ($method == 'get' ? !isset($_GET[$key]) : !isset($_POST[$key])) {
					if (isset($default)) {
						//有默认的值就读出来
						$req->{$key} = $default;
					} else {
						//没有对应的就狗袋
						die(json_encode((Object) [
							'status' => 'faild', 
							'message' => 'Wrong Arguments'
						]));
					}
				} else {
					//提供的数据存起来
					$req->{$key} = $method == 'get' ? $_GET[$key] : $_POST[$key];
				}
			}
			//如果返回json数据的话
			if($match['json']) header('content-type:application/json;charset=utf8');
			$match['callback']($req);
		} else {
			//去死
			die(json_encode((Object) [
				'status' => 'faild', 
				'message' => 'Action Not Allow'
			]));
		}
	}
}
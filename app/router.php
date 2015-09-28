<?php 
// $_SERVER['REQUEST_URI'];
class Router {
	public static $_router = [];
	public static function add($method, $uri, $callback, $verify = [], $json = false) {
		$method = strtolower($method);
		if (!isset(Router::$_router[$method])) Router::$_router[$method] = [];
		Router::$_router[$method][$uri] = ['callback' => $callback, 'verify' => $verify, 'json' => $json];
	}
	public static function run() {
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?') ? strpos($_SERVER['REQUEST_URI'], '?') : strlen($_SERVER['REQUEST_URI']));
		if ($uri=='') $uri = '/';
		if (isset(Router::$_router[$method]) && isset(Router::$_router[$method][$uri])) {
			$match = Router::$_router[$method][$uri];
			$req = new stdClass();
			foreach ($match['verify'] as $key) {
				if ($method == 'get' ? !isset($_GET[$key]) : !isset($_POST[$key])) die(json_encode((Object) [
					'status' => 'faild', 
					'message' => 'Wrong Arguments'
				]));
				$req->{$key} = $method == 'get' ? $_GET[$key] : $_POST[$key];
			}
			if($match['json']) header('content-type:application/json;charset=utf8');
			$match['callback']($req);
		} else {
			die(json_encode((Object) [
				'status' => 'faild', 
				'message' => 'Action Not Allow'
			]));
		}
	}
}
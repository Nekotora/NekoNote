<?php
/**
 * Router
 *
 * @author NekoTora <i@flag.moe>
 * 分离访问路径
 * 
 */
var_dump($_SERVER['REQUEST_URI']);


//获取一下文件位置以分割后面的字符
$uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?') ? strpos($_SERVER['REQUEST_URI'], '?') : strlen($_SERVER['REQUEST_URI']));
echo($uri);

class Router{
	static $_router = [];

	static function add($method, $url, $callback, ){
	}
}
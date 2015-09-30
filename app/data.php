<?php
require_once 'config.php';

//数据库
$db=new mysqli();
$db->connect(DB_SERVER, DB_USER, DB_PWD, DB_NAME);
if ($db->connect_error) {
	die('DB connect faild!');
}

switch ($_GET["method"]) {
case 'getnote': //获取留言
	$page = 1; //页数
	$slice = 10; //每页数量
	if(isset($_GET['page'])) $page = $_GET['page'];

	//查询数据库
	$query = 'SELECT * FROM `note` LIMIT ' . ($page-1)*$slice . ',' . $slice;
	$result = $db->query($query);

	//base64解密
	$r = array();
	while($t = $result->fetch_object()) {
		$t->content = base64_decode($t->content);
		$t->user = base64_decode($t->user);
		$r[] = $t;
	}

	echo json_encode($r);
  break;  

case 'addnote': //添加留言

	//解析json对象
	$post =  json_decode(file_get_contents('php://input'));
	var_dump($post);

	//检查数据不为空
	foreach (array('content', 'user') as $v) {
		if (!array_key_exists($v, $post)) die('Can not empty!');
	}

	//存入数据库
	$query = 'INSERT INTO `nekonote`.`note` (`time`, `content`, `user`) VALUES (\'' .
		time() .'\',\''.
		base64_encode(htmlspecialchars($post['content'])) .'\',\''. 
		base64_encode(htmlspecialchars($post['user'])) .'\')';
	$result = $db->query($query);
	echo json_encode(array('status'=>'success'));
	break;

default:
	echo json_encode(array('status'=>'faild'));
}

?>
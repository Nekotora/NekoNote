<?php
require_once 'config.php';

//数据库
$db=new mysqli();
$db->connect(DB_SERVER, DB_USER, DB_PWD, DB_NAME);
if ($db->connect_error) {
	die('connect faild!');
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
	foreach (array('content', 'user') as $v) {
		if (!array_key_exists($v, $_GET)) die('Method ERROR!');
	}
	$query = 'INSERT INTO `nekonote`.`note` (`time`, `content`, `user`) VALUES (\'' .
		time() .'\',\''.
		base64_encode(htmlspecialchars($_GET['content'])) .'\',\''. 
		base64_encode(htmlspecialchars($_GET['user'])) .'\')';
	$result = $db->query($query);
	echo json_encode(array('status'=>'success'));
	break;
default:
	echo json_encode(array('status'=>'faild'));
}

?>
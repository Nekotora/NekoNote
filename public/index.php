<?php
require_once '../app/config.php';
require_once '../app/database.php';
require_once '../app/router.php';

DB::connect();

Router::add('get', '/', function($data) {
	include '../view/index.html';
});

Router::add('get', '/note', function($data) { 
	echo DB::find('note', [], $data->page, $data->size)->json;
}, [['page', 1], ['size', 20]], true);

Router::add('post', '/note', function($data) {
	echo DB::insert('note', [
		'time' => time(),
		'content' => htmlspecialchars($data->content),
		'user' => $data->user
	])->json;
}, ['content', 'user']);

Router::run();

DB::Disconnect();
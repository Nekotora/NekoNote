<?php
require_once '../app/config.php';
require_once '../app/database.php';
require_once '../app/router.php';

DB::connect();

Router::add('get', '/', function($data) {
	include '../view/index.php';
});

Router::add('get', '/note', function($data) { 
	echo DB::find('note', [], $data->page, $data->size)->json;
}, ['page', 'size'], true);

Router::add('post', '/note', function($data) {
	echo DB::insert('note', [
		'time' => time(),
		'content' => $data->content,
		'user' => $data->author
	]);
}, ['content', 'author']);

Router::run();
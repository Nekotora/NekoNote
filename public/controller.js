(function () {

	//Angular Start!! 
	var app = angular.module('nekonote', []);

	//读取note
	app.controller('notebox', function($scope,$http) {
		$http({
			method : 'GET',
			url : './app/data.php?method=getnote'
		}).success(function(data,status,headers,config){
			$scope.notelist = data;
		}).error(function(data,status,headers,config){
			$scope.notelist = [{"content":"ERROR!","user":"admin"}];
		});
	});

	//提交note
	app.controller('addnote', function($scope,$http) {
	    $scope.add = function() {
	    	console.log($scope.note);
	    	//showtips('提交中……','doing',0);
	    	//提了个交
	        $http({
				method : 'post',
				url : 'app/data.php?method=addnote',
				data : $scope.note,
			}).success(function(data,status,headers,config){
				console.log(data);
			}).error(function(data,status,headers,config){
				$scope.notelist = [{"content":"ERROR!","user":"admin"}];
			});
	    };
	});

	//显示提示
	
})();
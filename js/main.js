/*
	* AngularJS handling JSON api
	* Getting "naslovna" page 
	* Dynamic;
	* Popularno;
*/
var Naslovna = angular.module('dynHangaar', []);
Naslovna.controller('naslovnaCntrl', function($scope, $http){
	$http.get("http://192.241.189.218/beta/hangaar/quatronic/api.php?stream=dynamic").then(function(res){
		$scope.dyn = res.data;
	});
});

var Rep = angular.module('repHangaar', []);
Rep.controller('repCntrl', function($scope, $http){
	$http.get("http://192.241.189.218/beta/hangaar/quatronic/api.php?stream=republic").then(function(res){
		$scope.rep = res.data;
	})
});

var Left = angular.module('leftHangaar', []);
Left.controller('leftCntrl', function($scope, $http){
	$http.get("http://192.241.189.218/beta/hangaar/quatronic/api.php?stream=left").then(function(res){
		$scope.levo = res.data;
	})
});

var Podcast = angular.module('podcastHangaar', []);
Podcast.controller('podcastCntrl', function($scope, $http){
	$http.get("http://192.241.189.218/beta/hangaar/quatronic/api.php?stream=podcast").then(function(res){
		$scope.podcast = res.data;
	})
});
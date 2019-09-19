mainProject.controller('DashboardCtrl', function($scope, $http, $state, Winehouse) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarProdutos();
    });
    $scope.produtosLoad = false;
    $scope.ListaProdutos = [];

    $scope.ListarProdutos = function() {
        $scope.produtosLoad = true;
        $http.get(APIBaseUrl+"/product/winehouselist/id/" + Winehouse.get().id).then(function(res) {
            $scope.produtosLoad = false;
            $scope.ListaProdutos = res.data;
        }, function(res) {
            $scope.produtosLoad = false;
            $scope.ListaProdutos = [];
        });
    };
});

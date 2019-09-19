mainProject.controller('DashboardCtrl', function ($scope, $http) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarProdutos();
        $scope.ListarConsumidores();
    });
    $scope.produtosLoad = false;
    $scope.cadastroLoad = false;
    $scope.ListaProdutos = [];
    $scope.ListaConsumidores = [];

    $scope.ListarProdutos = function () {
        $scope.produtosLoad = true;
        $http.get(APIBaseUrl + "/product/list").then(function (res) {
            $scope.produtosLoad = false;
            $scope.ListaProdutos = res.data;
        }, function (res) {
            $scope.produtosLoad = false;
            $scope.ListaProdutos = [];
        });
    };

    $scope.ListarConsumidores = function () {
        $scope.produtosLoad = true;
        $http.get(APIBaseUrl + "/customer/list").then(function (res) {
            $scope.cadastroLoad = false;
            $scope.ListaConsumidores = res.data;
        }, function (res) {
            $scope.cadastroLoad = false;
            $scope.ListaConsumidores = [];
        });
    };
});

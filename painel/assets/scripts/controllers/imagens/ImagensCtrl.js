mainProject.controller('ImagensCtrl', function ($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarProdutos();
    });
    $scope.produtosLoad = false;
    $scope.ListaProdutos = [];
    $scope.Filtro = {};

    $scope.ListarProdutos = function () {
        $scope.produtosLoad = true;
        $http.get(APIBaseUrl + "/product/list").then(function (res) {
            $scope.produtosLoad = false;
            $scope.ListaProdutos = res.data;
            angular.forEach($scope.ListaProdutos, function (prod) {
                prod.fullstring = [prod.name, prod.country, prod.region, prod.region, prod.grape, prod.productor, prod.tipicity].join('|');
            });
        }, function (res) {
            $scope.produtosLoad = false;
            $scope.ListaProdutos = [];
        });
    };
});

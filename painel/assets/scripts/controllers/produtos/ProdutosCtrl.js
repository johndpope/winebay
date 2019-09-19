mainProject.controller('ProdutosCtrl', function ($scope, $http, $state, User) {
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
                prod.no_image = (prod.id_image_thumb == null);
            });
        }, function (res) {
            $scope.produtosLoad = false;
            $scope.ListaProdutos = [];
        });
    };

    $scope.ExcluiProduto = function (produto) {
        bootbox.confirm("Deseja realmente excluir este produto?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function (ans) {
            if (ans) {
                $scope.paisesLoad = true;
                $http.get(APIBaseUrl + "/product/delete/id/" + produto.id).then(function (res) {
                    $scope.paisesLoad = false;
                    $scope.ListarProdutos();
                }, function (res) {
                    $scope.paisesLoad = false;
                    bootbox.alert("Não foi possível excluír o produto. Tente novamente.");
                });
            }
        });
    };
});

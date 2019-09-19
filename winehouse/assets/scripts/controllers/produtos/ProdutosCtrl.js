mainProject.controller('ProdutosCtrl', function($scope, $http, $state, Winehouse) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarProdutos();
    });
    $scope.load = false;
    $scope.ListaProdutos = [];
    $scope.PermiteCadastro = Winehouse.get().can_add_products;
    $scope.ProductFilter = {};

    $scope.ListarProdutos = function() {
        $scope.load = true;
        $http.get(APIBaseUrl+"/product/winehouselist/id/" + Winehouse.get().id).then(function(res) {
            $scope.load = false;
            $scope.ListaProdutos = res.data;
        }, function(res) {
            $scope.load = false;
            $scope.ListaProdutos = [];
        });
    };

    $scope.ExcluiProduto = function(produto) {
        bootbox.confirm("Confirma a remoção do produto '" + produto.name + "'?", function(ans) {
            if (ans) {
                $scope.load = true;
                $http.get(APIBaseUrl+"/winehouse/removeproduct/id/"+produto.id).then(function(res) {
                    $scope.load = false;
                    bootbox.alert("O produto foi removido.");
                    $scope.ListarProdutos();
                }, function(res) {
                    $scope.load = false;
                    bootbox.alert("Não foi possível remover o produto. Tente novamente.")
                });
            }
        });
    };
});

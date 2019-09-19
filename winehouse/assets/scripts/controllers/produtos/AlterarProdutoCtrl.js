mainProject.controller('AlterarProdutoCtrl', function ($scope, $http, $state, Winehouse, $stateParams) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDadosProduto();
    });
    $scope.load = false;
    $scope.WinehouseProduto = false;

    $scope.BuscarDadosProduto = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/winehouse/getproduct/id/" + $stateParams.Id).then(function (res) {
            $scope.WinehouseProduto = res.data.winehouseproduct;
            $scope.ProdutoSelecionado = res.data.product;
            $scope.load = false;
        }, function (res) {
            bootbox.alert("Não foi possível buscar os dados do produto.");
            $state.go("produtos");
        });
    };

    $scope.EnviaEstoque = function (type) {
        bootbox.prompt("Digite a quantidade para " + (type=="add"?'adicionar':'remover') + ":", function (quantity) {
            if (isNaN(quantity) || (+quantity <= 0)) return bootbox.alert("A quantidade deve ser maior que zero.");

            var dadosLancamento = {
                quantity: (type == "del") ? -quantity : quantity,
                description: (type == 'add') ? 'Adição de Estoque' : 'Remoção de Estoque',
                id_winehouse_product: $scope.WinehouseProduto.id,
                is_manual: true
            }

            $scope.load = true;
            $http.post(APIBaseUrl + "/estoque/addentry", dadosLancamento).then(function (res) {
                $scope.NovoLancamento = {
                    quantity: "",
                    description: "",
                    type: "add"
                }
                $scope.BuscarDadosProduto();
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível efetuar o lançamento.");
            });
        });
    }

    $scope.SalvarProduto = function () {
        $scope.WinehouseProduto.price = +($scope.WinehouseProduto.price.toString().replace(",", "."));
        if (isNaN($scope.WinehouseProduto.price) || (+$scope.WinehouseProduto.price < 0)) return bootbox.alert("Digite o valor do produto!");
        $scope.WinehouseProduto.quantity = +($scope.WinehouseProduto.quantity.toString().replace(',', '.'));
        if (isNaN($scope.WinehouseProduto.quantity) || (+$scope.WinehouseProduto.quantity < 1)) return bootbox.alert("Digite a quantidade do produto!");

        $scope.load = true;
        $http.post(APIBaseUrl + "/winehouse/saveproduct", $scope.WinehouseProduto).then(function (res) {
            $scope.load = false;
            bootbox.alert("Produto alterado com sucesso.");
            $state.go("produtos");
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível alterar o produto. Tente novamente.");
        });
    };
});
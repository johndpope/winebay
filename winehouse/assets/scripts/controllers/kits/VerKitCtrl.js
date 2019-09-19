mainProject.controller('VerKitCtrl', function ($scope, $http, $filter, Winehouse, $state, $stateParams) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarProdutos();
        $scope.ListarEmbalagens();
    });
    $scope.load = false;
    $scope.ListaProdutos = [];
    $scope.ProductFilter = { selecionado: false };
    $scope.DadosKit = {
        id_winehouse: Winehouse.get().id,
        name: '',
        description: '',
        price: '',
        package_id: ''
    };
    $scope.LimiteItens = 1;

    $scope.ListarEmbalagens = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/package/list").then(function (res) {
            $scope.load = false;
            $scope.ListaEmbalagens = [];
            angular.forEach(res.data, function (i) {
                if (i.size > 1) {
                    $scope.ListaEmbalagens.push(i);
                }
            });
        }, function (res) {
            $scope.load = false;
            $scope.ListaEmbalagens = [];
        });
    };

    $scope.ListarProdutos = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/product/winehouselist/id/" + Winehouse.get().id).then(function (res) {
            $scope.load = false;
            $scope.ListaProdutos = res.data;
            $scope.ListaProdutos.map(function (i) { i.selecionado = false; i.kit_quantity = 1; return i; });
        }, function (res) {
            $scope.load = false;
            $scope.ListaProdutos = [];
        }).finally(function () {
            $scope.DadosKit();
        });
    };

    $scope.AtualizaLimiteItens = function () {
        $scope.LimiteItens = $filter("filter")($scope.ListaEmbalagens, { id: $scope.DadosKit.package_id })[0].size;
    };

    $scope.ContagemItens = function () {
        var quantidade = 0;
        var produtosKit = $filter('filter')($scope.ListaProdutos, { selecionado: true });
        angular.forEach(produtosKit, function (i, k) {
            quantidade += i.kit_quantity;
        });
        return quantidade;
    };

    $scope.DadosKit = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/kit/get/id/" + $stateParams.Id).then(function (res) {
            $scope.load = false;
            $scope.DadosKit = res.data;
            $scope.LimiteItens = $scope.DadosKit.package_size;
            $scope.DadosKit.package_id = $scope.DadosKit.package_id.toString();
            angular.forEach($scope.ListaProdutos, function (produto) {
                var itemSelecionado = $filter('filter')($scope.DadosKit.items, { id_winehouse_product: produto.id });
                produto.selecionado = (itemSelecionado.length > 0);
                if (itemSelecionado.length) {
                    produto.kit_quantity = itemSelecionado[0].quantity;
                    produto.unit_price = itemSelecionado[0].unit_price;
                }
            });
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível carregar os dados do kit.");
            $state.go("kits");
        });
    };

    $scope.SelecionaProduto = function (produto) {
        produto.selecionado = true;
    };

    $scope.RemoveProduto = function (produto) {
        produto.selecionado = false;
        produto.kit_quantity = 1;
    };

    $scope.VerificaCalculoKit = function () {
        var produtosKit = $filter('filter')($scope.ListaProdutos, { selecionado: true });
        var recalc = false;
        angular.forEach(produtosKit, function (prod) {
            if (!isNaN(prod.unit_price)) {
                recalc = true;
            }
        });

        if (recalc) {
            $scope.CalculaValorKit();
        }
    };

    $scope.CalculaValorKit = function () {
        var valorTotal = 0;
        var produtosKit = $filter('filter')($scope.ListaProdutos, { selecionado: true });
        angular.forEach(produtosKit, function (prod) {
            if (isNaN(prod.unit_price)) prod.unit_price = 1;
            valorTotal += prod.kit_quantity * prod.unit_price;
        });
        $scope.DadosKit.price = valorTotal;
    };

    $scope.SalvarKit = function () {
        if (!$scope.DadosKit.name.length) return bootbox.alert("Digite o nome do Kit.");
        if ($scope.DadosKit.price == '') return bootbox.alert("Digite o valor do Kit.");
        if (isNaN($scope.DadosKit.price) || (+$scope.DadosKit.price < 0)) return bootbox.alert("O valor inserido é inválido.");
        if (!$scope.DadosKit.description.length) return bootbox.alert("Digite a descrição do Kit.");

        var produtosKit = $filter('filter')($scope.ListaProdutos, { selecionado: true });
        if (produtosKit.length < 2) return bootbox.alert("Selecione pelo menos 2 produtos para formar o kit.");
        if ($scope.ContagemItens() > $scope.LimiteItens) return bootbox.alert("A quantidade total de itens não pode ser maior que a embalagem.");
        var hasError = false;
        angular.forEach(produtosKit, function (i, k) {
            if (!hasError) {
                if (i.kit_quantity == "") {
                    hasError = true;
                    return bootbox.alert("O produto '" + i.name + "' deve ter uma quantidade no kit.");
                }
                if (isNaN(i.kit_quantity) || (+i.kit_quantity < 1)) {
                    hasError = true;
                    return bootbox.alert("A quantidade inserida para o produto '" + i.name + "' é inválida.");
                }
                produtosKit[k] = {
                    id_product_kit: "",
                    id_winehouse_product: i.id,
                    quantity: i.kit_quantity
                }
            }
        });

        if (!hasError) {
            $scope.DadosKit.items = produtosKit;
            $scope.load = true;
            $http.post(APIBaseUrl + "/kit/update", $scope.DadosKit).then(function (res) {
                $scope.load = false;
                bootbox.alert("Kit alterado com sucesso!");
                $state.go("kits");

            }, function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível alterar o Kit. Tente novamente.");
            });
        }
    };
});

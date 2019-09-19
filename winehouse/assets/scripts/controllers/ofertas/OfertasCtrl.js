mainProject.controller('OfertasCtrl', function ($scope, $http, $state, Winehouse, $filter, $timeout) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarProdutos();
        $scope.ListarEmbalagens();
        $scope.ListarOfertas();
        $scope.$watch(function () { return $scope.NovaOferta.price_unit + " " + $scope.NovaOferta.package_id }, function (val) {
            var quant = $filter("filter")($scope.ListaEmbalagens, { id: $scope.NovaOferta.package_id });
            if (quant.length) {
                $scope.NovaOferta.price = +$scope.NovaOferta.price_unit * quant[0].size;
            }
        });
    });
    $scope.load = false;
    $scope.ListaEmbalagens = [];
    $scope.ListaProdutos = [];
    $scope.NovaOferta = {
        id_winehouse_product: "",
        package_id: "",
        price: "",
        price_unit: "",
        active: true
    };
    $scope.OfertaAlterar = false;

    $scope.AlteraOferta = function (oferta) {
        $scope.OfertaAlterar = angular.copy(oferta);
        $scope.OfertaAlterar.package_id = $scope.OfertaAlterar.package_id.toString();
        $scope.OfertaAlterar.price_unit = $scope.OfertaAlterar.price / $filter("filter")($scope.ListaEmbalagens, { id: $scope.OfertaAlterar.package_id })[0].size;
    };

    $scope.AtualizaPrecoAlterar = function () {
        $scope.OfertaAlterar.price = $scope.OfertaAlterar.price_unit * $filter("filter")($scope.ListaEmbalagens, { id: $scope.OfertaAlterar.package_id })[0].size;
    };

    $scope.ListarProdutos = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/product/winehouselist/id/" + Winehouse.get().id).then(function (res) {
            $scope.load = false;
            $scope.ListaProdutos = res.data;
        }, function (res) {
            $scope.load = false;
            $scope.ListaProdutos = [];
        });
    };

    $scope.ListarEmbalagens = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/package/list").then(function (res) {
            $scope.load = false;
            $scope.ListaEmbalagens = res.data;
            $scope.NovaOferta.package_id = $scope.ListaEmbalagens[0].id.toString();
        }, function (res) {
            $scope.load = false;
            $scope.ListaEmbalagens = [];
        });
    };

    $scope.ExibeEstoque = function () {
        $scope.EstoqueProdutoNovaOferta = $filter("filter")($scope.ListaProdutos, { id: $scope.NovaOferta.id_winehouse_product })[0].quantity;
        $scope.PrecoProdutoNovaOferta = $filter("filter")($scope.ListaProdutos, { id: $scope.NovaOferta.id_winehouse_product })[0].price;
    }

    $scope.ListarOfertas = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/sale/winehouselist/id/" + Winehouse.get().id).then(function (res) {
            $scope.load = false;
            $scope.ListaOfertas = res.data;
            angular.forEach($scope.ListaOfertas, function (oferta) {
                oferta.active = Boolean(+oferta.active);
                if (oferta.active) {
                    oferta.loaded = false;
                    $timeout(function () {
                        angular.element("#oferta_" + oferta.id + " input[bs-switch]").click();
                        angular.element("#oferta_" + oferta.id + " input[bs-switch]").click();
                        $timeout(function () {
                            oferta.loaded = true;
                        }, 150);
                    }, 100);
                } else {
                    $timeout(function () {
                        oferta.loaded = true;
                    }, 150);
                }
            });
        }, function (res) {
            $scope.load = false;
            $scope.ListaOfertas = [];
        });
    };

    $scope.DefineStatusOferta = function (oferta) {
        if (oferta.loaded) {
            if (oferta.active) {
                $scope.AtivarOferta(oferta);
            } else {
                $scope.DesativarOferta(oferta);
            }
        }
    }

    $scope.AtivarOferta = function (oferta) {
        $scope.load = true;
        $http.post(APIBaseUrl + "/sale/start/id/" + oferta.id).then(function (res) {
            $scope.load = false;
            oferta.active = true;
        }, function (res) {
            $scope.load = false;
            oferta.active = false;
        });
    };

    $scope.DesativarOferta = function (oferta) {
        $scope.load = true;
        $http.post(APIBaseUrl + "/sale/stop/id/" + oferta.id).then(function (res) {
            $scope.load = false;
            oferta.active = false;
        }, function (res) {
            $scope.load = false;
            oferta.active = true;
        });
    };

    $scope.ExcluiOferta = function (oferta) {
        bootbox.confirm("Confirma a remoção da oferta do produto '" + oferta.product + "'?", function (ans) {
            if (ans) {
                $scope.load = true;
                $http.get(APIBaseUrl + "/sale/delete/id/" + oferta.id).then(function (res) {
                    $scope.load = false;
                    bootbox.alert("A oferta foi removida.");
                    $scope.ListarOfertas();
                }, function (res) {
                    $scope.load = false;
                    bootbox.alert("Não foi possível remover a oferta. Tente novamente.")
                });
            }
        });
    };

    $scope.CriarOferta = function () {
        if ($scope.NovaOferta.id_winehouse_product == "") return bootbox.alert("Selecione o produto da oferta.");
        if ($scope.NovaOferta.price == "") return bootbox.alert("Digite o valor desta oferta.");
        if (isNaN($scope.NovaOferta.price) || (+$scope.NovaOferta.price <= 0)) return bootbox.alert("O valor inserido é inválido.");

        $scope.ofertaLoad = true;
        $http.post(APIBaseUrl + "/sale/create", $scope.NovaOferta).then(function (res) {
            $scope.ofertaLoad = false;
            bootbox.alert("Oferta criada com sucesso.");
            $scope.NovaOferta = {
                id_winehouse_product: "",
                id_package: $scope.ListaEmbalagens[0].id.toString(),
                price: "",
                active: true
            };
            $scope.ListarOfertas();
        }, function (res) {
            $scope.ofertaLoad = false;
            bootbox.alert("Não foi possível criar a oferta. Tente novamente.");
        });
    };

    $scope.SalvarOferta = function () {
        if ($scope.OfertaAlterar.price == "") return bootbox.alert("Digite o valor desta oferta.");
        if (isNaN($scope.OfertaAlterar.price) || (+$scope.OfertaAlterar.price <= 0)) return bootbox.alert("O valor inserido é inválido.");

        $scope.ofertaLoad = true;
        $http.post(APIBaseUrl + "/sale/save", $scope.OfertaAlterar).then(function (res) {
            $scope.ofertaLoad = false;
            bootbox.alert("Oferta alterara com sucesso.");
            $scope.OfertaAlterar = false;
            $scope.ListarOfertas();
        }, function (res) {
            $scope.ofertaLoad = false;
            bootbox.alert("Não foi possível alterar a oferta. Tente novamente.");
        });
    };
});

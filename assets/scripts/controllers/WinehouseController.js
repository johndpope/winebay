mainProject.controller('WinehouseController', function ($filter, $scope, Products, $stateParams, $state, $http, $timeout) {
    $scope.$on("$viewContentLoaded", function () {
        if (!$stateParams.Id_Winehouse) return $state.go("/");
        if (!$stateParams.Id_Produto) return $state.go("/");
        $scope.BuscaProdutos();
        $scope.BuscaDadosWinehouse();
    });

    $scope.$watch(function () { return JSON.stringify(Products.getFilter()) }, function (v) {
        $scope.HasFilter = (v != "{}");
        $scope.FilterParams = Products.getFilter();
    });

    $scope.$watch(function () { return JSON.stringify(Products.getBox()) }, function (v) {
        if ($scope.ProductList.length) {
            var currentBox = Products.getBox();
            angular.forEach($scope.ProductList, function (prod) {
                var winehouseProd = $filter("filter")(currentBox, { id: prod.id });
                prod.onBox = (winehouseProd.length > 0);
            });
        }
    });
    $scope.ProductFeatured = false; //Variável que representa o produto que está sendo visto
    $scope.HasFilter = false;
    $scope.FilterView = 'grid';
    $scope.FilterOrder = 'price';
    $scope.FilterParams = {};
    $scope.ProductList = [];
    $scope.productLoad = false;
    $scope.CepPrazoEntrega = "";
    $scope.WinehouseData = {
        name: "Carregando..."
    };

    $scope.LimpaFiltros = function () {
        angular.forEach(angular.element("#winehousesidebar input[type='checkbox']:checked"), function (el) {
            el.checked = false;
        });
        Products.setFilter(false);
    };

    $scope.BuscaProdutos = function () {
        $scope.productLoad = true;
        $http.get(APIBaseUrl + "/store/getproductsbywinehouse/id/" + $stateParams.Id_Winehouse).then(function (res) {
            $scope.ProductList = res.data;
            angular.forEach($scope.ProductList, function (prod) {
                if (prod.id == $stateParams.Id_Produto) {
                    $scope.ProductFeatured = prod;
                }
                Products.checkItemOnBox(prod);
            });
        }, function (res) {
            console.log("Error on fetching products:", res.data);
        });
    };

    $scope.BuscaDadosWinehouse = function () {
        $scope.winehouseLoad = true;
        $http.get(APIBaseUrl + "/winehouse/get/id/" + $stateParams.Id_Winehouse).then(function (res) {
            $scope.winehouseLoad = false;
            $scope.WinehouseData = res.data;
        }, function (res) {
            $scope.winehouseLoad = false;
            console.log("Error on fetching winehouse:", res.data);
        });
    }

    $scope.ProductBoxInc = function (pr) {
        pr.quant = pr.quant + 1;
    }

    $scope.ProductBoxDec = function (pr) {
        if (pr.quant > 1) {
            pr.quant = pr.quant - 1;
        }
    }

    $scope.FiltraProdutosWinehouse = function () {
        return $filter('filter')($scope.ProductList, $scope.ProductFilter);
    };

    $scope.ProductFilter = function (prod) {
        var canReturn = true;
        angular.forEach($scope.FilterParams, function (filter, param) {
            if (prod[param]) {
                if (typeof filter[0] == "object") {
                    angular.forEach(filter, function (filterValue) {
                        if (filterValue.min) {
                            if (prod[param] < filterValue.min) {
                                canReturn = false;
                            }
                        };
                        if (filterValue.max) {
                            if (prod[param] > filterValue.max) {
                                canReturn = false;
                            }
                        };
                    });
                } else {
                    if (filter.indexOf(prod[param].value) == -1) canReturn = false;
                }
            } else {
                canReturn = false;
            }
        });
        return canReturn;
    };

    $scope.AddToBox = function (product) {
        product.onBox = true;
        Products.addToBox(product);
        if (Products.getBox().length == 1) {
            setTimeout(function () {
                Products.toggleBox();
            }, 100);
        }
    };

    $scope.CalculaPrazoEntrega = function () {
        $scope.freteError = false;
        $scope.DadosFrete = false;
        if ($scope.CepPrazoEntrega.trim().length) {
            $scope.freteLoad = true;
            $http.get(APIBaseUrl + "/shipment/calculate/cep/" + $scope.CepPrazoEntrega.trim() + "/id/" + $scope.ProductFeatured.id).then(function (res) {
                $scope.freteLoad = false;
                $scope.freteError = false;
                $scope.DadosFrete = res.data;
            }, function (res) {
                $scope.freteLoad = false;
                $scope.freteError = true;
            });
        }
    }
});

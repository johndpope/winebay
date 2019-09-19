mainProject.controller('BuscaGeralController', function ($scope, Products, $filter, $http, $stateParams) {
    $scope.HasFilter = false;
    $scope.FilterView = 'grid';
    $scope.FilterOrder = 'price';
    $scope.FilterParams = {};
    $scope.ProductList = [];
    $scope.TermoBusca = "";


    $scope.$on("$viewContentLoaded", function () {
        $scope.TermoBusca = $stateParams.Termo;
        $scope.ExecutaBusca();
    });

    $scope.$watch(function () { return JSON.stringify(Products.getFilter()) }, function (v) {
        $scope.HasFilter = (v != "{}");
        $scope.FilterParams = Products.getFilter();
    });


    $scope.ExecutaBusca = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/store/search/term/" + $scope.TermoBusca).then(function (res) {
            $scope.load = false;
            $scope.ProductList = res.data;
        }, function (res) {
            $scope.load = false;
            $scope.ProductList = [];
            console.log("Error on fetching products:", res.data);
        });
    }

    $scope.LimpaFiltros = function () {
        angular.forEach(angular.element("#homesidebar input[type='checkbox']:checked"), function (el) {
            el.checked = false;
        });
        Products.setFilter({});
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

    $scope.FiltraProdutosHome = function () {
        return $filter('filter')($scope.ProductList, $scope.ProductFilter);
    };
});

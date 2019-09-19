mainProject.controller('HomeController', function ($scope, Products, $filter, $http) {
    $scope.HasFilter = false;
    $scope.FilterView = 'grid';
    $scope.FilterOrder = 'price';
    $scope.FilterParams = {};
    $scope.ProductList = [];

    $scope.$watch(function () { return JSON.stringify(Products.getFilter()) }, function (v) {
        $scope.HasFilter = (v != "{}");
        $scope.FilterParams = Products.getFilter();
    })

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

    $scope.DestaqueCount = function () {
        return $filter('filter')($scope.ProductList, { featured: true }).length;
    };

    $scope.ProductList = Products.getProducts();
    if (!$scope.ProductList.length) {
        $http.get(APIBaseUrl + "/store/getproducts").then(function (res) {
            $scope.ProductList = res.data;
        }, function (res) {
            console.log("Error on fetching products:", res.data);
        });
    }

    $scope.featuredSliderResponsiveSettings = [
        {
            breakpoint: 780,
            settings: {
                arrows: false,
                dots: false
            }
        },
        {
            breakpoint: 400,
            settings: {
                dots: false,
                arrows: false,
                slidesToShow: 1
            }
        }
    ];
    $scope.homeSliderResponsiveSettings = [
        {
            breakpoint: 966,
            settings: {
                arrows: false,
                dots: false,
                slidesToShow: 3
            }
        },
        {
            breakpoint: 780,
            settings: {
                arrows: false,
                dots: false,
                slidesToShow: 2
            }
        },
        {
            breakpoint: 400,
            settings: {
                dots: false,
                arrows: false,
                slidesToShow: 1
            }
        }
    ];
    $scope.homeSliderGroups = [
        { title: "Melhor Custo-Benefício", filter: "bestchoice" },
        { title: "Mais Vendidos", filter: "bestseller" },
        { title: "Novidades", filter: "isnew" },
    ];
});

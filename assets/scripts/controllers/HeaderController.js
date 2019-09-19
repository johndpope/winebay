mainProject.controller('HeaderController', function ($scope, $filter, $timeout, Utils, Products, $state, User, $http) {
    $scope.headerType = 'light';
    $scope.isBoxCollapsed = true;
    $scope.ProductBox = Products.getBox();
    $scope.BoxLoaded = false;
    $scope.BuscaGeral = "";
    $scope.CurrentPage = "";

    $scope.ExecutaBuscaGeral = function () {
        if ($scope.BuscaGeral.trim().length) {
            $state.go("buscageral", { Termo: $scope.BuscaGeral });
        }
    }

    $scope.$watch(function () { return Utils.getHeaderType() }, function (val) {
        $scope.headerType = val;
    });

    $scope.$watch(function () { return User.get() }, function (val) {
        $scope.UserData = val;
    });

    $scope.$watch(function () { return Products.BoxLoaded() }, function (val) {
        $scope.BoxLoaded = val;
    });

    $scope.$watch(function () { return $state.current.name}, function (val) {
        $scope.CurrentPage = val;
    });

    $scope.$watch(function () { return JSON.stringify(Products.getBox()) }, function (val) {
        $scope.ProductBox = JSON.parse(val);
        $scope.ProductBox = $filter('orderBy')($scope.ProductBox, ['winehouse.name', 'name']);
        angular.forEach($scope.ProductBox, function (prod, i) {
            if (i > 0) {
                if ($scope.ProductBox[i - 1].winehouse_id != prod.winehouse_id) {
                    $scope.ProductBox[i].divisor = true;
                }
            }
        });
        $timeout(function () {
            $scope.$apply();
        })
    });

    $scope.loginLoad = false;
    $scope.loginSuccess = false;
    $scope.loginData = {
        password: "",
        email: ""
    };
    $scope.UserData = false;
    $scope.currentSlide = 0;
    $scope.CurrentSlide = function () { return +$scope.currentSlide; }
    $scope.BoxSlickSettings = {
        infinite: false,
        autoplay: false,
        speed: 700,
        dots: false,
        slidesToShow: 4,
        slidesToScroll: 1,
        prevArrow: ".box-slider [rel='prev-arrow']",
        nextArrow: ".box-slider [rel='next-arrow']",
        arrows: true,
        // variableWidth: true,
        // initialSlide: $scope.CurrentSlide(),
        event: {
            afterChange: function (event, slick, currentSlide, nextSlide) {
                $scope.currentSlide = currentSlide;
            },
            init: function (event, slick) {
                $timeout(function () {
                    slick.slickGoTo($scope.currentSlide); // slide to correct index when init
                });
            }
        },
        responsive: [
            {
                breakpoint: 966,
                settings: {
                    arrows: false,
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 780,
                settings: {
                    arrows: false,
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 400,
                settings: {
                    arrows: false,
                    slidesToShow: 1
                }
            }
        ]
    };

    $scope.BoxItemsCount = function () {
        return Products.countBox();
    };

    $scope.DoLogin = function () {
        $scope.loginLoad = true;
        $http.post(APIBaseUrl + "/customer/login", $scope.loginData).then(
            function (res) {
                $scope.loginLoad = false;
                if (res.data == "not_found") {
                    bootbox.alert("Por favor, verifique usuário/senha.");
                } else {
                    var userData = res.data;
                    if (!userData.addresses) userData.addresses = [];
                    else userData.addresses = JSON.parse(userData.addresses);
                    User.set(userData);
                }
            }, function (res) {
                $scope.loginLoad = false;
                bootbox.alert("Não foi possível efetuar login. Tente novamente.");
            }
        );
    };

    $scope.DoLogout = function () {
        User.set(false);
        Products.setBox([]);
        $state.go("/");
    }

    $scope.ToggleCaixa = function () {
        Products.toggleBox();
    };

    $scope.CalculaBoxTotal = function () {
        var total = 0;
        angular.forEach($scope.ProductBox, function (pr) {
            total += pr.price * pr.quant;
        });
        return total;
    };

    $scope.ProductBoxIncrease = function (pr) {
        pr.quant = pr.quant + 1;
        Products.setBox($scope.ProductBox);
    }

    $scope.ProductBoxDecrease = function (pr) {
        if (pr.quant <= 1) {
            $scope.ProductBox.splice($scope.ProductBox.indexOf(pr), 1);
        } else {
            pr.quant = pr.quant - 1;
        }
        Products.setBox($scope.ProductBox);
    }

    $scope.FecharCaixa = function () {
        Products.closeBox();
    };

    $scope.FinalizarCompra = function () {
        if (User.get()) $state.go("finalizarcompra");
        else {
            Products.toggleBox();
            $state.go("login", { Destination: "finalizarcompra" });
        }
    };
});

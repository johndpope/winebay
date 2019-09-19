mainProject.controller('CouponsCtrl', function ($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarCupons();
    });
    $scope.cuponsLoad = false;
    $scope.ListaCupons = [];
    $scope.NovoCupom = {
        code: "",
        value: 1,
        active: true
    };

    $scope.CriarNovoCupom = function () {
        if ($scope.NovoCupom.code.trim() == "") return bootbox.alert("Digite o código do cupom.");
        if ($scope.NovoCupom.value.trim() == "") return bootbox.alert("Digite o valor do cupom.");
        $scope.NovoCupom.value = $scope.NovoCupom.value.replace(',', '.');
        if (isNaN($scope.NovoCupom.value) || +$scope.NovoCupom.length < 0) return bootbox.alert("O valor inserido é inválido.");


        $scope.cuponsLoad = true;
        $http.post(APIBaseUrl + "/coupon/create/", $scope.NovoCupom).then(function (res) {
            $scope.cuponsLoad = false;
            $scope.ListarCupons();
        }, function (res) {
            $scope.cuponsLoad = false;
            bootbox.alert("Não foi possível alterar o status deste cupom. Tente novamente.");
        })
    };

    $scope.ListarCupons = function () {
        $scope.cuponsLoad = true;
        $http.get(APIBaseUrl + "/coupon/list").then(function (res) {
            $scope.cuponsLoad = false;
            $scope.ListaCupons = res.data;
            angular.forEach($scope.ListaCupons, function (coupon) {
                coupon.date_creation_formatted = moment(coupon.date_creation).format("DD/MM/YY");
            });
        }, function (res) {
            $scope.cuponsLoad = false;
            $scope.ListaCupons = [];
        });
    };

    $scope.ToggleStatusCupom = function (cupom) {
        $scope.cuponsLoad = true;
        $http.get(APIBaseUrl + "/coupon/toggle/id/" + cupom.id + "/flag/" + +(!cupom.active)).then(function (res) {
            $scope.cuponsLoad = false;
            cupom.active = !cupom.active;
        }, function (res) {
            $scope.cuponsLoad = false;
            bootbox.alert("Não foi possível alterar o status deste cupom. Tente novamente.");
        })
    }

    $scope.ExcluiCupom = function (cupom) {
        bootbox.confirm("Deseja realmente excluir o cupom?", function (ans) {
            if (ans) {
                $scope.cuponsLoad = true;
                $http.get(APIBaseUrl + "/coupon/remove/id/" + cupom.id).then(function (res) {
                    $scope.cuponsLoad = false;
                    $scope.ListarCupons();
                }, function (res) {
                    $scope.cuponsLoad = false;
                    bootbox.alert("Não foi possível remover o cupom. Tente novamente.");
                })
            }
        });
    }
});

mainProject.controller('FreteCtrl', function ($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDadosFreteGeral();
        $scope.ListarWinehouses();
    });
    $scope.geralLoad = false;
    $scope.winehousesLoad = true;
    $scope.ListaWinehouses = [];

    $scope.DadosFreteGeral = {
        type: "value",
        value: 0
    }

    $scope.BuscarDadosFreteGeral = function () {
        $scope.geralLoad = true;
        $http.post(APIBaseUrl + "/website/get", { list: ["shipment_discount", "shipment_discount_type"] }).then(
            function (res) {
                $scope.geralLoad = false;
                $scope.DadosFreteGeral.type = res.data.shipment_discount_type;
                $scope.DadosFreteGeral.value = +res.data.shipment_discount;
            }, function (res) {
                $scope.geralLoad = false;
                bootbox.alert("Não foi possível buscar os dados gerais. Tente novamente.");
            }
        );
    };

    $scope.SalvarFreteGeral = function () {
        if ($scope.DadosFreteGeral.value.toString() == "") return bootbox.alert("Insira o valor do desconto do frete geral.");
        $scope.DadosFreteGeral.value = $scope.DadosFreteGeral.value.replace(',', '.');
        if (isNaN($scope.DadosFreteGeral.value)) return bootbox.alert("O desconto do frete geral não é um número válido.");

        $scope.geralLoad = true;
        $http.post(APIBaseUrl + "/website/save", {
            Dados: {
                shipment_discount: $scope.DadosFreteGeral.value,
                shipment_discount_type: $scope.DadosFreteGeral.type
            }
        }).then(
            function (res) {
                $scope.geralLoad = false;
            },
            function (res) {
                $scope.geralLoad = false;
                bootbox.alert("Não foi possível salvar os dados. Tente novamente.");
            }
        );
    };

    $scope.ListarWinehouses = function () {
        $scope.winehousesLoad = 1;
        $http.get(APIBaseUrl + "/winehouse/list").then(function (res) {
            $scope.winehousesLoad = 0;
            $scope.ListaWinehouses = res.data;
        }, function (res) {
            $scope.winehousesLoad = 0;
            $scope.ListaWinehouses = [];
            bootbox.alert("Não foi possível buscar as winehouses. Tente novamente.");
        });
    };

    $scope.SalvarFreteWinehouses = function () {
        var errorList = [];
        angular.forEach($scope.ListaWinehouses, function (winehouse) {
            if ((winehouse.shipment_discount.toString() == "")
                || isNaN(winehouse.shipment_discount)
                || (+winehouse.shipment_discount < 0)) return errorList.push(winehouse.name);

            $scope.winehousesLoad++;
            $http.post(APIBaseUrl + "/winehouse/save", winehouse).then(function (res) {
                $scope.winehousesLoad--;
            },
                function (res) {
                    $scope.winehousesLoad--;
                    bootbox.alert("Não foi possível salvar os dados. Tente novamente.");
                });
        });

        if (errorList.length) {
            bootbox.alert("O valor de desconto da<small>(s)</small> winehouse<small>(s)</small>: " + errorList.join(', ') + " é inválido.")
        }
    };
});

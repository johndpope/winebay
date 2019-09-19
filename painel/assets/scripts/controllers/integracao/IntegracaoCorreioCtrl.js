mainProject.controller('IntegracaoCorreioCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDados();
    });
    $scope.APIBaseUrl = APIBaseUrl;
    $scope.IntegrationData = {
        name: "Correio",
        status: false,
        test_data: {},
        production_data: {
            services: [
                { code: 81019, text: "E-Sedex", active: false },
                { code: 41068, text: "Pac", active: false },
                { code: 40096, text: "Sedex", active: false },
                { code: 40886, text: "Sedex 10 Pacote", active: false },
                { code: 40878, text: "Sedex Hoje", active: false },
            ],
            additional: [
                { code: "mp", text: "Mão Própria", active: false },
                { code: "vd", text: "Valor Declarado", active: false },
                { code: "ar", text: "Aviso de Recebimento", active: false },
            ],
        }
    }

    $scope.BuscarDados = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/integration/get/name/Correio").then(function (res) {
            $scope.load = false;
            $scope.IntegrationData = res.data;
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível salvar os dados. Tente novamente.");
        });
    };

    $scope.ToggleStatus = function () {
        $scope.IntegrationData.status = !$scope.IntegrationData.status;
        $scope.SalvarAlteracoes();
    };

    $scope.SalvarAlteracoes = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/integration/save", { Dados: $scope.IntegrationData }).then(
            function (res) {
                $scope.load = false;
                bootbox.alert("Os dados da integração foram alterados.");
            },
            function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível salvar os dados. Tente novamente.");
            }
        );
    }
});

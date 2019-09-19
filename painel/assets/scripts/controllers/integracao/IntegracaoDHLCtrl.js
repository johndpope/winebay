mainProject.controller('IntegracaoDHLCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDados();
    });
    $scope.APIBaseUrl = APIBaseUrl;
    $scope.IntegrationData = {
        name: "DHL",
        status: false,
        current_env: "test_data",
        test_data: {
            site_id: "", 
            password: "", 
            account_number: "", 
            email_id: "", 
            name: "", 
            country: "", 
            url: ""
        },
        production_data: {
            site_id: "", 
            password: "", 
            account_number: "", 
            email_id: "", 
            name: "", 
            country: "", 
            url: ""
        }
    }

    $scope.BuscarDados = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/integration/get/name/DHL").then(function (res) {
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

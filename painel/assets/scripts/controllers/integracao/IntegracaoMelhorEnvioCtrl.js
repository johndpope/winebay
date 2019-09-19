mainProject.controller('IntegracaoMelhorEnvioCtrl', function ($scope, $http, $state) {
    //console.log($scope, $http, $state)
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDados();
    });


    $scope.APIBaseUrl = APIBaseUrl;
    $scope.IntegrationData = {
        name: "MelhorEnvio",
        status: false,
        //current_env: "production_data",
        production_data: {
            token: ""
            
        }
    }

    $scope.BuscarDados = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/integration/get/name/MelhorEnvio").then(function (res) {
            //console.log(res)
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

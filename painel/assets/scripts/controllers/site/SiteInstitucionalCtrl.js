mainProject.controller('SiteInstitucionalCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDadosPagina();
    });
    $scope.APIBaseUrl = APIBaseUrl;

    $scope.DadosPagina = {
        title: "",
        block1: "",
        block2: "",
        block3: ""
    };

    $scope.BuscarDadosPagina = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/get", { list: ["page_institucional"] }).then(
            function (res) {
                $scope.load = false;
                if (res.data.page_institucional) {
                    $scope.DadosPagina = JSON.parse(res.data.page_institucional);
                }
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível buscar os dados da página. Tente novamente.");
            }
        );
    };

    $scope.SalvarAlteracoes = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/save", { Dados: { page_institucional: angular.toJson($scope.DadosPagina) } }).then(
            function (res) {
                $scope.load = false;
            },
            function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível salvar os dados. Tente novamente.");
            }
        );
    }
});

mainProject.controller('IntegracaoYesPayCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {

    });
    $scope.APIBaseUrl = APIBaseUrl;

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

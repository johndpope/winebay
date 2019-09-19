mainProject.controller('InstitucionalController', function ($scope, $http) {
    $scope.$on("$viewContentLoaded", function () {
        $scope.BuscarDadosPagina();
    });

    $scope.load = false;
    $scope.DadosPagina = false;

    $scope.BuscarDadosPagina = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/get", { list: ["page_institucional"] }).then(
            function (res) {
                $scope.DadosPagina = JSON.parse(res.data.page_institucional);
                $scope.load = false;
            }, function (res) {
            }
        );
    };
});

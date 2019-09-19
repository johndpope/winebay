mainProject.controller('ContatoController', function ($scope, $http) {
    $scope.$on("$viewContentLoaded", function () {
        $scope.BuscarDadosPagina();
    });

    $scope.load = false;
    $scope.DadosPagina = false;

    $scope.BuscarDadosPagina = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/get", { list: ["contact_list"] }).then(
            function (res) {
                $scope.DadosPagina = JSON.parse(res.data.contact_list);
                $scope.load = false;
            }, function (res) {
            }
        );
    };
});

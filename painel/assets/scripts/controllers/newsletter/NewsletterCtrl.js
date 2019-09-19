mainProject.controller('NewsletterCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDados();
    });
    $scope.APIBaseUrl = APIBaseUrl;
    $scope.ListaNewsletter = [];
    $scope.load = false;

    $scope.BuscarDados = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/newsletter/getlist").then(function (res) {
            $scope.load = false;
            $scope.ListaNewsletter = res.data;
            angular.forEach($scope.ListaNewsletter, function (item) {
                item.subscription_date_formatted = moment(item.subscription_date, "YYYY-MM-DD HH:mm:ss").format("DD/MM/YY");
            });
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possivel buscar a lista de inscritos. Tente novamente.");
        });
    };
});

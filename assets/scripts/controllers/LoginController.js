mainProject.controller('LoginController', function ($scope, User, $http, $state, $stateParams) {
    $scope.$on("$viewContentLoaded", function () {
        $scope.UserLogged = User.get();
        $scope.Destination = $stateParams.Destination;
    });
    $scope.UserLogged = false;
    $scope.loginData = {
        password: "",
        email: ""
    };
    $scope.DoLogin = function () {
        $scope.loginLoad = true;
        $http.post(APIBaseUrl + "/customer/login", $scope.loginData).then(
            function (res) {
                $scope.loginLoad = false;
                if (res.data == "not_found") {
                    bootbox.alert("Por favor, verifique usuário/senha.");
                } else {
                    var userData = res.data;
                    if (!userData.addresses) userData.addresses = [];
                    else userData.addresses = JSON.parse(userData.addresses);
                    User.set(userData);
                    if ($stateParams.Destination) {
                        $state.go($stateParams.Destination);
                    } else {
                        $state.go("/");
                    }
                }
            }, function (res) {
                $scope.loginLoad = false;
                bootbox.alert("Não foi possível efetuar login. Tente novamente.");
            }
        );
    };
});

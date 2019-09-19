mainProject.controller('LoginModalCtrl', function($scope, $uibModal, User) {
    var modalInstance = $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: 'login-modal.html',
        backdrop: 'static',
        keyboard: false,
        size: "custom-lg",
        appendTo: angular.element('.modal-login'),
        controller: 'LoginCtrl',
    });
}).controller('LoginCtrl', function($scope, $uibModal, User, $state, $http) {
    $scope.DoLogin = function() {
        if ($scope.loginData==undefined) return bootbox.alert("Digite login e senha!");
        if (($scope.loginData.login==undefined)||($scope.loginData.login=="")) return bootbox.alert("Digite o seu login!");
        if (($scope.loginData.password==undefined)||($scope.loginData.password=="")) return bootbox.alert("Digite a sua senha!");

        $scope.load = true;
        $http.post(APIBaseUrl+"/login/dologin", {loginData:$scope.loginData}).then(function(res) {
            $scope.load = false;
            if (res.data) {
                User.set(res.data);
                $state.go("dashboard");
            } else {
                bootbox.alert("Por favor, verifique usuário e senha.");
            }
        }, function(res) {
            $scope.load = false;
            bootbox.alert("Ocorreu um erro ao efetuar login, tente novamente!");
        });
    };
});

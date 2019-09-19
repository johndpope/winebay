mainProject.controller('FooterController', function ($scope, $timeout, $state, $http) {
    $scope.newsletterLoad = false;
    $scope.newsletterSuccess = false;
    $scope.newsletterWarning = false;
    $scope.newsletterData = {
        name: "",
        email: ""
    };

    $scope.SendNewsletter = function () {
        if (!$scope.newsletterData.name.trim().length) return bootbox.alert("Por favor, digite seu nome!");
        if (!$scope.newsletterData.email.trim().length) return bootbox.alert("Por favor, digite seu email!");
        if (!/\S+@\S+\.\S+/.test($scope.newsletterData.email)) return bootbox.alert("O email inserido é inválido!");

        $scope.newsletterLoad = true;
        $http.post(APIBaseUrl + "/newsletter/add", $scope.newsletterData).then(function (res) {
            $scope.newsletterLoad = false;
            $scope.newsletterSuccess = (res.data != 'exists');
            $scope.newsletterWarning = (res.data == 'exists');
            $scope.newsletterData = {
                name: "",
                email: ""
            };
            $timeout(function () {
                $scope.newsletterSuccess = false;
                $scope.newsletterWarning = false;
            }, 5000);
        }, function (res) {
            $scope.newsletterLoad = false;
            $scope.newsletterSuccess = false;
            $scope.newsletterWarning = false;
        });
    };

    $scope.ShowMenu = function () {
        if (angular.element("#headerUserMenu").length) {
            $timeout(function () {
                angular.element("#headerUserMenu").triggerHandler('click');
            });
        } else {
            $state.go("login");
        }
    };
});

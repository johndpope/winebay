mainProject.controller('WinehouseCtrl', function($scope, $http, $state, Winehouse) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.DadosWinehouse = angular.copy(Winehouse.get());
    });
    $scope.load = false;
    $scope.DadosWinehouse = false;

    $scope.SalvarWinehouse = function() {
        if (!$scope.DadosWinehouse) return bootbox.alert("Não há um winehouse sendo alterado.");
        if ($scope.DadosWinehouse.name.trim()=="") return bootbox.alert("Digite o nome da winehouse.");
        if ($scope.DadosWinehouse.business_name.trim()=="") return bootbox.alert("Digite a razão social da winehouse.");
        if ($scope.DadosWinehouse.address.trim()=="") return bootbox.alert("Digite o endereço da winehouse.");
        if ($scope.DadosWinehouse.city.trim()=="") return bootbox.alert("Digite a cidade da winehouse.");
        if ($scope.DadosWinehouse.bairro.trim()=="") return bootbox.alert("Digite o bairro da winehouse.");
        if ($scope.DadosWinehouse.state.trim()=="") return bootbox.alert("Digite o estado da winehouse.");
        if ($scope.DadosWinehouse.cep.trim()=="") return bootbox.alert("Digite o cep da winehouse.");
        if ($scope.DadosWinehouse.phone.trim()=="") return bootbox.alert("Digite o telefone da winehouse.");
        if ($scope.DadosWinehouse.cnpj.trim()=="") return bootbox.alert("Digite o CNPJ da winehouse.");
        if ($scope.DadosWinehouse.tax_type=="") return bootbox.alert("Selecione o regime tributário.");

        $scope.load = true;
        $http.post(APIBaseUrl+"/winehouse/save", $scope.DadosWinehouse).then(function(res) {
            $http.get(APIBaseUrl+"/winehouse/get/id/"+$scope.DadosWinehouse.id).then(function(res) {
                $scope.load = false;
                Winehouse.set(res.data);
                $scope.DadosWinehouse = angular.copy(res.data);
            }, function(res) {
                $scope.load = false;
            });
        }, function(res) {
            $scope.load = false;
            bootbox.alert("Não foi possível alterar o winehouse. Tente novamente.");
        });
    };
});

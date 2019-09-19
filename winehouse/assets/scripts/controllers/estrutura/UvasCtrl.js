mainProject.controller('UvasCtrl', function($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarUvas();
    });
    $scope.ListaPaises = [];
    $scope.ListaRegioes = [];
    $scope.ListaUvas = [];
    $scope.NovaUva = {
        name: "",
        description: "",
        id_image: ""
    };
    $scope.NovaUvaFlow = false;
    $scope.UvaAlterarFlow = false;
    $scope.uploadFlag = false;
    $scope.ToggleUploadFlag = function(flag) {
        $scope.uploadFlag = flag;
    };

    $scope.DefineImagemNovaUva = function($message, $flow) {
        $scope.NovaUva.id_image = $message;
        $scope.NovaUvaFlow = $flow;
    };

    $scope.DefineImagemUvaAlterar = function($message, $flow) {
        $scope.UvaAlterar.id_image = $message;
        $scope.UvaAlterarFlow = $flow;
    };

    $scope.CancelaImagem = function($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.ListarUvas = function() {
        $scope.uvasLoad = true;
        $http.get(APIBaseUrl+"/grape/list").then(function(res) {
            $scope.uvasLoad = false;
            $scope.ListaUvas = res.data;
        }, function(res) {
            $scope.uvasLoad = false;
            $scope.ListaUvas = [];
        });
    };

    $scope.CriaNovaUva = function() {
        if (!$scope.NovaUva.name.trim().length) return bootbox.alert("Digite o nome da uva.");

        $scope.novaUvaLoad = true;
        $http.post(APIBaseUrl+"/grape/create", $scope.NovaUva).then(function(res) {
            $scope.novaUvaLoad = false;
            $scope.NovaUva = {
                name: "",
                description: "",
                id_image: ""
            };
            if ($scope.NovaUvaFlow) $scope.NovaUvaFlow.files = [];
            $scope.ListarUvas();
        }, function(res) {
            $scope.novaUvaLoad = false;
            bootbox.alert("Não foi possível criar a uva. Tente novamente.");
        });
    };

    $scope.ExcluiUva = function(regiao) {
        bootbox.confirm("Deseja realmente excluir esta uva?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function(ans) {
            if (ans) {
                $scope.uvasLoad = true;
                $http.get(APIBaseUrl+"/grape/delete/id/"+regiao.id).then(function(res) {
                    $scope.uvasLoad = false;
                    $scope.ListarUvas();
                }, function(res) {
                    $scope.uvasLoad = false;
                    bootbox.alert("Não foi possível excluír a uva. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraUva = function(uva) {
        $scope.UvaAlterar = angular.copy(uva);
    };

    $scope.SalvarUva = function() {
        if (!$scope.UvaAlterar) return bootbox.alert("Não há uma uva sendo alterada.");
        if (!$scope.UvaAlterar.name.trim().length) return bootbox.alert("Digite o nome da uva.");

        $scope.alterarUvaLoad = true;
        $http.post(APIBaseUrl+"/grape/save", $scope.UvaAlterar).then(function(res) {
            $scope.alterarUvaLoad = false;
            $scope.UvaAlterar = false;
            if ($scope.UvaAlterarFlow) $scope.UvaAlterarFlow.files = [];
            $scope.ListarUvas();
        }, function(res) {
            $scope.alterarUvaLoad = false;
            bootbox.alert("Não foi possível alterar a uva. Tente novamente.");
        });
    };
});

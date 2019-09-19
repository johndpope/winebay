mainProject.controller('ImportadorCtrl', function($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarImportadores();
    });
    $scope.ListaImportadores = [];
    $scope.importadoresLoad = false;
    $scope.novoImportadorLoad = false;
    $scope.alterarImportadorLoad = false;
    $scope.NovoImportador = {
        name: "",
        description: "",
        address: "",
        phone: "",
        email: "",
        id_image: ""
    };
    $scope.NovoImportadorFlow = false;
    $scope.ImportadorAlterarFlow = false;
    $scope.ImportadorAlterar = false;
    $scope.dataTable = false;
    $scope.uploadFlag = false;
    $scope.ToggleUploadFlag = function(flag) {
        $scope.uploadFlag = flag;
    };

    $scope.DefineImagemNovoImportador = function($message, $flow) {
        $scope.NovoImportador.id_image = $message;
        $scope.NovoImportadorFlow = $flow;
    };

    $scope.DefineImagemImportadorAlterar = function($message, $flow) {
        $scope.ImportadorAlterar.id_image = $message;
        $scope.ImportadorAlterarFlow = $flow;
    };

    $scope.CancelaImagem = function($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.CriaNovoImportador = function() {
        if ($scope.NovoImportador.name.trim()=="") return bootbox.alert("Digite o nome do importador.");
        $scope.novoImportadorLoad = true;
        $http.post(APIBaseUrl+"/importer/create", $scope.NovoImportador).then(function(res) {
            $scope.novoImportadorLoad = false;
            $scope.NovoImportador = {
                name: "",
                description: "",
                address: "",
                phone: "",
                email: "",
                id_image: ""
            };
            if ($scope.NovoImportadorFlow) $scope.NovoImportadorFlow.files = [];
            $scope.ListarImportadores();
        }, function(res) {
            $scope.novoImportadorLoad = false;
            bootbox.alert("Não foi possível cadastrar o importador. Tente novamente.");
        });
    };

    $scope.ListarImportadores = function() {
        $scope.importadoresLoad = true;
        $http.get(APIBaseUrl+"/importer/list").then(function(res) {
            $scope.importadoresLoad = false;
            $scope.ListaImportadores = res.data;
        }, function(res) {
            $scope.importadoresLoad = false;
            $scope.ListaImportadores = [];
        });
    };

    $scope.ExcluiImportador = function(importador) {
        bootbox.confirm("Deseja realmente excluir este importador?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function(ans) {
            if (ans) {
                $scope.importadoresLoad = true;
                $http.get(APIBaseUrl+"/importer/delete/id/"+importador.id).then(function(res) {
                    $scope.importadoresLoad = false;
                    $scope.ListarImportadores();
                }, function(res) {
                    $scope.importadoresLoad = false;
                    bootbox.alert("Não foi possível excluír o importador. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraImportador = function(importador) {
        $scope.ImportadorAlterar = angular.copy(importador);
    };

    $scope.SalvarImportador = function() {
        if (!$scope.ImportadorAlterar) return bootbox.alert("Não há um importador sendo alterado.");
        if ($scope.ImportadorAlterar.name.trim()=="") return bootbox.alert("Digite o nome do importador.");

        $scope.alterarImportadorLoad = true;
        $http.post(APIBaseUrl+"/importer/save", $scope.ImportadorAlterar).then(function(res) {
            $scope.alterarImportadorLoad = false;
            if ($scope.ImportadorAlterarFlow) $scope.ImportadorAlterarFlow.files = [];
            $scope.ImportadorAlterar = false;
            $scope.ListarImportadores();
        }, function(res) {
            $scope.alterarImportadorLoad = false;
            bootbox.alert("Não foi possível alterar o importador. Tente novamente.");
        });
    };
});

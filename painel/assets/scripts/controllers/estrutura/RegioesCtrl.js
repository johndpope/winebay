mainProject.controller('RegioesCtrl', function($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarPaises();
        $scope.ListarRegioes();
    });
    $scope.ListaPaises = [];
    $scope.ListaRegioes = [];
    $scope.NovaRegiao = {
        name: "",
        id_country: "",
        description: "",
        id_image: ""
    };
    $scope.NovaRegiaoFlow = false;
    $scope.RegiaoAlterarFlow = false;
    $scope.uploadFlag = false;
    $scope.ToggleUploadFlag = function(flag) {
        $scope.uploadFlag = flag;
    };

    $scope.DefineImagemNovaRegiao = function($message, $flow) {
        $scope.NovaRegiao.id_image = $message;
        $scope.NovaRegiaoFlow = $flow;
    };

    $scope.DefineImagemRegiaoAlterar = function($message, $flow) {
        $scope.RegiaoAlterar.id_image = $message;
        $scope.RegiaoAlterarFlow = $flow;
    };

    $scope.CancelaImagem = function($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.ListarPaises = function() {
        $scope.novaRegiaoLoad = true;
        $http.get(APIBaseUrl+"/country/list").then(function(res) {
            $scope.novaRegiaoLoad = false;
            $scope.ListaPaises = res.data;
        }, function(res) {
            $scope.novaRegiaoLoad = false;
            $scope.ListaPaises = [];
        });
    };

    $scope.ListarRegioes = function() {
        $scope.regioesLoad = true;
        $http.get(APIBaseUrl+"/region/list").then(function(res) {
            $scope.regioesLoad = false;
            $scope.ListaRegioes = res.data;
        }, function(res) {
            $scope.regioesLoad = false;
            $scope.ListaRegioes = [];
        });
    };

    $scope.CriaNovaRegiao = function() {
        if (!$scope.NovaRegiao.name.trim().length) return bootbox.alert("Digite o nome da região.");
        if ($scope.NovaRegiao.id_country=="") return bootbox.alert("Selecione um país para a região.");

        $scope.novaRegiaoLoad = true;
        $http.post(APIBaseUrl+"/region/create", $scope.NovaRegiao).then(function(res) {
            $scope.novaRegiaoLoad = false;
            $scope.NovaRegiao = {
                name: "",
                id_country: "",
                description: "",
                id_image: ""
            };
            if ($scope.NovaRegiaoFlow) $scope.NovaRegiaoFlow.files = [];
            $scope.ListarRegioes();
        }, function(res) {
            $scope.novaRegiaoLoad = false;
            bootbox.alert("Não foi possível criar a região. Tente novamente.");
        });
    };

    $scope.ExcluiRegiao = function(regiao) {
        bootbox.confirm("Deseja realmente excluir esta região?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function(ans) {
            if (ans) {
                $scope.regioesLoad = true;
                $http.get(APIBaseUrl+"/region/delete/id/"+regiao.id).then(function(res) {
                    $scope.regioesLoad = false;
                    $scope.ListarRegioes();
                }, function(res) {
                    $scope.regioesLoad = false;
                    bootbox.alert("Não foi possível excluír a região. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraRegiao = function(regiao) {
        regiao.id_country += "";
        $scope.RegiaoAlterar = angular.copy(regiao);
    };

    $scope.SalvarRegiao = function() {
        if (!$scope.RegiaoAlterar) return bootbox.alert("Não há uma região sendo alterada.");
        if (!$scope.RegiaoAlterar.name.trim().length) return bootbox.alert("Digite o nome da região.");
        if ($scope.RegiaoAlterar.id_country=="") return bootbox.alert("Selecione o país da região.");

        $scope.alterarRegiaoLoad = true;
        $http.post(APIBaseUrl+"/region/save", $scope.RegiaoAlterar).then(function(res) {
            $scope.alterarRegiaoLoad = false;
            $scope.RegiaoAlterar = false;
            if ($scope.RegiaoAlterarFlow) $scope.RegiaoAlterarFlow.files = [];
            $scope.ListarRegioes();
        }, function(res) {
            $scope.alterarRegiaoLoad = false;
            bootbox.alert("Não foi possível alterar a região. Tente novamente.");
        });
    };
});

mainProject.controller('TipicidadesCtrl', function($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarTipicidades();
    });
    $scope.ListaPaises = [];
    $scope.ListaRegioes = [];
    $scope.ListaTipicidades = [];
    $scope.NovaTipicidade = {
        name: "",
        description: "",
        id_image: ""
    };
    $scope.NovaTipicidadeFlow = false;
    $scope.TipicidadeAlterarFlow = false;
    $scope.uploadFlag = false;
    $scope.ToggleUploadFlag = function(flag) {
        $scope.uploadFlag = flag;
    };

    $scope.DefineImagemNovaTipicidade = function($message, $flow) {
        $scope.NovaTipicidade.id_image = $message;
        $scope.NovaTipicidadeFlow = $flow;
    };

    $scope.DefineImagemTipicidadeAlterar = function($message, $flow) {
        $scope.TipicidadeAlterar.id_image = $message;
        $scope.TipicidadeAlterarFlow = $flow;
    };

    $scope.CancelaImagem = function($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.ListarTipicidades = function() {
        $scope.tipicidadesLoad = true;
        $http.get(APIBaseUrl+"/tipicity/list").then(function(res) {
            $scope.tipicidadesLoad = false;
            $scope.ListaTipicidades = res.data;
        }, function(res) {
            $scope.tipicidadesLoad = false;
            $scope.ListaTipicidades = [];
        });
    };

    $scope.CriaNovaTipicidade = function() {
        if (!$scope.NovaTipicidade.name.trim().length) return bootbox.alert("Digite o nome da tipicidade.");

        $scope.novaTipicidadeLoad = true;
        $http.post(APIBaseUrl+"/tipicity/create", $scope.NovaTipicidade).then(function(res) {
            $scope.novaTipicidadeLoad = false;
            $scope.NovaTipicidade = {
                name: "",
                description: "",
                id_image: ""
            };
            if ($scope.NovaTipicidadeFlow) $scope.NovaTipicidadeFlow.files = [];
            $scope.ListarTipicidades();
        }, function(res) {
            $scope.novaTipicidadeLoad = false;
            bootbox.alert("Não foi possível criar a tipicidade. Tente novamente.");
        });
    };

    $scope.ExcluiTipicidade = function(regiao) {
        bootbox.confirm("Deseja realmente excluir esta tipicidade?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function(ans) {
            if (ans) {
                $scope.tipicidadesLoad = true;
                $http.get(APIBaseUrl+"/tipicity/delete/id/"+regiao.id).then(function(res) {
                    $scope.tipicidadesLoad = false;
                    $scope.ListarTipicidades();
                }, function(res) {
                    $scope.tipicidadesLoad = false;
                    bootbox.alert("Não foi possível excluír a tipicidade. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraTipicidade = function(tipicidade) {
        $scope.TipicidadeAlterar = angular.copy(tipicidade);
    };

    $scope.SalvarTipicidade = function() {
        if (!$scope.TipicidadeAlterar) return bootbox.alert("Não há uma tipicidade sendo alterada.");
        if (!$scope.TipicidadeAlterar.name.trim().length) return bootbox.alert("Digite o nome da tipicidade.");

        $scope.alterarTipicidadeLoad = true;
        $http.post(APIBaseUrl+"/tipicity/save", $scope.TipicidadeAlterar).then(function(res) {
            $scope.alterarTipicidadeLoad = false;
            $scope.TipicidadeAlterar = false;
            if ($scope.TipicidadeAlterarFlow) $scope.TipicidadeAlterarFlow.files = [];
            $scope.ListarTipicidades();
        }, function(res) {
            $scope.alterarTipicidadeLoad = false;
            bootbox.alert("Não foi possível alterar a tipicidade. Tente novamente.");
        });
    };
});

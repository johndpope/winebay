mainProject.controller('CountriesCtrl', function($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarPaises();
    });
    $scope.ListaPaises = [];
    $scope.paisesLoad = false;
    $scope.novoPaisLoad = false;
    $scope.alterarPaisLoad = false;
    $scope.NomeNovoPais = "";
    $scope.SiglaNovoPais = "";
    $scope.ImagemNovoPais = "";
    $scope.NovoPaisFlow = false;
    $scope.PaisAlterarFlow = false;
    $scope.PaisAlterar = false;
    $scope.dataTable = false;
    $scope.uploadFlag = false;
    $scope.ToggleUploadFlag = function(flag) {
        $scope.uploadFlag = flag;
    };

    $scope.DefineImagemNovoPais = function($message, $flow) {
        $scope.ImagemNovoPais = $message;
        $scope.NovoPaisFlow = $flow;
    };

    $scope.DefineImagemPaisAlterar = function($message, $flow) {
        $scope.PaisAlterar.id_image = $message;
        $scope.PaisAlterarFlow = $flow;
    };

    $scope.CancelaImagem = function($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.NovoPais = function() {
        if ($scope.NomeNovoPais.trim()=="") return bootbox.alert("Digite o nome do país.");
        if ($scope.SiglaNovoPais.trim()=="") return bootbox.alert("Digite a sigla do país.");
        if (!$scope.SiglaNovoPais.trim().length>3) return bootbox.alert("A sigla do país deve contr até 3 caracteres.");
        $scope.novoPaisLoad = true;
        $http.post(APIBaseUrl+"/country/create", {name:$scope.NomeNovoPais.trim(), shortname:$scope.SiglaNovoPais.trim(), image:$scope.ImagemNovoPais}).then(function(res) {
            $scope.novoPaisLoad = false;
            $scope.NomeNovoPais = "";
            $scope.SiglaNovoPais = "";
            $scope.ImagemNovoPais = "";
            if ($scope.NovoPaisFlow) $scope.NovoPaisFlow.files = [];
            $scope.ListarPaises();
        }, function(res) {
            $scope.novoPaisLoad = false;
            bootbox.alert("Não foi possível cadastrar o país. Tente novamente.");
        });
    };

    $scope.ListarPaises = function() {
        $scope.paisesLoad = true;
        $http.get(APIBaseUrl+"/country/list").then(function(res) {
            $scope.paisesLoad = false;
            $scope.ListaPaises = res.data;
        }, function(res) {
            $scope.paisesLoad = false;
            $scope.ListaPaises = [];
        });
    };

    $scope.ExcluiPais = function(pais) {
        bootbox.confirm("Deseja realmente excluir este país?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function(ans) {
            if (ans) {
                $scope.paisesLoad = true;
                $http.get(APIBaseUrl+"/country/delete/id/"+pais.id).then(function(res) {
                    $scope.paisesLoad = false;
                    $scope.ListarPaises();
                }, function(res) {
                    $scope.paisesLoad = false;
                    bootbox.alert("Não foi possível excluír o país. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraPais = function(pais) {
        $scope.PaisAlterar = angular.copy(pais);
    };

    $scope.SalvarPais = function() {
        if (!$scope.PaisAlterar) return bootbox.alert("Não há um país sendo alterado.");
        if (!$scope.PaisAlterar.name.trim().length) return bootbox.alert("Digite o nome do país.");
        if (!$scope.PaisAlterar.shortname.trim().length) return bootbox.alert("Digite a sigla do país.");
        if (!$scope.PaisAlterar.shortname.trim().length>3) return bootbox.alert("A sigla do país deve contr até 3 caracteres.");

        $scope.alterarPaisLoad = true;
        $http.post(APIBaseUrl+"/country/save", $scope.PaisAlterar).then(function(res) {
            $scope.alterarPaisLoad = false;
            if ($scope.PaisAlterarFlow) $scope.PaisAlterarFlow.files = [];
            $scope.PaisAlterar = false;
            $scope.ListarPaises();
        }, function(res) {
            $scope.alterarPaisLoad = false;
            bootbox.alert("Não foi possível alterar o país. Tente novamente.");
        });
    };
});

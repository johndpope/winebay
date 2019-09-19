mainProject.controller("EmbalagensCtrl", function ($scope, $state, $http, User) {
    $scope.$on("$viewContentLoaded", function () {
        $scope.ListarEmbalagens();
    });
    $scope.ListaEmbalagens = [];
    $scope.embLoad = false;
    $scope.novaEmbLoad = false;
    $scope.alterarEmbLoad = false;
    $scope.EmbAlterar = false;
    $scope.NomeNovaEmbalagem = "";
    $scope.TamanhoNovaEmbalagem = 1;
    $scope.AlturaNovaEmbalagem = 0;
    $scope.LarguraNovaEmbalagem = 0;
    $scope.ProfundidadeNovaEmbalagem = 0;
    $scope.PesoNovaEmbalagem = 0;


    $scope.NovaEmbalagem = function () {
        if ($scope.NomeNovaEmbalagem.trim() == "") return bootbox.alert("Digite o nome da embalagem.");
        if (isNaN($scope.TamanhoNovaEmbalagem) || (+$scope.TamanhoNovaEmbalagem < 1)) return bootbox.alert("Digite o tamanho da embalagem.");
        if (isNaN($scope.AlturaNovaEmbalagem) || (+$scope.AlturaNovaEmbalagem < 1)) return bootbox.alert("Digite a altura em CM da embalagem.");
        if (isNaN($scope.LarguraNovaEmbalagem) || (+$scope.LarguraNovaEmbalagem < 1)) return bootbox.alert("Digite a largura em CM da embalagem.");
        if (isNaN($scope.ProfundidadeNovaEmbalagem) || (+$scope.ProfundidadeNovaEmbalagem < 1)) return bootbox.alert("Digite a profundidade em CM da embalagem.");
        if (isNaN($scope.PesoNovaEmbalagem) || (+$scope.PesoNovaEmbalagem < 1)) return bootbox.alert("Digite o peso em gramas da embalagem.");

        $scope.novaEmbLoad = true;
        $http.post(APIBaseUrl + "/package/create", { name: $scope.NomeNovaEmbalagem.trim(), size: $scope.TamanhoNovaEmbalagem }).then(function (res) {
            $scope.novaEmbLoad = false;
            $scope.NomeNovaEmbalagem = "";
            $scope.TamanhoNovaEmbalagem = 1;
            $scope.AlturaNovaEmbalagem = 0;
            $scope.LarguraNovaEmbalagem = 0;
            $scope.ProfundidadeNovaEmbalagem = 0;
            $scope.PesoNovaEmbalagem = 0;
            $scope.ListarEmbalagens();
        }, function (res) {
            $scope.novaEmbLoad = false;
            bootbox.alert("Não foi possível cadastrar a embalagem. Tente novamente.");
        });
    };

    $scope.ListarEmbalagens = function () {
        $scope.embLoad = true;
        $http.get(APIBaseUrl + "/package/list").then(function (res) {
            $scope.embLoad = false;
            $scope.ListaEmbalagens = res.data;
        }, function (res) {
            $scope.embLoad = false;
            $scope.ListaEmbalagens = [];
        });
    };

    $scope.ExcluiEmbalagem = function (embalagem) {
        bootbox.confirm("Deseja realmente excluir esta embalagem?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function (ans) {
            if (ans) {
                $scope.embLoad = true;
                $http.get(APIBaseUrl + "/package/delete/id/" + embalagem.id).then(function (res) {
                    $scope.embLoad = false;
                    $scope.ListarEmbalagens();
                }, function (res) {
                    $scope.embLoad = false;
                    bootbox.alert("Não foi possível excluír a embalagem. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraEmbalagem = function (embalagem) {
        $scope.EmbAlterar = angular.copy(embalagem);
    };

    $scope.SalvarEmbalagem = function () {
        if (!$scope.EmbAlterar) return bootbox.alert("Não há uma embalagem sendo alterada.");
        if (!$scope.EmbAlterar.name.trim().length) return bootbox.alert("Digite o nome da embalagem.");
        if (isNaN($scope.EmbAlterar.size) || (+$scope.EmbAlterar.size < 1)) return bootbox.alert("Digite o tamanho da embalagem.");
        if (isNaN($scope.EmbAlterar.width) || (+$scope.EmbAlterar.width < 1)) return bootbox.alert("Digite a largura em CM da embalagem.");
        if (isNaN($scope.EmbAlterar.height) || (+$scope.EmbAlterar.height < 1)) return bootbox.alert("Digite a altura em CM da embalagem.");
        if (isNaN($scope.EmbAlterar.depth) || (+$scope.EmbAlterar.depth < 1)) return bootbox.alert("Digite a profundidade em CM da embalagem.");
        if (isNaN($scope.EmbAlterar.weight) || (+$scope.EmbAlterar.weight < 1)) return bootbox.alert("Digite o peso em gramas da embalagem.");

        $scope.alterarEmbLoad = true;
        $http.post(APIBaseUrl + "/package/save", $scope.EmbAlterar).then(function (res) {
            $scope.alterarEmbLoad = false;
            $scope.EmbAlterar = false;
            $scope.ListarEmbalagens();
        }, function (res) {
            $scope.alterarEmbLoad = false;
            bootbox.alert("Não foi possível alterar a embalagem. Tente novamente.");
        });
    };
});
mainProject.controller('ProdutorCtrl', function($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarProdutores();
    });
    $scope.ListaProdutores = [];
    $scope.produtoresLoad = false;
    $scope.novoProdutorLoad = false;
    $scope.alterarProdutorLoad = false;
    $scope.NovoProdutor = {
        name: "",
        description: "",
        address: "",
        phone: "",
        email: "",
        id_image: ""
    };
    $scope.NovoProdutorFlow = false;
    $scope.ProdutorAlterarFlow = false;
    $scope.ProdutorAlterar = false;
    $scope.dataTable = false;
    $scope.uploadFlag = false;
    $scope.ToggleUploadFlag = function(flag) {
        $scope.uploadFlag = flag;
    };

    $scope.DefineImagemNovoProdutor = function($message, $flow) {
        $scope.NovoProdutor.id_image = $message;
        $scope.NovoProdutorFlow = $flow;
    };

    $scope.DefineImagemProdutorAlterar = function($message, $flow) {
        $scope.ProdutorAlterar.id_image = $message;
        $scope.ProdutorAlterarFlow = $flow;
    };

    $scope.CancelaImagem = function($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.CriaNovoProdutor = function() {
        if ($scope.NovoProdutor.name.trim()=="") return bootbox.alert("Digite o nome do produtor.");
        if ($scope.NovoProdutor.address.trim()=="") return bootbox.alert("Digite o endereço do produtor.");
        if ($scope.NovoProdutor.phone.trim()=="") return bootbox.alert("Digite o telefone do produtor.");
        $scope.novoProdutorLoad = true;
        $http.post(APIBaseUrl+"/productor/create", $scope.NovoProdutor).then(function(res) {
            $scope.novoProdutorLoad = false;
            $scope.NovoProdutor = {
                name: "",
                description: "",
                address: "",
                phone: "",
                email: "",
                id_image: ""
            };
            if ($scope.NovoProdutorFlow) $scope.NovoProdutorFlow.files = [];
            $scope.ListarProdutores();
        }, function(res) {
            $scope.novoProdutorLoad = false;
            bootbox.alert("Não foi possível cadastrar o produtor. Tente novamente.");
        });
    };

    $scope.ListarProdutores = function() {
        $scope.produtoresLoad = true;
        $http.get(APIBaseUrl+"/productor/list").then(function(res) {
            $scope.produtoresLoad = false;
            $scope.ListaProdutores = res.data;
        }, function(res) {
            $scope.produtoresLoad = false;
            $scope.ListaProdutores = [];
        });
    };

    $scope.ExcluiProdutor = function(produtor) {
        bootbox.confirm("Deseja realmente excluir este produtor?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function(ans) {
            if (ans) {
                $scope.produtoresLoad = true;
                $http.get(APIBaseUrl+"/productor/delete/id/"+produtor.id).then(function(res) {
                    $scope.produtoresLoad = false;
                    $scope.ListarProdutores();
                }, function(res) {
                    $scope.produtoresLoad = false;
                    bootbox.alert("Não foi possível excluír o produtor. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraProdutor = function(produtor) {
        $scope.ProdutorAlterar = angular.copy(produtor);
    };

    $scope.SalvarProdutor = function() {
        if (!$scope.ProdutorAlterar) return bootbox.alert("Não há um produtor sendo alterado.");
        if ($scope.ProdutorAlterar.name.trim()=="") return bootbox.alert("Digite o nome do produtor.");
        if ($scope.ProdutorAlterar.address.trim()=="") return bootbox.alert("Digite o endereço do produtor.");
        if ($scope.ProdutorAlterar.phone.trim()=="") return bootbox.alert("Digite o telefone do produtor.");

        $scope.alterarProdutorLoad = true;
        $http.post(APIBaseUrl+"/productor/save", $scope.ProdutorAlterar).then(function(res) {
            $scope.alterarProdutorLoad = false;
            if ($scope.ProdutorAlterarFlow) $scope.ProdutorAlterarFlow.files = [];
            $scope.ProdutorAlterar = false;
            $scope.ListarProdutores();
        }, function(res) {
            $scope.alterarProdutorLoad = false;
            bootbox.alert("Não foi possível alterar o produtor. Tente novamente.");
        });
    };
});

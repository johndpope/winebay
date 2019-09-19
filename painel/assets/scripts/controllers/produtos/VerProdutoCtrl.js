mainProject.controller('VerProdutoCtrl', function($scope, $stateParams, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        if ($stateParams.id_produto) {
            $scope.BuscarDadosProduto($stateParams.id_produto);
        } else {
            $state.go("produtos");
        }
    });
    $scope.produtoLoad = false;
    $scope.dadosProduto = false;

    $scope.BuscarDadosProduto = function(id_produto) {
        $scope.produtoLoad = true;
        $http.get(APIBaseUrl+"/product/get/id/"+id_produto).then(function(res) {
            $scope.produtoLoad = false;
            $scope.dadosProduto = res.data;
            for (var i in $scope.dadosProduto) {
                if ($scope.dadosProduto[i]==null) {
                    $scope.dadosProduto[i] = "";
                }
                $scope.dadosProduto[i] += "";
            }
            $scope.ListarPaises();
            $scope.ListarRegioes();
            $scope.ListarUvas();
            $scope.ListarProdutores();
            $scope.ListarTipicidades();
        }, function(res) {
            $scope.produtoLoad = false;
            $scope.dadosProduto = false;
            bootbox.alert("Não foi possível buscar os dados do produto.");
            $state.go("produtos");
        });
    };

    $scope.ListarPaises = function() {
        $scope.produtoLoad = true;
        $http.get(APIBaseUrl+"/country/list").then(function(res) {
            $scope.produtoLoad = false;
            $scope.ListaPaises = res.data;
        }, function(res) {
            $scope.produtoLoad = false;
            $scope.ListaPaises = [];
        });
    };

    $scope.ListarRegioes = function() {
        $scope.produtoLoad = true;
        $http.get(APIBaseUrl+"/region/list").then(function(res) {
            $scope.produtoLoad = false;
            $scope.ListaRegioes = res.data;
        }, function(res) {
            $scope.produtoLoad = false;
            $scope.ListaRegioes = [];
        });
    };

    $scope.ListarUvas = function() {
        $scope.produtoLoad = true;
        $http.get(APIBaseUrl+"/grape/list").then(function(res) {
            $scope.produtoLoad = false;
            $scope.ListaUvas = res.data;
        }, function(res) {
            $scope.produtoLoad = false;
            $scope.ListaUvas = [];
        });
    };

    $scope.ListarProdutores = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl+"/productor/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaProdutores = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaProdutores = [];
        });
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


    $scope.CancelaImagem = function($flow, obj) {
        $flow.cancel();
        obj = "";
    };


    $scope.RemoveImagem = function() {
        $scope.dadosProduto.image_thumb = null;
        $scope.dadosProduto.id_image_thumb = null;
        $scope.SalvarProduto();
    }

    $scope.SalvarProduto = function() {
        if ($scope.dadosProduto.name.trim()=="") return bootbox.alert("Digite o nome do produto.");
        if (isNaN($scope.dadosProduto.graduation)) return bootbox.alert("A graduação deve ser um número válido.");
        if (isNaN($scope.dadosProduto.size)) return bootbox.alert("O tamanho deve ser um número válido.");

        $scope.produtoLoad = true;
        $http.post(APIBaseUrl+"/product/save", $scope.dadosProduto).then(function(res){
            $scope.produtoLoad = false;
            bootbox.alert("Produto alterado com sucesso.");
        }, function(res) {
            $scope.produtoLoad = false;
            bootbox.alert("Não foi possível alterar o produto. Tente novamente.");
        });
    };
});

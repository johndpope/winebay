mainProject.controller('CriarProdutoCtrl', function($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarPaises();
        $scope.ListarRegioes();
        $scope.ListarUvas();
        $scope.ListarProdutores();
        $scope.ListarTipicidades();
    });
    $scope.novoProdutoLoad = false;
    $scope.dadosProduto = {
        name: '',
        graduation: '',
        size: ''
    };

    $scope.ListarPaises = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl+"/country/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaPaises = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaPaises = [];
        });
    };

    $scope.ListarRegioes = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl+"/region/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaRegioes = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaRegioes = [];
        });
    };

    $scope.ListarUvas = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl+"/grape/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaUvas = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
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

    $scope.CriarProduto = function() {
        if ($scope.dadosProduto.name.trim()=="") return bootbox.alert("Digite o nome do produto.");
        if (isNaN($scope.dadosProduto.graduation)) return bootbox.alert("A graduação deve ser um número válido.");
        if (isNaN($scope.dadosProduto.size)) return bootbox.alert("O tamanho deve ser um número válido.");

        $scope.novoProdutoLoad = true;
        $http.post(APIBaseUrl+"/product/create", $scope.dadosProduto).then(function(res){
            $scope.novoProdutoLoad = false;
            bootbox.alert("Produto criado com sucesso.");
            $state.go("produtos");
        }, function(res) {
            $scope.novoProdutoLoad = false;
            bootbox.alert("Não foi possível cadastrar o produto. Tente novamente.");
        });
    };
});

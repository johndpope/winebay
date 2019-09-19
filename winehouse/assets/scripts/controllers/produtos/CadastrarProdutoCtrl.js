mainProject.controller('CadastrarProdutoCtrl', function($scope, $http, $state, Winehouse) {
    $scope.$on('$viewContentLoaded', function() {
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
        size: '',
        id_winehouse_creation: Winehouse.get().id
    };

    $scope.dadosVincular = {
        price: 0,
        quantity: 0,
        crop: ''
    };

    $scope.ListarPaises = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl + "/country/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaPaises = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaPaises = [];
        });
    };

    $scope.ListarRegioes = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl + "/region/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaRegioes = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaRegioes = [];
        });
    };

    $scope.ListarUvas = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl + "/grape/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaUvas = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaUvas = [];
        });
    };

    $scope.ListarProdutores = function() {
        $scope.novoProdutoLoad = true;
        $http.get(APIBaseUrl + "/productor/list").then(function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaProdutores = res.data;
        }, function(res) {
            $scope.novoProdutoLoad = false;
            $scope.ListaProdutores = [];
        });
    };

    $scope.ListarTipicidades = function() {
        $scope.tipicidadesLoad = true;
        $http.get(APIBaseUrl + "/tipicity/list").then(function(res) {
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
        if ($scope.dadosProduto.name.trim() == "") return bootbox.alert("Digite o nome do produto.");
        if (isNaN($scope.dadosProduto.graduation)) return bootbox.alert("A graduação deve ser um número válido.");
        if (isNaN($scope.dadosProduto.size)) return bootbox.alert("O tamanho deve ser um número válido.");
        if (isNaN($scope.dadosVincular.price)) return bootbox.alert("O preço deve ser um número válido.");
        if ($scope.dadosVincular.price == 0) return bootbox.alert("O preço deve ser maior que zero.");
        if (isNaN($scope.dadosVincular.quantity)) return bootbox.alert("A quantidade em estoque deve ser um número válido.");
        if ($scope.dadosVincular.quantity == 0) return bootbox.alert("A quantidade deve ser maior que zero.");
        if ($scope.dadosVincular.crop.trim() == "") return bootbox.alert("Digite a safra do produto.");

        $scope.novoProdutoLoad = true;
        $http.post(APIBaseUrl + "/product/create", $scope.dadosProduto).then(function(res) {
            var novoProdutoVincular = {
                id_product: res.data,
                id_winehouse: Winehouse.get().id,
                price: $scope.dadosVincular.price,
                quantity: $scope.dadosVincular.quantity,
                crop: $scope.dadosVincular.crop
            };
            $http.post(APIBaseUrl + "/winehouse/addproductlist", { list: [novoProdutoVincular] }).then(function(res) {
                $scope.novoProdutoLoad = false;
                bootbox.alert("Produto criado com sucesso.");
                $state.go("produtos");
            }, function(res) {
                $scope.novoProdutoLoad = false;
                bootbox.alert("O produto foi criado, mas não foi possível vinculas à winehouse. Tente novamente.");
                $state.go("produtos");
            });
        }, function(res) {
            $scope.novoProdutoLoad = false;
            bootbox.alert("Não foi possível cadastrar o produto. Tente novamente.");
        });
    };
});
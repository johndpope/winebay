mainProject.controller('NovoProdutoCtrl', function ($scope, $http, $state, Winehouse) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarProdutos();
    });
    $scope.load = false;
    $scope.ListaProdutos = [];
    $scope.ProductFilter = {};
    $scope.NovoWinehouseProduto = {
        id_product: false,
        id_winehouse: Winehouse.get().id,
        price: '',
        quantity: ''
    }

    $scope.ListarProdutos = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/product/winehouselist/notid/" + Winehouse.get().id).then(function (res) {
            $scope.load = false;
            $scope.ListaProdutos = res.data;
        }, function (res) {
            $scope.load = false;
            $scope.ListaProdutos = [];
        });
    };

    $scope.DefineProduto = function (produto) {
        $scope.NovoWinehouseProduto.id_product = produto.id;
        produto.selecionado = true;
        $scope.ProductFilter = produto;
    };

    $scope.RemoveProduto = function (produto) {
        $scope.NovoWinehouseProduto.id_product = false;
        produto.selecionado = false;
        $scope.ProductFilter = {};
    };

    $scope.MostraInfo = function (produto) {
        bootbox.dialog({
            title: "Informações do Produto",
            message: '<div class="row">\
            <div class="form-group col-sm-12"><label>Produto:</label><br/>' + produto.name + '</div>\
            <div class="form-group col-sm-4"><label>Graduação:</label><br/>' + produto.graduation + '</div>\
            <div class="form-group col-sm-4"><label>Tamanho:</label><br/>' + produto.size + 'ml</div>\
            <div class="form-group col-sm-4"><label>Tipicidade:</label><br/>' + produto.tipicity + '</div>\
            <div class="form-group col-sm-4"><label>País:</label><br/>' + produto.country + '</div>\
            <div class="form-group col-sm-4"><label>Região:</label><br/>' + produto.region + '</div>\
            <div class="form-group col-sm-4"><label>Uva:</label><br/>' + produto.grape + '</div>\
            <div class="form-group col-sm-4"><label>Produtor:</label><br/>' + produto.productor + '</div>\
            <div class="form-group col-sm-4"><label>Importador:</label><br/>' + produto.importer + '</div>\
            </div>',
            onEscape: true,
            backdrop: true
        });
    };

    $scope.CriarProduto = function () {
        if (!$scope.NovoWinehouseProduto.id_product) return bootbox.alert("Selecione o produto.");
        $scope.NovoWinehouseProduto.price = +($scope.NovoWinehouseProduto.price.toString().replace(",", "."));
        if (isNaN($scope.NovoWinehouseProduto.price) || (+$scope.NovoWinehouseProduto.price < 0)) return bootbox.alert("Digite o valor do produto!");
        $scope.NovoWinehouseProduto.quantity = +($scope.NovoWinehouseProduto.quantity.toString().replace(',', '.'));
        if (isNaN($scope.NovoWinehouseProduto.quantity) || (+$scope.NovoWinehouseProduto.quantity < 1)) return bootbox.alert("Digite a quantidade do produto!");

        $scope.load = true;
        $http.post(APIBaseUrl + "/winehouse/addproduct", $scope.NovoWinehouseProduto).then(function (res) {
            $scope.load = false;
            bootbox.alert("Produto criado com sucesso.");
            $state.go("produtos");
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível criar o produto. Tente novamente.");
        });
    };
});

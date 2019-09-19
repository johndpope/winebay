mainProject.controller('NovoProdutoCtrl', function ($scope, $http, $state, Winehouse, $filter) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarProdutos();
    });
    $scope.load = false;
    $scope.ListaProdutos = [];
    $scope.ProductFilter = {};

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

    $scope.MostraInfo = function (produto) {
        bootbox.dialog({
            title: "Informações do Produto",
            message: '<div class="row">\
            <div class="col-sm-9">\
                <div class="row">\
                    <div class="form-group col-sm-12"><label>Produto:</label><br/>' + produto.name + '</div>\
                    <div class="form-group col-sm-6"><label>Graduação:</label><br/>' + produto.graduation + '</div>\
                    <div class="form-group col-sm-6"><label>Tamanho:</label><br/>' + produto.size + 'ml</div>\
                    <div class="form-group col-sm-6"><label>Tipicidade:</label><br/>' + produto.tipicity + '</div>\
                    <div class="form-group col-sm-6"><label>País:</label><br/>' + produto.country + '</div>\
                    <div class="form-group col-sm-6"><label>Região:</label><br/>' + produto.region + '</div>\
                    <div class="form-group col-sm-6"><label>Uva:</label><br/>' + produto.grape + '</div>\
                    <div class="form-group col-sm-6"><label>Produtor:</label><br/>' + produto.productor + '</div>\
                    <div class="form-group col-sm-6"><label>Importador:</label><br/>' + produto.importer + '</div>\
                </div>\
            </div>\
            <div class="col-sm-3">\
                <div class="row">\
                    <div class="form-group col-sm-12">\
                        <img src="' + APIBaseUrl + produto.image_thumb + '" class="img-responsive" style="max-height:300px"/>\
                    </div>\
                </div>\
            </div>\
            </div>',
            onEscape: true,
            backdrop: true
        });
    };

    $scope.CriarProduto = function () {
        var listaProdutos = [];
        angular.forEach($scope.ListaProdutos, function (prod) {
            if (prod.price) {
                prod.price = +(prod.price.toString().replace(",", "."));
                if (!isNaN(prod.price)) {
                    if (prod.quantity) {
                        prod.quantity = +(prod.quantity.toString().replace(',', '.'));
                    }
                    if (!isNaN(prod.quantity)) {
                        if (prod.crop) {
                            prod.crop = prod.crop.trim();
                            listaProdutos.push({
                                id_product: prod.id,
                                id_winehouse: Winehouse.get().id,
                                price: prod.price,
                                quantity: prod.quantity,
                                crop: prod.crop
                            });
                        }
                    }
                }
            }
        });

        $scope.load = true;
        $http.post(APIBaseUrl + "/winehouse/addproductlist", { list: listaProdutos }).then(function (res) {
            $scope.load = false;
            bootbox.alert("Produtos adicionados com sucesso.");
            $state.go("produtos");
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível criar os produtos. Tente novamente.");
        });
    };
});

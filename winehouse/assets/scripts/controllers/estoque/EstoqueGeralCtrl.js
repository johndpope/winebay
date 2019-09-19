mainProject.controller("EstoqueGeralCtrl", function ($scope, $http, Winehouse) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarProdutos();
        angular.element(".input-group.date input").datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });
    });
    $scope.load = false;
    $scope.ListaProdutos = [];
    $scope.FiltroEstoque = {};
    $scope.ListaLancamentos = [];
    $scope.BuscaLancamentos = {
        Inicio: moment().subtract(1, 'week').format("DD/MM/YYYY"),
        Fim: moment().format("DD/MM/YYYY"),
        Produto: ""
    };
    $scope.NovoLancamento = {
        quantity: "",
        description: "",
        type: "add"
    }

    $scope.ListarProdutos = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/product/winehouselist/id/" + Winehouse.get().id).then(function (res) {
            $scope.load = false;
            $scope.ListaProdutos = res.data;
            $scope.BuscaLancamentos.Produto = $scope.ListaProdutos[0].id.toString();
        }, function (res) {
            $scope.load = false;
            $scope.ListaProdutos = [];
        });
    };

    $scope.BuscarLancamentos = function () {
        if ($scope.BuscaLancamentos.Inicio == "") return bootbox.alert("Selecione a data inicial!");
        if ($scope.BuscaLancamentos.Fim == "") return bootbox.alert("Selecione a data final!");
        $scope.ListaLancamentos = [];
        $scope.load = true;
        $http.post(APIBaseUrl + "/estoque/getentries", $scope.BuscaLancamentos).then(function (res) {
            $scope.load = false;
            $scope.ListaLancamentos = res.data;
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível buscar os lançamentos.");
        });
    };

    $scope.DefinirEstoque = function (produto) {
        bootbox.prompt("Insira a nova quantidade de estoque para <strong>" + produto.name + "</strong>:", function (ans) {
            if (ans) {
                if (isNaN(ans)) return bootbox.alert("O novo estoque deve ser um número válido.");
                var novo = Math.abs(ans);
                var type = (produto.quantity < ans ? 'add' : 'del');
                var dadosLancamento = {
                    quantity: (type == "del") ? -Math.abs(produto.quantity-novo) : Math.abs(produto.quantity-novo),
                    description: (type == 'add') ? 'Adição de Estoque' : 'Remoção de Estoque',
                    id_winehouse_product: produto.id,
                    is_manual: true
                }

                $scope.load = true;
                $http.post(APIBaseUrl + "/estoque/addentry", dadosLancamento).then(function (res) {
                    $scope.load = false;
                    $scope.ListarProdutos();
                }, function (res) {
                    $scope.load = false;
                    bootbox.alert("Não foi possível efetuar o lançamento.");
                });
            }
        });
    };

    $scope.EfetivarLancamento = function () {
        if (isNaN($scope.NovoLancamento.quantity) || (+$scope.NovoLancamento.quantity <= 0)) return bootbox.alert("A quantidade deve ser maior que zero.");

        var dadosLancamento = {
            quantity: ($scope.NovoLancamento.type == "del") ? -$scope.NovoLancamento.quantity : $scope.NovoLancamento.quantity,
            description: ($scope.NovoLancamento.description == "") ? (($scope.NovoLancamento.type == 'add') ? 'Adição de Estoque' : 'Remoção de Estoque') : $scope.NovoLancamento.description,
            id_winehouse_product: $scope.BuscaLancamentos.Produto,
            is_manual: true
        }

        $scope.load = true;
        $http.post(APIBaseUrl + "/estoque/addentry", dadosLancamento).then(function (res) {
            $scope.load = false;
            $scope.NovoLancamento = {
                quantity: "",
                description: "",
                type: "add"
            }
            $scope.BuscarLancamentos();
            $scope.ListarProdutos();
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível efetuar o lançamento.");
        });
    };
});
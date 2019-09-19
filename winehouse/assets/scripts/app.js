// var APIBaseUrl = "http://wb-admin-server.local";
var APIBaseUrl = "http://api.winebay.com.br";

var mainProject = angular.module("winebay-admin", [
        'ngSanitize', 'ui.router', 'ui.bootstrap', 'angularUtils.directives.dirPagination',
        'flow', 'daterangepicker', 'frapontillo.bootstrap-switch', 'ngclipboard', 'ngMask'
    ]).config([
        '$compileProvider',
        function($compileProvider) {
            $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|tel|javascript|data):/);
        }
    ])
    .config(['flowFactoryProvider', function(flowFactoryProvider) {
        flowFactoryProvider.defaults = {
            target: APIBaseUrl + '/image/upload',
            permanentErrors: [404, 500, 501],
            testChunks: false
        };
    }])
    .filter("unsafe", function($sce) { return function(val) { return $sce.trustAsHtml(val); } })
    .config(function($stateProvider, $urlRouterProvider) {
        $stateProvider.state('login', {
            url: "/login",
            templateUrl: "/views/login.html",
            controller: "LoginModalCtrl",
        }).state('cadastro', {
            url: "/cadastro",
            templateUrl: "/views/cadastro.html",
            controller: "CadastroModalCtrl",
        }).state('dashboard', {
            url: "/dashboard",
            templateUrl: "/views/dashboard.html",
            controller: "DashboardCtrl"
        }).state('winehouse', {
            url: "/winehouse",
            templateUrl: "/views/winehouse/winehouse.html",
            controller: "WinehouseCtrl"
        }).state('produtos', {
            url: "/produtos",
            templateUrl: "/views/produtos/produtos.html",
            controller: "ProdutosCtrl"
        }).state('cadastrarproduto', {
            url: "/produtos/cadastrar",
            templateUrl: "/views/produtos/cadastrar.html",
            controller: "CadastrarProdutoCtrl"
        }).state('alterarcadastroproduto', {
            url: "/produtos/alterarcadastro/{id_produto}",
            templateUrl: "/views/produtos/alterarcadastro.html",
            controller: "VerProdutoCtrl"
        }).state('novoproduto', {
            url: "/produtos/novo",
            templateUrl: "/views/produtos/criar.html",
            controller: "NovoProdutoCtrl"
        }).state('ofertas', {
            url: "/ofertas",
            templateUrl: "/views/ofertas/ofertas.html",
            controller: "OfertasCtrl"
        }).state('kits', {
            url: "/kits",
            templateUrl: "/views/kits/kits.html",
            controller: "KitsCtrl"
        }).state('novokit', {
            url: "/kits/novo",
            templateUrl: "/views/kits/criar.html",
            controller: "NovoKitCtrl"
        }).state('verkit', {
            url: "/kits/ver/{Id}",
            templateUrl: "/views/kits/ver.html",
            controller: "VerKitCtrl"
        }).state('estoque', {
            url: "/estoque",
            templateUrl: "/views/estoque/geral.html",
            controller: "EstoqueGeralCtrl"
        }).state('alterarproduto', {
            url: "/produtos/alterar/{Id}",
            templateUrl: "/views/produtos/alterar.html",
            controller: "AlterarProdutoCtrl"
        }).state('listapedidos', {
            url: "/pedidos",
            templateUrl: "/views/pedidos/lista.html",
            controller: "ListaPedidosCtrl"
        }).state('verpedido', {
            url: "/pedidos/ver/{Id}",
            templateUrl: "/views/pedidos/ver.html",
            controller: "VerPedidoCtrl"
        });
        $urlRouterProvider.otherwise('login');
    })
    .run(function($rootScope, $state, Winehouse) {
        $rootScope.$on('$stateChangeStart', function(e, to) {
            if (!Winehouse.get() && (to.name != "login")) {
                e.preventDefault();
                $state.go("login");
            }
        });
    })
    .controller("MainController", function($scope, $state, Winehouse) {
        $scope.WinehouseData = false;
        $scope.APIBaseUrl = APIBaseUrl;
        $scope.$watch(function() { return Winehouse.get(); }, function(val) {
            $scope.WinehouseData = val;
            if (!$scope.WinehouseData) {
                $scope.DoLogout();
            }
        });
        $scope.DoLogout = function() {
            Winehouse.set(false);
            $state.go("login");
        };
    });

function alert(string) {
    bootbox.alert(string);
}

Number.prototype.toBRCurrency = function() {
    return this.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

String.prototype.toBRCurrency = function() {
    return (+this).toBRCurrency();
};
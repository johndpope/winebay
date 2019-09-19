// var APIBaseUrl = "http://wb-admin-server.local";

var APIBaseUrl           = "http://api.winebay.com.br";
var PanelUrl             = "http://painel.winebay.com.br";
var WinehouseUrl         = "http://winehouse.winebay.com.br";
var WinehouseRegisterUrl = "http://winehouse.winebay.com.br/#!/cadastro";
var ProjectName          = "WineBay";

var mainProject = angular.module('winebay', ['ngSanitize', 'ui.router', 'ui.bootstrap', 'ngAnimate', 'slickCarousel', 'ngMask', 'angularUtils.directives.dirPagination']).config([
        '$compileProvider',
        function ($compileProvider) {
            $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|tel|javascript):/);
        }
    ]).filter("unsafe", function ($sce) {
        return function (val) {
            return $sce.trustAsHtml(val);
        }
    })
    .config(function ($stateProvider, $urlRouterProvider) {
        $stateProvider
            .state('/', {
                url: "/",
                templateUrl: "/views/home.html",
                controller: "HomeController",
                headerType: 'light'
            })
            .state('cadastro', {
                url: "/cadastro",
                templateUrl: "/views/cadastro.html",
                controller: "CadastroController",
                headerType: 'dark',
                params: {
                    Destination: false
                }
            })
            .state('cadastroredirect', {
                url: "/cadastro/redirect/{Destination}",
                templateUrl: "/views/cadastro.html",
                controller: "CadastroController",
                headerType: 'dark'
            })
            .state('login', {
                url: "/login",
                templateUrl: "/views/login.html",
                controller: "LoginController",
                headerType: 'dark',
                params: {
                    Destination: false
                }
            })
            .state('loginredirect', {
                url: "/login/redirect/{Destination}",
                templateUrl: "/views/login.html",
                controller: "LoginController",
                headerType: 'dark'
            })
            .state('winehouse', {
                url: "/winehouse/{Id_Winehouse}/produto/{Id_Produto}",
                templateUrl: "/views/winehouse.html",
                controller: "WinehouseController",
                headerType: 'light'
            })
            .state('finalizarcompra', {
                url: "/finalizar",
                templateUrl: "/views/finalizarcompra.html",
                controller: "FinalizarCompraController",
                headerType: 'light'
            })
            .state('institucional', {
                url: "/institucional",
                templateUrl: "/views/institucional.html",
                controller: "InstitucionalController",
                headerType: 'light'
            })
            .state('contato', {
                url: "/contato",
                templateUrl: "/views/contato.html",
                controller: "ContatoController",
                headerType: 'light'
            })
            .state('pedido_realizado', {
                url: "/pedido/finalizado",
                templateUrl: "/views/pedido_finalizado.html",
                params: {
                    dadosCompra: false
                },
                controller: "PedidoFinalizadoController",
                headerType: 'light'
            })
            .state('pedidos_cliente', {
                url: "/cliente/pedidos",
                templateUrl: "/views/pedidos_cliente.html",
                controller: "PedidosClienteController",
                headerType: 'light'
            })
            .state('dados_cliente', {
                url: "/cliente/dados",
                templateUrl: "/views/dados_cliente.html",
                controller: "DadosClienteController",
                headerType: 'light'
            })
            .state('buscageral', {
                url: "/busca/{Termo}",
                templateUrl: "/views/busca_geral.html",
                controller: "BuscaGeralController",
                headerType: 'light'
            });
        $urlRouterProvider.otherwise('/');
    })
    .controller('AppController', function ($rootScope, Utils, $window, SmoothScroll, $scope, System, $http, Products) {
        $rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
            Utils.setHeaderType(toState.headerType);
            $scope.ScrollTo("mainBody");
            jQuery("#product-box-container").slideUp();
        });

        $scope.FecharCaixa = function () {
            Products.closeBox();
        };

        // angular.element("body").click(function (e) {
        //     if (!angular.element(e.target).parents("slick").length && !angular.element(e.target).parents("header").length) {
        // Products.closeBox();
        //     }
        // });

        $scope.$on("$viewContentLoaded", function () {
            $scope.BuscarDadosSite();
        });

        $scope.ScrollTo = function (elID) {
            SmoothScroll.to(elID);
        };

        $scope.APIBaseUrl = APIBaseUrl;
        $scope.PanelUrl = PanelUrl;
        $scope.WinehouseUrl = WinehouseUrl;
        $scope.WinehouseRegisterUrl = WinehouseRegisterUrl;
        $scope.ProjectName = ProjectName;
        $scope.DadosSite = {
            main_logo: "",
            footer_logo: "",
            footer_text: "",
            facebook: "",
            instagram: "",
            twitter: "",
            home_banners: [],
            featured_banner: {},
            items: []
        }

        $scope.BuscarDadosSite = function () {
            $http.post(APIBaseUrl + "/website/get", {
                list: Object.keys($scope.DadosSite)
            }).then(
                function (res) {
                    $scope.DadosSite = res.data;
                    $scope.DadosSite.home_banners = JSON.parse($scope.DadosSite.home_banners);
                    $scope.DadosSite.featured_banner = JSON.parse($scope.DadosSite.featured_banner);
                    System.setWebsiteData($scope.DadosSite);
                    angular.element("#preloadoverlay").remove();
                },
                function (res) {}
            );
        };
    });
boxSlickFlag = false;

function alert(string) {
    bootbox.alert(string);
}
Number.prototype.toBRCurrency = function () {
    return this.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
String.prototype.toBRCurrency = function () {
    return (+this).toBRCurrency();
}

function validaCPF(str) {
    var soma = 0,
        resto;
    str = str.replace(/[^\d]+/g, '');
    if (str == "00000000000") return false;
    for (i = 1; i <= 9; i++) soma += parseInt(str.substring(i - 1, i)) * (11 - i);
    resto = (soma * 10) % 11;

    if ((resto == 10) || (resto == 11)) resto = 0;
    if (resto != parseInt(str.substring(9, 10))) return false;

    soma = 0;
    for (i = 1; i <= 10; i++) soma = soma + parseInt(str.substring(i - 1, i)) * (12 - i);
    resto = (soma * 10) % 11;

    if ((resto == 10) || (resto == 11)) resto = 0;
    if (resto != parseInt(str.substring(10, 11))) return false;
    return true;
}
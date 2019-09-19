// var APIBaseUrl = "http://wb-admin-server.local";
var APIBaseUrl = "http://api.winebay.com.br";


var mainProject = angular.module("winebay-admin", [
    'ngSanitize', 'ui.router', 'ui.bootstrap', 'angularUtils.directives.dirPagination',
    'flow', 'ngCsv', 'ngMask']).config([
        '$compileProvider', function ($compileProvider) {
            $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|tel|javascript):/);
        }
    ])
    .config(['flowFactoryProvider', function (flowFactoryProvider) {
        flowFactoryProvider.defaults = {
            target: APIBaseUrl + '/image/upload',
            permanentErrors: [404, 500, 501],
            testChunks: false
        };
    }])
    .filter("unsafe", function ($sce) { return function (val) { return $sce.trustAsHtml(val); } })
    .config(function ($stateProvider, $urlRouterProvider) {
        $stateProvider.state('login', {
            url: "/login",
            templateUrl: "/views/login.html",
            controller: "LoginModalCtrl",
        }).state('dashboard', {
            url: "/dashboard",
            templateUrl: "/views/dashboard.html",
            controller: "DashboardCtrl"
        }).state('paises', {
            url: "/estrutura/paises",
            templateUrl: "/views/estrutura/paises.html",
            controller: "CountriesCtrl"
        }).state('regioes', {
            url: "/estrutura/regioes",
            templateUrl: "/views/estrutura/regioes.html",
            controller: "RegioesCtrl"
        }).state('uvas', {
            url: "/estrutura/uvas",
            templateUrl: "/views/estrutura/uvas.html",
            controller: "UvasCtrl"
        }).state('tipicidades', {
            url: "/estrutura/tipicidades",
            templateUrl: "/views/estrutura/tipicidades.html",
            controller: "TipicidadesCtrl"
        }).state('embalagens', {
            url: "/estrutura/embalagens",
            templateUrl: "/views/estrutura/embalagens.html",
            controller: "EmbalagensCtrl"
        }).state('winehouses', {
            url: "/winehouses",
            templateUrl: "/views/winehouses/winehouses.html",
            controller: "WinehousesCtrl"
        }).state('produtores', {
            url: "/estrutura/produtores",
            templateUrl: "/views/estrutura/produtores.html",
            controller: "ProdutorCtrl"
        }).state('importadores', {
            url: "/estrutura/importadores",
            templateUrl: "/views/estrutura/importadores.html",
            controller: "ImportadorCtrl"
        }).state('produtos', {
            url: "/produtos",
            templateUrl: "/views/produtos/lista.html",
            controller: "ProdutosCtrl"
        }).state('criarproduto', {
            url: "/produtos/criar",
            templateUrl: "/views/produtos/criar.html",
            controller: "CriarProdutoCtrl"
        }).state('alterarproduto', {
            url: "/produtos/alterar/{id_produto}",
            templateUrl: "/views/produtos/alterar.html",
            controller: "VerProdutoCtrl"
        }).state('importarexcel', {
            url: "/manutencao/importar",
            templateUrl: "/views/manutencao/importar.html",
            controller: "ImportarExcelCtrl"
        }).state('sitegeral', {
            url: "/site/geral",
            templateUrl: "/views/site/geral.html",
            controller: "SiteGeralCtrl"
        }).state('sitebanners', {
            url: "/site/banners",
            templateUrl: "/views/site/banners.html",
            controller: "SiteBannersCtrl"
        }).state('sitedestaque', {
            url: "/site/destaque",
            templateUrl: "/views/site/destaque.html",
            controller: "SiteDestaqueCtrl"
        }).state('pageInstitucional', {
            url: "/site/institucional",
            templateUrl: "/views/site/institucional.html",
            controller: "SiteInstitucionalCtrl"
        }).state('pageContato', {
            url: "/site/contato",
            templateUrl: "/views/site/contato.html",
            controller: "SiteContatoCtrl"
        }).state('integracaoDHL', {
            url: "/integracao/dhl",
            templateUrl: "/views/integracao/dhl.html",
            controller: "IntegracaoDHLCtrl"    
        }).state('integracaoCorreio', {
            url: "/integracao/correio",
            templateUrl: "/views/integracao/correio.html",
            controller: "IntegracaoCorreioCtrl"
        }).state('integracaoYesPay', {
            url: "/integracao/yespay",
            templateUrl: "/views/integracao/yespay.html",
            controller: "IntegracaoYesPayCtrl"
        }).state('integracaoMelhorEnvio', {
            url: "/integracao/melhorenvio",
            templateUrl: "/views/integracao/melhorenvio.html",
            controller: "IntegracaoMelhorEnvioCtrl"
        }).state('newsletter', {
            url: "/newsletter",
            templateUrl: "/views/newsletter/newsletter.html",
            controller: "NewsletterCtrl"
        }).state('imagens', {
            url: "/imagens",
            templateUrl: "/views/imagens/lista.html",
            controller: "ImagensCtrl"
        }).state('coupons', {
            url: "/cupons",
            templateUrl: "/views/cupons/lista.html",
            controller: "CouponsCtrl"
        }).state('frete', {
            url: "/frete",
            templateUrl: "/views/frete/frete.html",
            controller: "FreteCtrl"
        }).state('kits', {
            url: "/kits",
            templateUrl: "/views/kits/kits.html",
            controller: "KitsCtrl"
        });

        
        
        $urlRouterProvider.otherwise('dashboard');
    })
    .run(function ($rootScope, $state, User) {
        $rootScope.$on('$stateChangeStart', function (e, to) {
            if (!User.get() && (to.name != "login")) {
                $state.go("login");
                e.preventDefault();
            }
            $('body').layout('fix');
        });
    })
    .controller("MainController", function ($scope, $state, User) {
        $scope.UserData = false;
        $scope.APIBaseUrl = APIBaseUrl;
        //console.log($state)
        $scope.$watch(function () { return User.get(); }, function (val) {
            $scope.UserData = User.get();
            if (!$scope.UserData) {
                $scope.DoLogout();
            }
        });
        $scope.DoLogout = function () {
            User.set(false);
            $state.go("login");
        };
    });

function alert(string) {
    bootbox.alert(string);
}

function nl2br(str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
Number.prototype.toBRCurrency = function () {
    return this.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
String.prototype.toBRCurrency = function () {
    return (+this).toBRCurrency();
}
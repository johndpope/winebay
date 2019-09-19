mainProject.controller('SideMenuCtrl', function ($scope, $state, User) {
    $scope.CurrentState = "";
    $scope.$watch(function () { return $state.current }, function () {
        if ($state.current.url != "") $scope.CurrentState = $state.current;
    });
    $scope.MenuItems = [
        { text: "Dashboard", icon: "fas fa-tachometer-alt", href: "/dashboard" },
        {
            text: "Estrutura",
            icon: "fa fa-sitemap",
            hrefBase: "/estrutura",
            subitens: [
                { text: "Países", href: "/estrutura/paises" },
                { text: "Regiões", href: "/estrutura/regioes" },
                { text: "Uvas", href: "/estrutura/uvas" },
                { text: "Tipicidades", href: "/estrutura/tipicidades" },
                { text: "Produtores", href: "/estrutura/produtores" },
                { text: "Importadores", href: "/estrutura/importadores" },
                { text: "Embalagens", href: "/estrutura/embalagens" }
            ]
        },
        { text: "Winehouses", icon: "fas fa-warehouse", href: "/winehouses" },
        { text: "Produtos", icon: "fa fa-cubes", href: "/produtos" },
        {
            text: "Dados do Site",
            icon: "fas fa-globe",
            hrefBase: "/site",
            subitens: [
                { text: "Informações Gerais", href: "/site/geral" },
                { text: "Banners", href: "/site/banners" },
                { text: "Destaque", href: "/site/destaque" },
                { text: "Institucional", href: "/site/institucional" },
                { text: "Contato", href: "/site/contato" },
            ]
        },
        {
            text: "Integrações",
            icon: "fas fa-plug",
            hrefBase: "/integracao",
            subitens: [
                { text: "MelhorEnvio", href: "/integracao/melhorenvio" },
                { text: "Correio", href: "/integracao/correio" },
                { text: "DHL", href: "/integracao/dhl" },
                { text: "YesPay", href: "/integracao/yespay" }
            ]
        },
        { text: "Newsletter", icon: "fas fa-newspaper", href: "/newsletter" },
        {
            text: "Manutenção",
            icon: "fa fa-cog",
            hrefBase: "/manutencao",
            subitens: [
                { text: "Importar Excel", href: "/manutencao/importar" }
            ]
        },
        { text: "Imagens", icon: "far fa-images", href: "/imagens" },
        { text: "Cupons", icon: "fas fa-ticket-alt", href: "/cupons" },
        { text: "Frete", icon: "fas fa-truck", href: "/frete" },
        { text: "Kits", icon: "fas fa-truck", href: "/kits" },
    ];
});

mainProject.controller('SideMenuCtrl', function ($scope, $state, Winehouse) {
    $scope.CurrentState = "";
    $scope.$watch(function () { return $state.current }, function () {
        if ($state.current.url != "") $scope.CurrentState = $state.current;
    });
    $scope.MenuItems = [
        { text: "Dashboard", icon: "fas fa-tachometer-alt", href: "/dashboard" },
        { text: "Winehouse", icon: "fas fa-warehouse", href: "/winehouse" },
        { text: "Produtos", icon: "fa fa-cubes", href: "/produtos" },
        { text: "Ofertas", icon: "fa fa-dollar-sign", href: "/ofertas" },
        { text: "Kits", icon: "fa fa-box", href: "/kits" },
        { text: "Estoque", icon: "fa fa-list-ol", href: "/estoque" },
        { text: "Pedidos", icon: "fas fa-shopping-cart", href: "/pedidos" },
    ];
});

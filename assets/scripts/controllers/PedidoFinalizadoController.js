mainProject.controller('PedidoFinalizadoController', function($scope, $state, $stateParams) {
    if (!$stateParams.dadosCompra) $state.go("/");
    $scope.ListaCompras = $stateParams.dadosCompra;
    if ($scope.ListaCompras.length) {
        $scope.MetodoPagamento = $scope.ListaCompras[0].order.payment_mode;
    }
});

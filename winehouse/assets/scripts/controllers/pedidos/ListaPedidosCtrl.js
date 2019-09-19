mainProject.controller('ListaPedidosCtrl', function($scope, $http, $state, Winehouse) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarPedidos();
    });
    $scope.load = false;
    $scope.ListaPedidos = [];
    $scope.pedidosFiltro = {};

    $scope.ListarPedidos = function() {
        $scope.load = true;
        $http.get(APIBaseUrl+"/winehouse/getorders/id/" + Winehouse.get().id).then(function(res) {
            $scope.load = false;
            $scope.ListaPedidos = res.data;
        }, function(res) {
            $scope.load = false;
            $scope.ListaPedidos = [];
        });
    };
});

mainProject.controller('KitsCtrl', function($scope, $http, $state, Winehouse) {
    $scope.$on('$viewContentLoaded', function(){
        $scope.ListarKits();
    });
    $scope.load = false;
    $scope.ListaKits = [];

    $scope.ListarKits = function() {
        $scope.load = true;
        $http.get(APIBaseUrl+"/kit/winehouselist/id/" + Winehouse.get().id).then(function(res) {
            $scope.load = false;
            $scope.ListaKits = res.data;
        }, function(res) {
            $scope.load = false;
            $scope.ListaKits = [];
        });
    };

    $scope.ExcluiKit = function(kit) {
        bootbox.confirm("Confirma a remoção do kit '" + kit.name + "'?", function(ans) {
            if (ans) {
                $scope.load = true;
                $http.get(APIBaseUrl+"/kit/delete/id/"+kit.id).then(function(res) {
                    $scope.load = false;
                    bootbox.alert("O kit foi removido.");
                    $scope.ListarKits();
                }, function(res) {
                    $scope.load = false;
                    bootbox.alert("Não foi possível remover o kit. Tente novamente.")
                });
            }
        });
    };
});

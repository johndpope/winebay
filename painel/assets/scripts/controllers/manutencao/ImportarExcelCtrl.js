mainProject.controller("ImportarExcelCtrl", function ($scope, $http) {
    $scope.uploadFlag = false;
    $scope.importFlag = false;
    $scope.ListaImportar = [];
    $scope.ToggleUploadFlag = function (flag) {
        $scope.uploadFlag = flag;
    };
    $scope.ResetImportar = function() {
        $scope.ListaImportar=[];
        $scope.uploadFlag = false;
    }
    $scope.ProcessaRetornoUpload = function(message, flow) {
        $scope.ListaImportar = JSON.parse(message);
        angular.forEach($scope.ListaImportar, function(prod, p) {
            $scope.ListaImportar[p].name = (prod.name + ' ' + (prod.qualification || '')).trim();
            $scope.ListaImportar[p].id_image_thumb = "";
        });
        flow.files = [];
    };
    $scope.ImportarDados = function() {
        $scope.importFlag = true;
        $http.post(APIBaseUrl+"/maintenance/import", {list: $scope.ListaImportar}).then(function(res) {
            $scope.importFlag = false;
            $scope.ResetImportar();
            bootbox.alert("Dados importados com sucesso!");
        }, function(res) {
            $scope.importFlag = false;
            bootbox.alert("Não foi possível importar os dados. Tente novamente.");
        });
    };
});
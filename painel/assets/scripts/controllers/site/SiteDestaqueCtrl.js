mainProject.controller('SiteDestaqueCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarBanners();
    });
    $scope.APIBaseUrl = APIBaseUrl;
    $scope.logoLoad = false;
    $scope.ListaBanners = [];

    $scope.BannerFlow = false;
    $scope.BannerDestaque = {
        image: "",
        url: ""
    };

    $scope.BuscarBanners = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/get", { list: ["featured_banner"] }).then(
            function (res) {
                $scope.load = false;
                if (res.data.featured_banner) {
                    $scope.BannerDestaque = JSON.parse(res.data.featured_banner);
                }
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível buscar o destaque. Tente novamente.");
            }
        );
    };

    $scope.ToggleUploadFlag = function (flag) {
        $scope.logoLoad = flag;
    };

    $scope.DefineImagem = function ($message, $flow) {
        $scope.BannerDestaque.image = $message;
        $scope.BannerFlow = $flow;
    };

    $scope.CancelaImagem = function ($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.SalvarAlteracoes = function () {
        if ($scope.BannerDestaque.url == "") return bootbox.alert("Digite a URL do destaque.");
        if ($scope.BannerDestaque.image == "") return bootbox.alert("Selecione a imagem do destaque.");
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/save", { Dados: { featured_banner: angular.toJson($scope.BannerDestaque) } }).then(
            function (res) {
                $scope.load = false;
            },
            function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível salvar os dados. Tente novamente.");
            }
        );
    }
});

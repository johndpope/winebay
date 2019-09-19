mainProject.controller('SiteBannersCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarBanners();
    });
    $scope.APIBaseUrl = APIBaseUrl;
    $scope.logoLoad = false;
    $scope.ListaBanners = [];

    $scope.BannerFlow = false;
    $scope.NovoBanner = {
        image: "",
        url: ""
    };

    $scope.BuscarBanners = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/get", { list: ["home_banners"] }).then(
            function (res) {
                $scope.load = false;
                if (res.data.home_banners) {
                    $scope.ListaBanners = JSON.parse(res.data.home_banners);
                }
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível buscar os banners. Tente novamente.");
            }
        );
    };

    $scope.AdicionaBanner = function () {
        if ($scope.NovoBanner.url == "") return bootbox.alert("Digite a URL do banner.");
        if ($scope.NovoBanner.image == "") return bootbox.alert("Selecione a imagem do banner.");
        $scope.ListaBanners.push(angular.copy($scope.NovoBanner));
        $scope.NovoBanner = {
            image: "",
            url: ""
        };
        $scope.BannerFlow.cancel();
    };

    $scope.RemoveBanner = function (banner) {
        $scope.ListaBanners.splice($scope.ListaBanners.indexOf(banner), 1);
    }

    $scope.ToggleUploadFlag = function (flag) {
        $scope.logoLoad = flag;
    };

    $scope.DefineImagem = function ($message, $flow) {
        $scope.NovoBanner.image = $message;
        $scope.BannerFlow = $flow;
    };

    $scope.CancelaImagem = function ($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.SalvarAlteracoes = function () {
        $scope.load = true;
        listaBanners = angular.toJson($scope.ListaBanners);
        $http.post(APIBaseUrl + "/website/save", { Dados: { home_banners: listaBanners } }).then(
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

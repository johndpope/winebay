mainProject.controller('SiteGeralCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDadosSite();
    });
    $scope.APIBaseUrl = APIBaseUrl;
    $scope.logoLoad = false;
    $scope.load = false;
    $scope.DadosSite = {
        main_logo: "",
        footer_logo: "",
        footer_text: "",
        facebook: "",
        instagram: "",
        twitter: ""
    }
    $scope.MainLogoFlow = false;
    $scope.FooterLogoFlow = false;

    $scope.BuscarDadosSite = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/get", { list: Object.keys($scope.DadosSite) }).then(
            function (res) {
                $scope.load = false;
                $scope.DadosSite = res.data;
                if ($scope.DadosSite.footer_text) $scope.DadosSite.footer_text = $scope.DadosSite.footer_text.replace(/[<]br[^>]*[>]/gi, "");
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível buscar os dados do site. Tente novamente.");
            }
        );
    };

    $scope.ToggleUploadFlag = function (flag) {
        $scope.logoLoad = flag;
    };

    $scope.DefineLogoPrincipal = function ($message, $flow) {
        $scope.DadosSite.main_logo = $message;
        $scope.MainLogoFlow = $flow;
    };

    $scope.DefineLogoFooter = function ($message, $flow) {
        $scope.DadosSite.footer_logo = $message;
        $scope.FooterLogoFlow = $flow;
    };

    $scope.CancelaImagem = function ($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.SalvarAlteracoes = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/save", { Dados: $scope.DadosSite }).then(
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

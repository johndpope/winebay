mainProject.controller('SiteContatoCtrl', function ($scope, $http, $state) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarContatos();
    });
    $scope.APIBaseUrl = APIBaseUrl;
    $scope.logoLoad = false;
    $scope.ListaContatos = [];

    $scope.ContatoFlow = false;
    $scope.NovoContato = {
        title: "",
        text: ""
    };

    $scope.BuscarContatos = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/website/get", { list: ["contact_list"] }).then(
            function (res) {
                $scope.load = false;
                if (res.data.contact_list) {
                    $scope.ListaContatos = JSON.parse(res.data.contact_list);
                }
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Não foi possível buscar os contatos. Tente novamente.");
            }
        );
    };

    $scope.AdicionaContato = function () {
        if ($scope.NovoContato.title == "") return bootbox.alert("Digite o título do contato.");
        if ($scope.NovoContato.text == "") return bootbox.alert("Digite o texto do contato.");
        $scope.ListaContatos.push(angular.copy($scope.NovoContato));
        $scope.NovoContato = {
            title: "",
            text: ""
        };
    };

    $scope.RemoveContato = function (contato) {
        $scope.ListaContatos.splice($scope.ListaContatos.indexOf(contato), 1);
    }

    $scope.SalvarAlteracoes = function () {
        $scope.load = true;
        var listaContatos = angular.toJson($scope.ListaContatos);
        $http.post(APIBaseUrl + "/website/save", { Dados: { contact_list: listaContatos } }).then(
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

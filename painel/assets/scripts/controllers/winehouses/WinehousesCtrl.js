mainProject.controller('WinehousesCtrl', function ($scope, $http, $state, User) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.ListarWinehouses();
    });
    $scope.ListaWinehouses = [];
    $scope.winehousesLoad = false;
    $scope.novoWinehouseLoad = false;
    $scope.alterarWinehouseLoad = false;
    $scope.NovoWinehouse = {
        name: "",
        description: "",
        contact: "",
        address: "",
        city: "",
        region: "",
        state: "",
        cep: "",
        phone: "",
        email: "",
        cnpj: "",
        tax_type: "",
        fee_percentage: "",
        id_image: "",
        can_add_products: true,
    };
    $scope.NovoWinehouseFlow = false;
    $scope.WinehouseAlterarFlow = false;
    $scope.WinehouseAlterar = false;
    $scope.dataTable = false;
    $scope.uploadFlag = false;
    $scope.ToggleUploadFlag = function (flag) {
        $scope.uploadFlag = flag;
    };

    $scope.DefineImagemNovoWinehouse = function ($message, $flow) {
        $scope.NovoWinehouse.id_image = $message;
        $scope.NovoWinehouseFlow = $flow;
    };

    $scope.DefineImagemWinehouseAlterar = function ($message, $flow) {
        $scope.WinehouseAlterar.id_image = $message;
        $scope.WinehouseAlterarFlow = $flow;
    };

    $scope.CancelaImagem = function ($flow, obj) {
        $flow.cancel();
        obj = "";
    };

    $scope.CriaNovoWinehouse = function () {
        if ($scope.NovoWinehouse.name.trim() == "") return bootbox.alert("Digite o nome fantasia do winehouse.");
        if ($scope.NovoWinehouse.business_name.trim() == "") return bootbox.alert("Digite a razão social do winehouse.");
        if ($scope.NovoWinehouse.contact.trim() == "") return bootbox.alert("Digite o nome do responsável.");
        if ($scope.NovoWinehouse.address.trim() == "") return bootbox.alert("Digite o endereço do winehouse.");
        if ($scope.NovoWinehouse.cep.trim() == "") return bootbox.alert("Digite o CEP do winehouse.");
        if ($scope.NovoWinehouse.city.trim() == "") return bootbox.alert("Digite a cidade do winehouse.");
        if ($scope.NovoWinehouse.region.trim() == "") return bootbox.alert("Digite o bairro do winehouse.");
        if ($scope.NovoWinehouse.state.trim() == "") return bootbox.alert("Digite o estado do winehouse.");
        if ($scope.NovoWinehouse.phone.trim() == "") return bootbox.alert("Digite o telefone do winehouse.");
        if ($scope.NovoWinehouse.cnpj.trim() == "") return bootbox.alert("Digite o CNPJ do winehouse.");
        if ($scope.NovoWinehouse.tax_type == "") return bootbox.alert("Selecione o regime tributário.");
        if ($scope.NovoWinehouse.fee_percentage.toString().trim() == "") return bootbox.alert("Digite a porcentagem de comissão.");
        if (isNaN($scope.NovoWinehouse.fee_percentage) || (+$scope.NovoWinehouse.fee_percentage < 0) || (+$scope.NovoWinehouse.fee_percentage > 100)) return bootbox.alert("O valor de comissão é inválido.");
        $scope.novoWinehouseLoad = true;
        $http.post(APIBaseUrl + "/winehouse/create", $scope.NovoWinehouse).then(function (res) {
            $scope.novoWinehouseLoad = false;
            $scope.NovoWinehouse = {
                name: "",
                description: "",
                contact: "",
                address: "",
                city: "",
                region: "",
                state: "",
                cep: "",
                phone: "",
                email: "",
                cnpj: "",
                tax_type: "",
                fee_percentage: "",
                id_image: "",
                can_add_products: true,
            };
            if ($scope.NovoWinehouseFlow) $scope.NovoWinehouseFlow.files = [];
            $scope.ListarWinehouses();
        }, function (res) {
            $scope.novoWinehouseLoad = false;
            bootbox.alert("Não foi possível cadastrar o winehouse. Tente novamente.");
        });
    };

    $scope.ListarWinehouses = function () {
        $scope.winehousesLoad = true;
        $http.get(APIBaseUrl + "/winehouse/list").then(function (res) {
            $scope.winehousesLoad = false;
            $scope.ListaWinehouses = res.data;
        }, function (res) {
            $scope.winehousesLoad = false;
            $scope.ListaWinehouses = [];
        });
    };

    $scope.ExcluiWinehouse = function (winehouse) {
        bootbox.confirm("Deseja realmente excluir este winehouse?<br/><strong>Esta ação não poderá ser desfeita!</strong>", function (ans) {
            if (ans) {
                $scope.winehousesLoad = true;
                $http.get(APIBaseUrl + "/winehouse/delete/id/" + winehouse.id).then(function (res) {
                    $scope.winehousesLoad = false;
                    $scope.ListarWinehouses();
                }, function (res) {
                    $scope.winehousesLoad = false;
                    bootbox.alert("Não foi possível excluír o winehouse. Tente novamente.");
                });
            }
        });
    };

    $scope.AlteraSenhaWinehouse = function (winehouse) {
        bootbox.prompt("Digite a nova senha para a winehouse " + winehouse.name + ":", function (ans) {
            if (ans) {
                if (ans.length < 5) {
                    bootbox.alert("A nova senha deve ter pelo menos 5 caracteres.");
                    return false;
                } else {
                    $scope.winehousesLoad = true;
                    $http.post(APIBaseUrl + "/winehouse/setpass", {
                        id: winehouse.id,
                        password: ans
                    }).then(function (res) {
                        $scope.winehousesLoad = false;
                        bootbox.alert("A senha da winehouse foi alterada.");
                    }, function (res) {
                        $scope.winehousesLoad = false;
                        bootbox.alert("Não foi possível alterar a senha da winehouse. Tente novamente.");
                    });
                }
            }
        });
    };

    $scope.AlteraWinehouse = function (winehouse) {
        $scope.WinehouseAlterar = angular.copy(winehouse);
    };

    $scope.SalvarWinehouse = function () {
        if (!$scope.WinehouseAlterar) return bootbox.alert("Não há um winehouse sendo alterado.");
        if ($scope.WinehouseAlterar.name.trim() == "") return bootbox.alert("Digite o nome fantasia do winehouse.");
        if ($scope.WinehouseAlterar.business_name.trim() == "") return bootbox.alert("Digite a razão social do winehouse.");
        if ($scope.WinehouseAlterar.contact.trim() == "") return bootbox.alert("Digite o nome do responsável.");
        if ($scope.WinehouseAlterar.address.trim() == "") return bootbox.alert("Digite o endereço do winehouse.");
        if ($scope.WinehouseAlterar.cep.trim() == "") return bootbox.alert("Digite o CEP do winehouse.");
        if ($scope.WinehouseAlterar.city.trim() == "") return bootbox.alert("Digite a cidade do winehouse.");
        if ($scope.WinehouseAlterar.region.trim() == "") return bootbox.alert("Digite o bairro do winehouse.");
        if ($scope.WinehouseAlterar.state.trim() == "") return bootbox.alert("Digite o estado do winehouse.");
        if ($scope.WinehouseAlterar.phone.trim() == "") return bootbox.alert("Digite o telefone do winehouse.");
        if ($scope.WinehouseAlterar.cnpj.trim() == "") return bootbox.alert("Digite o CNPJ do winehouse.");
        if ($scope.WinehouseAlterar.tax_type == "") return bootbox.alert("Selecione o regime tributário.");
        if ($scope.WinehouseAlterar.fee_percentage.toString().trim() == "") return bootbox.alert("Digite a porcentagem de comissão.");
        if (isNaN($scope.WinehouseAlterar.fee_percentage) || (+$scope.WinehouseAlterar.fee_percentage < 0) || (+$scope.WinehouseAlterar.fee_percentage > 100)) return bootbox.alert("O valor de comissão é inválido.");

        $scope.alterarWinehouseLoad = true;
        $http.post(APIBaseUrl + "/winehouse/save", $scope.WinehouseAlterar).then(function (res) {
            $scope.alterarWinehouseLoad = false;
            if ($scope.WinehouseAlterarFlow) $scope.WinehouseAlterarFlow.files = [];
            $scope.WinehouseAlterar = false;
            $scope.ListarWinehouses();
        }, function (res) {
            $scope.alterarWinehouseLoad = false;
            bootbox.alert("Não foi possível alterar o winehouse. Tente novamente.");
        });
    };
});

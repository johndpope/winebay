mainProject.controller('CadastroModalCtrl', function ($scope, $uibModal, Winehouse) {
    var modalInstance = $uibModal.open({
        animation: true,
        ariaLabelledBy: 'modal-title',
        ariaDescribedBy: 'modal-body',
        templateUrl: 'cadastro-modal.html',
        backdrop: 'static',
        keyboard: false,
        size: "custom-lg",
        appendTo: angular.element('.modal-cadastro'),
        controller: 'CadastroCtrl',
        resolve: {
            modalInstance: function () {
                return modalInstance
            }
        }
    });
}).controller('CadastroCtrl', function ($scope, $uibModal, Winehouse, $state, $http, $uibModalStack) {
    $scope.load = false;
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
        id_image: "",
        can_add_products: true,
        self_register: true,
        fee_percentage: 15
    };


    $scope.DoRegister = function () {
        if ($scope.NovoWinehouse.name.trim() == "") return bootbox.alert("Digite o nome fantasia do winehouse.");
        if ($scope.NovoWinehouse.business_name.trim() == "") return bootbox.alert("Digite a razão social do winehouse.");if ($scope.NovoWinehouse.business_name.trim() == "") return bootbox.alert("Digite a razão social do winehouse.");
        if ($scope.NovoWinehouse.email.trim() == "") return bootbox.alert("Digite o email do winehouse.");
        if ($scope.NovoWinehouse.contact.trim() == "") return bootbox.alert("Digite o nome do responsável.");
        if ($scope.NovoWinehouse.address.trim() == "") return bootbox.alert("Digite o endereço do winehouse.");
        if ($scope.NovoWinehouse.cep.trim() == "") return bootbox.alert("Digite o CEP do winehouse.");
        if ($scope.NovoWinehouse.city.trim() == "") return bootbox.alert("Digite a cidade do winehouse.");
        if ($scope.NovoWinehouse.region.trim() == "") return bootbox.alert("Digite o bairro do winehouse.");
        if ($scope.NovoWinehouse.state.trim() == "") return bootbox.alert("Digite o estado do winehouse.");
        if ($scope.NovoWinehouse.phone.trim() == "") return bootbox.alert("Digite o telefone do winehouse.");
        if ($scope.NovoWinehouse.cnpj.trim() == "") return bootbox.alert("Digite o CNPJ do winehouse.");
        if ($scope.NovoWinehouse.tax_type == "") return bootbox.alert("Selecione o regime tributário.");
        $scope.load = true;
        $http.post(APIBaseUrl + "/winehouse/create", $scope.NovoWinehouse).then(function (res) {
            $scope.load = false;
            $scope.NovoWinehouse = {
                name: "",
                business_name: "",
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
                id_image: "",
                can_add_products: true,
                self_register: true,
                fee_percentage: 15
            };
            bootbox.alert("Cadastro efetuado com sucesso! Os dados de acesso foram enviados ao e-mail inserido.");
            $uibModalStack.dismissAll();
            $state.go("login");
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível cadastrar o winehouse. Tente novamente.");
        });
    };
});

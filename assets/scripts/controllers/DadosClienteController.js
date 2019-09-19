mainProject.controller('DadosClienteController', function ($scope, User, $http) {
    $scope.$on("$viewContentLoaded", function () {
        $scope.DadosCadastro = angular.copy(User.get());
        $scope.DadosCadastro.new_password = "";
        $scope.DadosCadastro.newsletter = Boolean($scope.DadosCadastro.newsletter);
        if (!$scope.DadosCadastro.addresses.length) $scope.DadosCadastro.addresses.push($scope.NovoEndereco);
    });

    $scope.NovoEndereco = {
        name: "",
        address: "",
        cep: "",
        region: "",
        city: "",
        state: "",
        information: "",
        dest: ""
    }

    $scope.RemoveEndereco = function (address) {
        $scope.DadosCadastro.addresses.splice($scope.DadosCadastro.addresses.indexOf(address), 1);
    }

    $scope.AdicionaEndereco = function () {
        $scope.DadosCadastro.addresses.push(angular.copy($scope.NovoEndereco));
    }

    $scope.SalvarDados = function () {
        if (!$scope.DadosCadastro.name.trim().length) return bootbox.alert("Digite seu nome completo!");
        if ($scope.DadosCadastro.name.trim().split(" ").length < 2) return bootbox.alert("Digite seu nome completo!");
        if (!$scope.DadosCadastro.cpf.trim().length) return bootbox.alert("Digite seu CPF!");
        if (!$scope.DadosCadastro.email.trim().length) return bootbox.alert("Digite seu email!");
        if (!$scope.DadosCadastro.email.indexOf("@") == -1) return bootbox.alert("Digite um email válido!");
        if ($scope.DadosCadastro.new_password.length) {
            if (!$scope.DadosCadastro.new_password_conf.length) return bootbox.alert("Digite a confirmação de senha!");
            if ($scope.DadosCadastro.new_password != $scope.DadosCadastro.new_password_conf) return bootbox.alert("As senhas digitadas não conferem!");
        }
        if (!$scope.DadosCadastro.birth_date.length) return bootbox.alert("Digite sua data de nascimento!");
        if (!$scope.DadosCadastro.phone.length) return bootbox.alert("Digite seu telefone!");
        var hasError = false;
        angular.forEach($scope.DadosCadastro.addresses, function (address) {
            if (!address.name.trim().length) { hasError = true; return bootbox.alert("Todos os endereços devem ter um nome!"); }
            if (!address.address.trim().length) { hasError = true; return bootbox.alert("Todos os endereços devem ter um endereço!"); }
            if (!address.cep.trim().length) { hasError = true; return bootbox.alert("Todos os endereços devem ter um CEP!"); }
            if (!address.region.trim().length) { hasError = true; return bootbox.alert("Todos os endereços devem ter um bairro!"); }
            if (!address.city.trim().length) { hasError = true; return bootbox.alert("Todos os endereços devem ter uma cidade!"); }
            if (!address.state.trim().length) { hasError = true; return bootbox.alert("Todos os endereços devem ter um UF!"); }
        });

        if (hasError) return false;
        $scope.load = true;
        var userData = angular.copy($scope.DadosCadastro);
        userData.addresses = JSON.stringify(userData.addresses);
        $http.post(APIBaseUrl + "/customer/update", { Dados: userData }).then(
            function (res) {
                $scope.load = false;
                bootbox.alert("Cadastro atualizado com sucesso!");
                User.set($scope.DadosCadastro);
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Erro ao atualizar o cadastro. Tente novamente.");
            }
        );
    };
});

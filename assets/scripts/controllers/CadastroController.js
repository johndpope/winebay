mainProject.controller('CadastroController', function ($scope, User, $http, $state, $stateParams) {
    $scope.UserLogged = false;
    $scope.load = false;
    $scope.DadosCadastro = {
        NomeCompleto: "",
        CPF: "",
        EMail: "",
        Senha: "",
        ConfSenha: "",
        Sexo: "F",
        DataNascimento: "",
        Telefone: "",
        CienteLei: false,
        Newsletter: true
    };

    $scope.$on("$viewContentLoaded", function () {
        $scope.UserLogged = User.get();
        $scope.Destination = $stateParams.Destination;
    });

    $scope.CriarCadastro = function () {
        if (!$scope.DadosCadastro.NomeCompleto.trim().length) return bootbox.alert("Digite seu nome completo!");
        if ($scope.DadosCadastro.NomeCompleto.trim().split(" ").length < 2) return bootbox.alert("Digite seu nome completo!");
        // if (!$scope.DadosCadastro.CPF.trim().length) return bootbox.alert("Digite seu CPF!");
        if ($scope.DadosCadastro.CPF.trim().length && !validaCPF($scope.DadosCadastro.CPF.trim())) return bootbox.alert("O CPF digitado é inválido!");
        if (!$scope.DadosCadastro.EMail.trim().length) return bootbox.alert("Digite seu email!");
        if (!$scope.DadosCadastro.EMail.indexOf("@") == -1) return bootbox.alert("Digite um email válido!");
        if (!$scope.DadosCadastro.Senha.length) return bootbox.alert("Digite sua senha!");
        if (!$scope.DadosCadastro.ConfSenha.length) return bootbox.alert("Digite a confirmação de senha!");
        if ($scope.DadosCadastro.Senha != $scope.DadosCadastro.ConfSenha) return bootbox.alert("As senhas digitadas não conferem!");
        // if (!$scope.DadosCadastro.DataNascimento.length) return bootbox.alert("Digite sua data de nascimento!");
        // if (!$scope.DadosCadastro.Telefone.length) return bootbox.alert("Digite seu telefone!");
        if (!$scope.DadosCadastro.CienteLei) return bootbox.alert("Você deve marcar a ciência sobre a lei 8.069/90!");

        $scope.load = true;
        $http.post(APIBaseUrl + "/customer/register", { Dados: $scope.DadosCadastro }).then(
            function (res) {
                $scope.load = false;
                if (res == "user_exists") {
                    bootbox.alert("Já existe um usuário com estes dados!");
                } else {
                    bootbox.alert("Cadastro efetuado com sucesso!");
                    var userData = res.data;
                    userData.addresses = [];
                    User.set(userData);
                    if ($stateParams.Destination) {
                        $state.go($stateParams.Destination);
                    } else {
                        $state.go("/");
                    }
                }
            }, function (res) {
                $scope.load = false;
                bootbox.alert("Erro ao efetuar o cadastro. Tente novamente.");
            }
        );
    };
});

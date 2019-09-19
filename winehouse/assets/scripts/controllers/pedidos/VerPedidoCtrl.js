mainProject.controller('VerPedidoCtrl', function ($scope, $sce, $http, $state, $stateParams, Winehouse, $filter) {
    $scope.$on('$viewContentLoaded', function () {
        $scope.BuscarDadosPedido();
    });
    $scope.load = false;
    $scope.DadosPedido = false;
    $scope.DadosWinehouse = Winehouse.get();
    $scope.MotivoCancColeta = "";
    $scope.DadosEtiqueta = false;
    $scope.DadosColeta = {
        type: "now", //Tipo de coleta, permite liberar na hora, ou agendar pra uma data específica
        nowDate: moment().format("DD/MM/YYYY"),
        date: moment().add(1, 'days').format("DD/MM/YYYY"),
        minHour: "08:00",
        maxHour: "18:00",
        location: "",
        contact: ""
    };

    $scope.ExibeDadosEtiqueta = function (base64) {
        if (base64) $scope.DadosEtiqueta = $sce.trustAsResourceUrl("data:application/pdf;base64," + base64);
        else $scope.DadosEtiqueta = false;
    };

    $scope.BuscarDadosPedido = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/winehouse/getorder/id/" + $stateParams.Id).then(function (res) {
            $scope.load = false;
            $scope.DadosPedido = res.data;
            $scope.DadosPedido.Embalagens = {};
            if ($scope.DadosPedido.pickup_info) {
                $scope.DadosColeta.nowDate = $scope.DadosPedido.pickup_info.pickup.date;
                $scope.DadosColeta.minHour = $scope.DadosPedido.pickup_info.pickup.min_hour;
                $scope.DadosColeta.maxHour = $scope.DadosPedido.pickup_info.pickup.max_hour;
                $scope.DadosColeta.location = $scope.DadosPedido.pickup_info.pickup.location;
                $scope.DadosColeta.contact = $scope.DadosPedido.pickup_info.pickup.contact;
            }
            var restante = $scope.DadosPedido.total_quantity;
            while (restante > 0) {
                var skip = false;
                angular.forEach($filter('orderBy')($scope.DadosPedido.packages, '-size'), function (embalagem) {
                    if (skip) return;
                    if ((restante >= embalagem.size) || (restante > embalagem.size / 2)) {
                        if ($scope.DadosPedido.Embalagens[embalagem.id] == undefined) {
                            $scope.DadosPedido.Embalagens[embalagem.id] = {
                                name: embalagem.name,
                                size: embalagem.size,
                                width: embalagem.width,
                                height: embalagem.height,
                                depth: embalagem.depth,
                                weight: embalagem.weight,
                                count: 0
                            }
                        }
                        $scope.DadosPedido.Embalagens[embalagem.id].count++;
                        restante -= embalagem.size;
                        skip = true;
                    }
                });
            }
            $scope.DadosPedido.Embalagens = Object.values($scope.DadosPedido.Embalagens);
        }, function (res) {
            $scope.load = false;
            $scope.DadosPedido = false;
        });
    };

    $scope.CopiaNumeroBoleto = function () {
        bootbox.alert("O número do boleto foi copiado para a área de transferência.");
    };

    $scope.SolicitarColeta = function () {
        var dadosColeta = {
            pickup: $scope.DadosColeta,
            order: $scope.DadosPedido,
            winehouse: $scope.DadosWinehouse
        };

        if ((dadosColeta.pickup.type == "schedule") && (!moment(dadosColeta.pickup.date, "DD/MM/YYYY").isValid())) {
            return bootbox.alert("A data da coleta é inválida.");
        }

        if (dadosColeta.pickup.type == "now") {
            dadosColeta.pickup.date = dadosColeta.pickup.nowDate;
        }

        if (!dadosColeta.pickup.location.trim().length) return bootbox.alert("Digite o local de retirada.");
        if (dadosColeta.pickup.location.trim().length > 30) return bootbox.alert("O local de retirada não pode ter mais de 30 caracteres.");
        if (!dadosColeta.pickup.contact.trim().length) return bootbox.alert("Digite o responsável pela entrega do pacote.");

        $scope.load = true;
        $http.post(APIBaseUrl + "/winehouse/requestpickup", { pickupData: dadosColeta }).then(function (res) {
            $scope.load = false;
            $scope.BuscarDadosPedido();
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível solicitar a coleta. Tenta novamente");
        });
    };

    $scope.CancelarColeta = function () {
        $scope.load = true;
        $http.post(APIBaseUrl + "/winehouse/cancelpickup", {
            pickupData: $scope.DadosPedido.pickup_info,
            reason: $scope.MotivoCancColeta,
            order: $scope.DadosPedido.id
        }).then(function (res) {
            $scope.load = false;
            $scope.MotivoCancColeta = "";
            $scope.BuscarDadosPedido();
        }, function (res) {
            $scope.load = false;
            bootbox.alert("Não foi possível solicitar a coleta. Tenta novamente");
        });
    };
});

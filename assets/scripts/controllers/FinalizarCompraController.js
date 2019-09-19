mainProject.controller('FinalizarCompraController', function ($scope, Products, $state, $filter, $http, User, $timeout) {
    var shippingTimeout = 0;

    $scope.$on("$viewContentLoaded", function () {
        $scope.ProductBox = Products.getBox();
        if (!$scope.ProductBox.length) return $state.go("/");
        if ($scope.DadosUsuario.addresses.length) {
            $scope.SetAddress($scope.DadosUsuario.addresses[0]);
        }
        $scope.BuscarEmbalagens(function() {
        $scope.$watch(function () { return JSON.stringify(Products.getBox()) }, function (val) {
            $scope.ProductBox = JSON.parse(val);
            $scope.GerarCaixas();
        });
        });
    });
    $scope.CaixasTotal = [];
    $scope.$watch(function () {
        return JSON.stringify($scope.CaixasTotal);
    }, function (val) {
        $timeout.cancel(shippingTimeout);
        if ($scope.freteLoad == 0) {
            shippingTimeout = $timeout(function () {
                if ($scope.freteLoad == 0) $scope.CalculaFreteCaixas();
            }, 500);
        }
    });
    $scope.freteLoad = 0;
    $scope.ListaEmbalagens = [];
    $scope.CupomDesconto = "";
    $scope.InformacoesCupom = false;
    $scope.DadosUsuario = User.get();
    $scope.NovaCompra = {
        address: "",
        payment_mode: "credit_card",
        id_customer: $scope.DadosUsuario.id,
        card_payment: {
            number: "",
            holder_name: "",
            holder_document: "",
            expiration: "",
            security_code: "",
            installments: "1"
        }
    };

    $scope.BuscarEmbalagens = function (callback) {
        $http.get(APIBaseUrl + "/package/list").then(function (res) {
            $scope.load = false;
            $scope.ListaEmbalagens = res.data;
            callback();
        }, function (res) {
            $scope.load = false;
            $scope.ListaEmbalagens = [];
        });
    }

    $scope.ValidarCupom = function () {
        if ($scope.CupomDesconto.trim().length) {
            $scope.couponLoad = true;
            $http.get(APIBaseUrl + "/coupon/validate/code/" + $scope.CupomDesconto).then(function (res) {
                $scope.couponLoad = false;
                $scope.InformacoesCupom = res.data;
            }, function (data) {
                $scope.couponLoad = false;
                $scope.InformacoesCupom = false;
            });
        }
    };

    $scope.SetAddress = function (address) {
        $scope.NovaCompra.address = address;
    };
    $scope.CalculaBoxTotal = function () {
        var total = 0;
        angular.forEach($scope.ProductBox, function (pr) {
            total += pr.price * pr.quant;
        });
        if ($scope.InformacoesCupom.value) {
            total -= $scope.InformacoesCupom.value;
        }
        return total;
    };

    $scope.CalculaPedidoTotal = function () {
        return $scope.CalculaBoxTotal() + $scope.CalculaValorFrete();
    }

    $scope.CalculaValorFrete = function () {
        var total = 0;
        angular.forEach($scope.CaixasTotal, function (box) {
            total += +box.total_frete;
        });
        return total;
    }

    $scope.CalculaFreteCaixas = function () {
        angular.forEach($scope.CaixasTotal, function (box) {
            $scope.freteLoad++;
            if (box.shipment == "expresso") {
                if ($scope.NovaCompra.address != "") {
                    box.embalagens = $scope.MontarEmbalagens(box);
                    console.log(box);
                    if (box.embalagens.length) {
                        $http.post(APIBaseUrl + "/shipment/calculatebox", {
                            address: $scope.NovaCompra.address,
                            box: box
                        }).then(function (res) {
                            box.total_frete = res.data.QtdShp.ShippingCharge;
                            box.desconto_frete = +res.data.DescontoFrete;
                            box.porcentagem_plataforma = +res.data.PorcentagemPlataforma;
                            $timeout(function () { $scope.freteLoad--; }, 500);
                        }, function (res) {
                            box.total_frete = 0;
                            bootbox.alert("Ocorreu um erro ao calcular o frete para a Winehouse " + box.name);
                            $timeout(function () { $scope.freteLoad--; }, 500);
                        });
                    }
                } else {
                    box.total_frete = 0;
                    $scope.freteLoad--;
                }
            } else {
                box.total_frete = 0;
                $scope.freteLoad--;
            }
            // console.log($scope.freteLoad);
        });
        // $scope.orderLoad = false;
    }

    $scope.MontarEmbalagens = function (box) {
        box.restante = 0;
        var embalagens = [];
        angular.forEach(box.products, function (product) {
            box.restante += product.quant;
        });
        box.total_count = box.restante;
        if ($scope.ListaEmbalagens.length) {
            while (box.restante > 0) {
                var skip = false;
                angular.forEach($filter('orderBy')($scope.ListaEmbalagens, '-size'), function (embalagem) {
                    if (skip) return;
                    if ((box.restante >= embalagem.size) || (box.restante > embalagem.size / 2)) {
                        if (embalagens[embalagem.id] == undefined) {
                            embalagens[embalagem.id] = {
                                name: embalagem.name,
                                size: embalagem.size,
                                width: embalagem.width,
                                height: embalagem.height,
                                depth: embalagem.depth,
                                weight: embalagem.weight,
                                count: 0
                            }
                        }
                        embalagens[embalagem.id].count++;
                        box.restante -= embalagem.size;
                        skip = true;
                    }
                });
            }
        }
        return embalagens;
    }

    $scope.GerarCaixas = function () {
        var caixas = [];
        angular.forEach($scope.ProductBox, function (product) {
            if (caixas[product.winehouse.value] == undefined) {
                caixas[product.winehouse.value] = {
                    winehouse: product.winehouse.value,
                    name: product.winehouse.name,
                    products: [],
                    total: 0,
                    shipment: "expresso",
                    total_frete: 0,
                    embalagens: []
                }
                var caixaAnterior = $filter("filter")($scope.CaixasTotal, { winehouse: product.winehouse.value });
                if (caixaAnterior.length) {
                    caixas[product.winehouse.value].shipment = caixaAnterior[0].shipment;
                }
            }
            caixas[product.winehouse.value].products.push(product);
            caixas[product.winehouse.value].total += product.price * product.quant;
        });
        $scope.CaixasTotal = Object.values(caixas);
    }

    $scope.ContagemBox = function () {
        return Products.countBox();
    }

    $scope.ProductBoxIncrease = function (pr) {
        pr.quant = pr.quant + 1;
        Products.setBox($scope.ProductBox);
    }

    $scope.ProductBoxDecrease = function (pr) {
        if (pr.quant <= 1) {
            $scope.ProductBox.splice($scope.ProductBox.indexOf(pr), 1);
        } else {
            pr.quant = pr.quant - 1;
        }
        Products.setBox($scope.ProductBox);
    }

    $scope.FinalizarPedido = function () {
        if (!$scope.ProductBox.length) return bootbox.alert("Não há produtos na sua caixa!");
        if ($scope.DadosUsuario.addresses && !$scope.DadosUsuario.addresses.length) return bootbox.alert("Você deve cadastrar um endereço.");
        if ($scope.NovaCompra.address == "") return bootbox.alert("Selecione um endereço para entrega.");

        if ($scope.NovaCompra.payment_mode == "credit_card") {
            if ($scope.NovaCompra.card_payment.number.length != 19) return bootbox.alert("Por favor, digite o número do cartão.");
            if ($scope.NovaCompra.card_payment.holder_name == "") return bootbox.alert("Por favor, digite o nome do titular do cartão.");
            if ($scope.NovaCompra.card_payment.holder_document == "") return bootbox.alert("Por favor, digite o CPF do titular do cartão.");
            if ($scope.NovaCompra.card_payment.expiration.length != 5) return bootbox.alert("Por favor, digite a data de validade do cartão.");
            if ($scope.NovaCompra.card_payment.security_code.length != 3) return bootbox.alert("Por favor, digite o código de segurança do cartão.");
        }
        $scope.NovaCompra.boxes = angular.copy($scope.CaixasTotal);
        $scope.NovaCompra.total_amount = $scope.CalculaBoxTotal();
        if ($scope.InformacoesCupom && $scope.InformacoesCupom != 'invalid') $scope.NovaCompra.coupon = angular.copy($scope.InformacoesCupom);
        $scope.orderLoad = true;
        $http.post(APIBaseUrl + "/customer/addorder", { NovaCompra: $scope.NovaCompra }).then(function (res) {
            $scope.orderLoad = false;
            if (res.data.error) {
                if (res.data.product_info.max_count > 0) {
                    bootbox.alert("\
                    <h3>Ops!</h3>\
                    <p>O produto <strong>" + res.data.product_info.name + "</strong> possui apenas <strong>" + res.data.product_info.max_count + "</strong> garrafas em estoque.</p>\
                    <p>Por favor, reduza a quantidade para efetuar a compra!</p>\
                    ");
                } else {
                    bootbox.alert("\
                    <h3>Ops!</h3>\
                    <p>O produto <strong>" + res.data.product_info.name + "</strong> não possui mais estoque para venda.</p>\
                    <p>Por favor, remova-o da caixa para finalizar a compra!</p>\
                    ");
                }
            } else {
                Products.setBox([]);
                $state.go("pedido_realizado", { dadosCompra: res.data });
            }
        }, function (res) {
            $scope.orderLoad = false;
            bootbox.alert("Não foi possível finalizar seu pedido. Tente novamente.");//
        });
    };
});

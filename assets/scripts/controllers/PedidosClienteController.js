mainProject.controller('PedidosClienteController', function ($scope, $http, User) {
    $scope.$on("$viewContentLoaded", function () {
        $scope.BuscarPedidosCliente();
    })

    $scope.BuscarPedidosCliente = function () {
        $scope.load = true;
        $http.get(APIBaseUrl + "/customer/getorders/id/" + User.get().id).then(function (res) {
            $scope.load = false;
            $scope.ListaPedidos = res.data.reverse();
            angular.forEach($scope.ListaPedidos, function (pedido) {
                pedido.date_formatted = moment(pedido.date).format("DD/MM/YY");
                switch (pedido.status) {
                    case 'open':
                        pedido.status = {
                            open: true,
                            approved: false,
                            shipment: false,
                            finished: false
                        }
                        break;
                    case 'approved':
                        pedido.status = {
                            open: true,
                            approved: true,
                            shipment: false,
                            finished: false
                        }
                        break;
                    case 'shipment':
                        pedido.status = {
                            open: true,
                            approved: true,
                            shipment: true,
                            finished: false
                        }
                        break;
                    case 'finished':
                        pedido.status = {
                            open: true,
                            approved: true,
                            shipment: true,
                            finished: true
                        }
                        break;
                    default:
                        break;
                }
            });
        }, function (res) {
            $scope.load = false;
            $scope.ListaPedidos = [];
        });
    };
    $scope.ListaPedidos = [
        {
            id: 1,
            Numero: '12345678',
            Data: '27/06/2018',
            LocalEntrega: 'Casa',
            Valor: 506.5,
            CartaoPagamento: 'XXXX-XXXX-XXXX-2541',
            Entrega: {
                Endereco: 'Rua Muller Carioba, 255\
                <br/>Jardim da Saúde, CEP 04291-020\
                <br/>São Paulo - SP',
                Destinatario: 'Fernando Santos',
                Vendedor: 'Winehouse São Paulo / Jardins',
                Prazo: 5
            },
            Items: [
                {
                    name: 'Dádivas Merlot Cabernet Sauvignon',
                    quantity: 4,
                    unit_price: 62.2,
                    total_price: 248.8
                },
                {
                    name: 'Genio Español D.O.P. Jumilla Tempranillo',
                    quantity: 1,
                    unit_price: 33.8,
                    total_price: 33.8
                }
            ],
            Status: {
                created: true,
                paid: true,
                transport: false,
                finished: false
            }
        },
        // {
        //     id: 2,
        //     Numero: '12345677',
        //     Data: '27/06/2018',
        //     LocalEntrega: 'Casa',
        //     Valor: 506.5,
        //     CartaoPagamento: 'XXXX-XXXX-XXXX-2541',
        //     Entrega: {
        //         Endereco: 'Rua Muller Carioba, 255\
        //         <br/>Jardim da Saúde, CEP 04291-020\
        //         <br/>São Paulo - SP',
        //         Destinatario: 'Fernando Santos',
        //         Vendedor: 'Winehouse São Paulo / Jardins',
        //         Prazo: 5
        //     },
        //     Items: [
        //         {
        //             name: 'Dádivas Merlot Cabernet Sauvignon',
        //             quantity: 4,
        //             unit_price: 62.2,
        //             total_price: 248.8
        //         },
        //         {
        //             name: 'Genio Español D.O.P. Jumilla Tempranillo',
        //             quantity: 1,
        //             unit_price: 33.8,
        //             total_price: 33.8
        //         }
        //     ],
        //     Status: {
        //         created: true,
        //         paid: true,
        //         transport: false,
        //         finished: false
        //     }
        // }
    ];

    $scope.ToggleOrder = function (pedido) {
        pedido.expanded = !pedido.expanded;
        jQuery('#orderContent' + pedido.id).slideToggle();
    };
});

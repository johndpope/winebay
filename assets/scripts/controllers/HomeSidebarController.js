mainProject.controller("HomeSidebarController", function ($scope, Products, System, $http) {
    $scope.filterIsCollapsed = true;
    $scope.FilterGroups = [];
    var filters = System.getFilterLists();
    if ((filters.winehouses == undefined) || !filters.winehouses.length) {
        $http.get(APIBaseUrl + "/winehouse/list").then(function (res) {
            filters.winehouses = res.data;
            $scope.ProcessarFiltros();
        }, function (res) {
            console.log("Error on fetching filter:", res.data);
        });
    }
    if ((filters.tipicity == undefined) || !filters.tipicity.length) {
        $http.get(APIBaseUrl + "/tipicity/list").then(function (res) {
            filters.tipicity = res.data;
            $scope.ProcessarFiltros();
        }, function (res) {
            console.log("Error on fetching filter:", res.data);
        });
    }
    if ((filters.countries == undefined) || !filters.countries.length) {
        $http.get(APIBaseUrl + "/country/list").then(function (res) {
            filters.countries = res.data;
            $scope.ProcessarFiltros();
        }, function (res) {
            console.log("Error on fetching filter:", res.data);
        });
    }
    if ((filters.regions == undefined) || !filters.regions.length) {
        $http.get(APIBaseUrl + "/region/list").then(function (res) {
            filters.regions = res.data;
            $scope.ProcessarFiltros();
        }, function (res) {
            console.log("Error on fetching filter:", res.data);
        });
    }
    if ((filters.grapes == undefined) || !filters.grapes.length) {
        $http.get(APIBaseUrl + "/grape/list").then(function (res) {
            filters.grapes = res.data;
            $scope.ProcessarFiltros();
        }, function (res) {
            console.log("Error on fetching filter:", res.data);
        });
    }

    $scope.ProcessarFiltros = function () {
        System.setFilterLists(filters);
        $scope.FilterGroups = [
            {
                title: "Proximidade",
                attr: 'winehouse',
                inputType: "checkbox",
                hasFilter: true,
                placeholder: 'Filtrar winehouses',
                items: filters.winehouses.map(function (i) {
                    return { text: i.name, value: i.id, checked: false }
                })
            },
            {
                title: "Tipo",
                attr: 'tipicity',
                inputType: "checkbox",
                hasFilter: true,
                placeholder: 'Filtrar tipos',
                items: filters.tipicity.map(function (i) {
                    return { text: i.name, value: i.id, checked: false }
                })
            },
            {
                title: "Preço",
                attr: 'price',
                inputType: "radio",
                items: [
                    { text: "Até R$40", value: { max: 40 }, checked: false },
                    { text: "R$40 a R$60", value: { min: 40, max: 60 }, checked: false },
                    { text: "R$60 a R$100", value: { min: 60, max: 100 }, checked: false },
                    { text: "R$100 a R$200", value: { min: 100, max: 200 }, checked: false },
                    { text: "R$200 a R$500   ", value: { min: 200, max: 500 }, checked: false },
                    { text: "Acima de R$500", value: { min: 500 }, checked: false }
                ]
            },
            {
                title: "País",
                attr: 'country',
                inputType: "checkbox",
                hasFilter: true,
                placeholder: 'Filtrar países',
                items: filters.countries.map(function (i) {
                    return { text: i.name, value: i.id, checked: false }
                })
            },
            {
                title: "Região",
                attr: 'region',
                inputType: "checkbox",
                hasFilter: true,
                placeholder: 'Filtrar regiões',
                items: filters.regions.map(function (i) {
                    return { text: i.name, value: i.id, checked: false }
                })
            },
            {
                title: "Uva",
                attr: 'grape',
                inputType: "checkbox",
                hasFilter: true,
                placeholder: 'Filtrar uvas',
                items: filters.grapes.map(function (i) {
                    return { text: i.name, value: i.id, checked: false }
                })
            },
            // {
            //     title: "Volume",
            //     attr: 'volume',
            //     items: [
            //         { text: "Acima de 800ml", value: '800_to_up', checked: false },
            //         { text: "De 400ml a 800ml", value: '400_to_800', checked: false },
            //         { text: "De 50ml a 400ml", value: '50_to_400', checked: false }
            //     ]
            // },
        ];
    }

    $scope.ToggleFilter = function (item, attr) {
        var currentFilter = Products.getFilter();
        if (currentFilter[attr] == undefined) currentFilter[attr] = [];
        if (item.checked) {
            currentFilter[attr].push(item.value);
        } else {
            currentFilter[attr].splice(currentFilter[attr].indexOf(item.value), 1);
        }
        if (!currentFilter[attr].length) delete (currentFilter[attr]);
        Products.setFilter(currentFilter);
    };

    $scope.ProcessarFiltros();
});

mainProject.service('Winehouse', function() {
    var winehouseData = false;
    return {
        get: function() {
            console.log
            if (!winehouseData) {
                var temp = localStorage.getItem("winebay-wh");
                if (temp&&(temp!="")) {
                    winehouseData = JSON.parse(temp);
                }
            }
            return winehouseData;
        },
        set: function(value) {
            winehouseData=value;
            localStorage.setItem("winebay-wh", JSON.stringify(winehouseData));
        },
    };
});

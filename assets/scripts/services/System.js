mainProject.service('System', function () {
    var filterLists = {
        winehouses: [],
        tipicity: [],
        countries: [],
        regions: [],
        grapes: []
    };
    var listsLoaded = false;
    var websiteData = false;

    this.getFilterLists = function () {
        // if (!listsLoaded) {
        //     var temp = localStorage.getItem("winebay-filterlist");
        //     if (temp && (temp != "")) {
        //         filterLists = JSON.parse(temp);
        //         listsLoaded = true;
        //     }
        // }
        return filterLists;
    };

    this.setFilterLists = function (data) {
        filterLists = data;
        localStorage.setItem("winebay-filterlist", JSON.stringify(filterLists));
    };

    this.getWebsiteData = function () {
        if (!websiteData) {
            var temp = localStorage.getItem("winebay-websitedata");
            if (temp && (temp != "")) {
                websiteData = JSON.parse(temp);
            }
        }
        return websiteData;
    };

    this.setWebsiteData = function (data) {
        websiteData = data;
        localStorage.setItem("winebay-websitedata", JSON.stringify(websiteData));
    };
});

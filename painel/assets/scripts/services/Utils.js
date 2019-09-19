mainProject.service('User', function() {
    var userData = false;
    return {
        get: function() {
            if (!userData) {
                var temp = localStorage.getItem("winebay-admin");
                if (temp&&(temp!="")) {
                    userData = JSON.parse(temp);
                }
            }
            return userData;
        },
        set: function(value) {
            userData=value;
            localStorage.setItem("winebay-admin", JSON.stringify(userData));
        },
    };
});

mainProject.service('Utils', function() {
    var headerType = 'light';
    return {
        getHeaderType: function() {
            return headerType;
        },
        setHeaderType: function(value) {
            headerType = value;
        },
    };
});

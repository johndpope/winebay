mainProject.directive("headershrink", function ($window) {
    return function(scope, element, attrs) {
        angular.element($window).bind("scroll", function() {
            if (this.pageYOffset >= 50) {
                angular.element(element).find("header").addClass("header-shrink");
            } else {
                angular.element(element).find("header").removeClass("header-shrink");
            }
            scope.$apply();
        });
    };
});

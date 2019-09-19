mainProject.service('Products', function ($timeout) {
    var productsList = [];
    var productFilter = {};
    var productBox = [];
    var boxLoaded = false;
    var currentSlide = 0;
    var fixSlick = function () {
        boxLoaded = false;
        $timeout(function () {
            boxLoaded = true;
        }, 5);
        return;
        if (boxSlickFlag) {
            currentSlide = jQuery("#box-slider").slick('slickCurrentSlide');
            jQuery("#box-slider").slick('unslick');
        }
        $timeout(function () {
            jQuery("#box-slider").slick({
                infinite: false,
                autoplay: false,
                speed: 700,
                dots: false,
                centerMode: false,
                slidesToShow: 4,
                slidesToScroll: 1,
                prevArrow: jQuery(".box-slider [rel='prev-arrow']"),
                nextArrow: jQuery(".box-slider [rel='next-arrow']"),
                arrows: true,
                initialSlide: +currentSlide,
                responsive: [
                    {
                        breakpoint: 966,
                        settings: {
                            arrows: false,
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 780,
                        settings: {
                            arrows: false,
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 400,
                        settings: {
                            arrows: false,
                            slidesToShow: 1
                        }
                    }
                ]
            });
        })
        boxSlickFlag = true;
    }
    return {
        getProducts: function () {
            if (!productsList.length) {
                var temp = localStorage.getItem("winebay-productlist");
                if (temp && (temp != "")) {
                    productsList = JSON.parse(temp);
                }
            }
            return productsList;
        },
        setProducts: function (value) {
            productsList = value;
            localStorage.setItem("winebay-productlist", JSON.stringify(productsList));
        },
        getFilter: function () {
            // if ((productFilter == {}) && !filterStorageLoaded) {
            //     var temp = localStorage.getItem("winebay-homefilter");
            //     if (temp && (temp != "")) {
            //         productFilter = JSON.parse(temp);
            //         filterStorageLoaded = true;
            //     }
            // }
            return productFilter;
        },
        setFilter: function (value) {
            productFilter = value;
            // localStorage.setItem("winebay-homefilter", JSON.stringify(productFilter));
        },
        getBox: function () {
            // if (!productBox.length && !boxStorageLoaded) {
            var temp = localStorage.getItem("winebay-currentbox");
            if (temp && (temp != "")) {
                productBox = JSON.parse(temp);
                // boxStorageLoaded = true;
            }
            // }
            return productBox;
        },
        setBox: function (value) {
            productBox = value;
            localStorage.setItem("winebay-currentbox", JSON.stringify(productBox));
            fixSlick();
        },
        BoxLoaded: function () {
            return boxLoaded
        },
        addToBox: function (product) {
            productBox.push(product);
            localStorage.setItem("winebay-currentbox", JSON.stringify(productBox));
            $timeout(function() {
                fixSlick();
            }, 100);
        },
        checkItemOnBox: function (prod) {
            if (!productBox.length) prod.onBox = false;
            angular.forEach(productBox, function (boxProd) {
                if (boxProd.id == prod.id) prod.onBox = true;
            });
        },
        countBox: function () {
            var c = 0;
            angular.forEach(productBox, function (prd) {
                c += prd.quant;
            });
            return c;
        },
        toggleBox: function () {
            jQuery("#product-box-container").slideToggle();
            jQuery("header").toggleClass("box-open");
            fixSlick();
        },
        closeBox: function() {
            jQuery("#product-box-container").slideUp();
            jQuery("header").removeClass("box-open");
        }
    };
});

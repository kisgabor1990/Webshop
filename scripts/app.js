function topFunction() {
    $("html, body").animate({scrollTop: 0}, "slow");
    return false;
}

function openNav(sidenav) {
    $(".sidenav").css("width", "0");
    $("#" + sidenav).css("width", "70%");
}
function closeNav(sidenav) {
    $("#" + sidenav).css("width", "0");
}


$(function () {

    $('[data-tooltip="tooltip"]').tooltip();

    // Aktív classok pakolása, oldal betöltésekor
    let params = new URL(window.location.href).searchParams;
    if (params.get('module') !== 'products') {
        $("a.nav-" + params.get('module')).addClass("active");
    } else {
        $("a.nav-category" + params.get('category_id')).addClass("active");
    }

    // A legújabbak eltűntetése, ha nem a főoldal
    if (params.get('module') === 'home' || params.get('module') === null) {
        $("#newest").fadeIn(500);
    } else {
        $("#newest").hide();
    }

    // Mobil nézetben a menük megnyitása/becsukása
    $(".sidenavButton").click(function () {
        let sidenav = $(this).data("id");
        openNav(sidenav);
    });
    $(".closebtn").click(function () {
        let sidenav = $(this).parent().attr("id");
        closeNav(sidenav);
    });

    $(window).scroll(function () {
        // Az oldal tetejére gomb
        if ($(this).scrollTop() > 500) {
            $("#topButton").css("display", "block");
        } else {
            $("#topButton").css("display", "none");
        }

        // Az oldalsó menü követi a görgetést
        if ($(this).scrollTop() < $("#mainContent").height() - 50) {

            $("#sideCategory")
                    .addClass("sticky-top")
                    .css("top", "60px");
        } else {
            $('#sideCategory')
                    .removeClass("sticky-top")
                    .css("top", ($("#mainContent").height() - $("#sideCategory").height()) > 0 ? $("#mainContent").height() - $("#sideCategory").height() : "0");

        }
    });


    $("#topButton").click(function () {
        topFunction();
    });



    $(document)
            .on("click", "a.is-ajax", function (e) {
                e.preventDefault();
                let this_r = $(this);
                let sidenav = $(this_r).parent().attr("id");
                let url = window.location.protocol + "//" + window.location.host + "/" + this_r.attr("href");
                let params = (new URL(url)).searchParams;
                $("#loading").show();
                closeNav(sidenav);

                if (params.get('module') === 'home') {
                    $("#newest").fadeIn(500);
                } else {
                    $("#newest").fadeOut(500);
                }

                $.get(this_r.attr("href"), function (data) {

                    $("a").removeClass("active");
                    if (params.get('module') !== 'products') {
                        $("a.nav-" + params.get('module')).addClass("active");
                    } else {
                        $("a.nav-category" + params.get('category_id')).addClass("active");
                    }
                    $("#mainContent").hide().html(data).fadeIn(500);
                    window.history.pushState("", "", this_r.attr("href"));
                    $("#loading").hide();
                    $('[data-tooltip="tooltip"]').tooltip();
                })
                        .fail(function (response) {
                            alert(response);
                        });
            })
            .on('click', 'a.is-ajax', function () {
                topFunction();
            })
            .on('click', '#resetButton', function () {
                $(".form-check-input").removeAttr("checked");
            })
            .on('submit', 'form', function () {
                $(this).find(":input").filter(function () {
                    return !this.value;
                }).attr("disabled", "disabled");
            });




});
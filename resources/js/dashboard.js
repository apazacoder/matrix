//// DASHBOARD
// window.load = init();

window.initDashboard = function() {
    console.log("The page was loaded");
    setLoader();
    setMenuToggler();
    setDropdown();
    setTooltips();
    handleResize(); // the first time should be handled
    setResizeHandler();
    // setNavigation();
    setScroller();
}

function setLoader(){
    if ($("#loader-wrapper").length){
        hideLoader();
    }else{
        window.onload = function(){
            setTimeout( () => {
                hideLoader();
            }, 300);
        };
    }
}

function hideLoader(){
    $("#loader-wrapper").fadeOut(400, 'swing', function(){
        $(this).remove();
    });
}

function setSidenavScroll() {
    $("#nav-inner").slimscroll({
        distance: '0',
        position: 'left',
        height: "auto",
        color: '#ddd',
        opacity: .25,
        size: '7px',
    });
}

// Stablish the toggler for the menu
function setMenuToggler() {
    $("#menu-btn").on("click", function () {
        $("#panel-wrapper").toggleClass("sidenav-collapsed");
    });
}

function setDropdown(){
    $(".dropdown > a").on("click", function(){
        let parent = $(this).parent();
        if (parent.hasClass("is-active")){
            $(this).next().slideUp();
            parent.removeClass("is-active");
        }else{
            $(this).next().slideDown();
            parent.addClass("is-active");
        }
    });
}

// Handle hover action to show navigation text when nav is collapsed
function setTooltips() {
    let supportsTouch = 'ontouchstart' in window || navigator.msMaxTouchPoints;
    // create the element in the body
    let tooltip = '<div id="nav-tooltip">Test content</div>';

    // if the element isn't in the body add to it
    if ($("#nav-tooltip").length === 0) {
        $("#panel-wrapper").append(tooltip);
    }

    $("#nav a:not(.without-tooltip)").on("mouseover", function () {
        // console.log("here");
        let navWidth = $("#nav").width();
        // verify if the menu is hidden
        if ($("#panel-wrapper").hasClass("sidenav-collapsed") && !supportsTouch) {
            // get the position of the link
            let offset = $(this).offset();
            // change the text, visibility and position of the tooltip
            $("#nav-tooltip").stop().text($(this).children("span").text()).css({
                "top": offset.top + "px",
                "left": offset.left + navWidth + 10 + "px"
            }).fadeIn(350);
        }
    }).on("mouseout", function () {
        if ($("#panel-wrapper").stop().hasClass("sidenav-collapsed")) {
            // empty the text, hide and position the tooltip
            $("#nav-tooltip").text("").css({
                "top": 0,
                "left": 0,
                "display": "none",
            });
        }
    });
}

// Resize window to hide / collapse the sidenav depending on the viewport size
window.handleResize = function() {
    let viewportWidth = $(window).width();
    if (viewportWidth <= 1200) {
        $("#panel-wrapper").addClass("sidenav-collapsed");
    } else {
        $("#panel-wrapper").removeClass("sidenav-collapsed");
    }
    // setSidenavScroll();
}

// Handle resize of the window without overloading the user system
window.doit;

window.setResizeHandler = function() {
    window.onresize = function () {
        clearTimeout(window.doit);
        window.doit = setTimeout(window.handleResize, 300);
    };
}


function setNavigation() {
    // console.log("navigation set");
    $("#nav a").click(function () {
        let href = $(this).attr("href");
        if (href) {
            var scrollToElement = $(href); //aquí no están permitidos los atributos div, usar ids

            // A element to serve as a placeholder
            var headerHeight = $('#top-nav').height();

            // var scrollToPosition = scrollToElement.offset().top - headerHeight;
            var scrollToPosition = scrollToElement[0].offsetTop - headerHeight;

            $("#main").animate({
                scrollTop: scrollToPosition
            }, 700, "swing");

            /* To add/remove class */
            $('#nav li').removeClass("is-active"); // first remove class from all menu items
            $(this).parent("li").addClass('is-active'); // then add to the clicked item

            return false;
        }
    });
}

let scrollTimeout;
function setScroller() {
    // console.log("scroller set");

    // find all sections
    let allSections = $("#main section");
    // console.log("allSections", allSections.length);

    $("#main").on("scroll", function () {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            handleScroll(allSections);
        }, 150);
    });
}

function handleScroll(allSections) {
    // stablish the mid point of the viewport
    let focusPoint = parseInt($(window).height() / 2);

    // test for every section
    allSections.each(function (i, e) {
        let sectionId = $(e).attr("id")
        if (sectionId) {

            // for each section obtain its initial and final within the viewport
            let initial = $(e).offset().top;
            let final = initial + $(e).height();

            if (focusPoint >= initial && focusPoint <= final){
                // active the link of the element
                $("#nav li").removeClass("is-active");
                $(`a[href='#${sectionId}']`).parent().addClass("is-active");

                // break if we found the one
                return false;
            }
        }
    });
}


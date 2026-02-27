// @pk305
$(document).ready(function () {
    /* tooltip */
    const tooltipTriggerList = document.querySelectorAll(
        '[data-bs-toggle="tooltip"]'
    );
    const tooltipList = [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );

    /* popover  */
    const popoverTriggerList = document.querySelectorAll(
        '[data-bs-toggle="popover"]'
    );
    const popoverList = [...popoverTriggerList].map(
        (popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl)
    );

    /* footer year */
    document.getElementById("year").innerHTML = new Date().getFullYear();
    /* footer year */

    /* node waves */
    Waves.attach(".btn-wave", ["waves-light"]);
    Waves.init();
    /* node waves */

    /* card with close button */
    let DIV_CARD = ".card";
    let cardRemoveBtn = document.querySelectorAll(
        '[data-bs-toggle="card-remove"]'
    );
    cardRemoveBtn.forEach((ele) => {
        ele.addEventListener("click", function (e) {
            e.preventDefault();
            let $this = this;
            let card = $this.closest(DIV_CARD);
            card.remove();
            return false;
        });
    });
    /* card with close button */

    /* card with fullscreen */
    let cardFullscreenBtn = document.querySelectorAll(
        '[data-bs-toggle="card-fullscreen"]'
    );
    cardFullscreenBtn.forEach((ele) => {
        ele.addEventListener("click", function (e) {
            let $this = this;
            let card = $this.closest(DIV_CARD);
            card.classList.toggle("card-fullscreen");
            card.classList.remove("card-collapsed");
            e.preventDefault();
            return false;
        });
    });
    /* card with fullscreen */

    /* count-up */
    var i = 1;
    setInterval(() => {
        document.querySelectorAll(".count-up").forEach((ele) => {
            if (ele.getAttribute("data-count") >= i) {
                i = i + 1;
                ele.innerText = i;
            }
        });
    }, 10);
    /* count-up */

    /* back to top */
    const scrollToTop = document.querySelector(".scrollToTop");

    const $rootElement = document.documentElement;
    const $body = document.body;
    window.onscroll = () => {
        const scrollTop = window.scrollY || window.pageYOffset;
        const clientHt = $rootElement.scrollHeight - $rootElement.clientHeight;
        if (window.scrollY > 100) {
            scrollToTop.style.display = "flex";
        } else {
            scrollToTop.style.display = "none";
        }
    };
    scrollToTop.onclick = () => {
        window.scrollTo(0, 0);
    };
    /* back to top */
});

/* Choices JS */
document.addEventListener("DOMContentLoaded", function () {
    var genericExamples = document.querySelectorAll("[data-trigger]");
    for (let i = 0; i < genericExamples.length; ++i) {
        var element = genericExamples[i];
        new Choices(element, {
            allowHTML: true,
            placeholderValue: "This is a placeholder set in the config",
            searchPlaceholderValue: "Search",
        });
    }
});
/* Choices JS */

/* header theme toggle */
function toggleTheme() {
    let html = document.querySelector("html");
    if (html.getAttribute("data-theme-mode") === "dark") {
        html.setAttribute("data-theme-mode", "light");
        html.setAttribute("data-header-styles", "light");
        html.setAttribute("data-menu-styles", "light");
        html.removeAttribute("data-bg-theme");
        html.removeAttribute("style");
        document
            .querySelector("html")
            .style.removeProperty("--body-bg-rgb", localStorage.bodyBgRGB);
        localStorage.removeItem("ynexdarktheme");
        localStorage.removeItem("ynexMenu");
        localStorage.removeItem("ynexHeader");
        localStorage.removeItem("bodylightRGB");
        localStorage.removeItem("bodyBgRGB");
        if (localStorage.getItem("ynexlayout") != "horizontal") {
            html.setAttribute("data-menu-styles", "dark");
        }
        console.log("light");
        html.setAttribute("data-header-styles", "light");
    } else {
        console.log("dark");

        html.setAttribute("data-theme-mode", "dark");
        html.setAttribute("data-header-styles", "dark");
        html.setAttribute("data-menu-styles", "dark");
        localStorage.setItem("ynexdarktheme", "true");
        localStorage.setItem("ynexMenu", "dark");
        localStorage.setItem("ynexHeader", "dark");
        localStorage.removeItem("bodylightRGB");
        localStorage.removeItem("bodyBgRGB");
    }
}

/* header theme toggle */

/* full screen */
function openFullscreen() {
    var elem = document.documentElement;
    let open = document.querySelector(".full-screen-open");
    let close = document.querySelector(".full-screen-close");

    if (
        !document.fullscreenElement &&
        !document.webkitFullscreenElement &&
        !document.msFullscreenElement
    ) {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            /* IE11 */
            elem.msRequestFullscreen();
        }
        close.classList.add("d-block");
        close.classList.remove("d-none");
        open.classList.add("d-none");
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            /* Safari */
            document.webkitExitFullscreen();
            console.log("working");
        } else if (document.msExitFullscreen) {
            /* IE11 */
            document.msExitFullscreen();
        }
        close.classList.remove("d-block");
        open.classList.remove("d-none");
        close.classList.add("d-none");
        open.classList.add("d-block");
    }
}
/* full screen */

/* toggle switches */
let customSwitch = document.querySelectorAll(".toggle");
customSwitch.forEach((e) =>
    e.addEventListener("click", () => {
        e.classList.toggle("on");
    })
);
/* toggle switches */

/* header dropdown close button */

/* for cart dropdown */
const headerbtn = document.querySelectorAll(".dropdown-item-close");
headerbtn.forEach((button) => {
    button.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        button.parentNode.parentNode.parentNode.parentNode.parentNode.remove();
        document.getElementById("cart-data").innerText = `${
            document.querySelectorAll(".dropdown-item-close").length
        } Items`;
        document.getElementById("cart-icon-badge").innerText = `${
            document.querySelectorAll(".dropdown-item-close").length
        }`;
        console.log(
            document.getElementById("header-cart-items-scroll").children.length
        );
        if (document.querySelectorAll(".dropdown-item-close").length == 0) {
            let elementHide = document.querySelector(".empty-header-item");
            let elementShow = document.querySelector(".empty-item");
            elementHide.classList.add("d-none");
            elementShow.classList.remove("d-none");
        }
    });
});
/* for cart dropdown */

/* for notifications dropdown */
const headerbtn1 = document.querySelectorAll(".dropdown-item-close1");
headerbtn1.forEach((button) => {
    button.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        button.parentNode.parentNode.parentNode.parentNode.remove();
        document.getElementById("notifiation-data").innerText = `${
            document.querySelectorAll(".dropdown-item-close1").length
        } Unread`;
        document.getElementById("notification-icon-badge").innerText = `${
            document.querySelectorAll(".dropdown-item-close1").length
        }`;
        if (document.querySelectorAll(".dropdown-item-close1").length == 0) {
            let elementHide1 = document.querySelector(".empty-header-item1");
            let elementShow1 = document.querySelector(".empty-item1");
            elementHide1.classList.add("d-none");
            elementShow1.classList.remove("d-none");
        }
    });
});
/* for notifications dropdown */

function removeCommas(amount) {
    // amount = amount.toString()

    // if (amount.match(/\.\d{2}$/)) {
    //     unformated = amount.replace(/,(?=.*\.\d+)/g, '');
    //     return unformated
    // }
    // console.log('amount to remove commas',amount,typeof amount != 'undefined');
    let numberWithoutCommas = amount;

    if (typeof amount != "undefined") {
        amount = amount.toString();
        // return amount
        numberWithoutCommas = amount.replace(/,/g, "");
    }
    return numberWithoutCommas;
}

function numberWithCommas(number) {
    // removeCommas(number);
    // number = number.toString().replace(",", "");

    // Convert the number to a string
    let numStr = number.toString().replace(",", "");

    // numStr = parseFloat(numStr).toFixed(2);

    //
    if (numStr.length > 6) {
        numStr = numStr.replace(",", "");
    }

    if (numStr.length > 11) {
        numStr = numStr.replace(",", "");
    }

    if (numStr.length > 14) {
        numStr = numStr.replace(",", "");
    }

    if (numStr.length > 17) {
        numStr = numStr.replace(",", "");
    }
    //
    // Split the string into integer and decimal parts
    let parts = numStr.split(".");
    let integerPart = parts[0];
    let decimalPart = parts.length > 1 ? "." + parts[1] : "";

    //    // If decimalPart is empty, add .00 to ensure two decimal places
    //    if (decimalPart === '') {
    //     decimalPart = '.00';
    // } else if (decimalPart.length === 2) {
    //     // If decimalPart has only one decimal place, add another zero
    //     decimalPart += '0';
    // }
    // Add commas as thousand separators to the integer part
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    // Combine the integer and decimal parts
    let formattedNumber = integerPart + decimalPart;

    return formattedNumber;
}

function showServerSideValidationErrors(errors) {
    // Validation errors
    for (let key in errors) {
        if (errors.hasOwnProperty(key)) {
            toastr.error(errors[key][0]);
        }
    }
}

function getCsrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
}

function fetchWithCsrf(url, options) {
    options = options || {};
    options.headers = options.headers || {};
    options.headers["X-CSRF-TOKEN"] = getCsrfToken();

    return fetch(url, options);
}

$(".amount").keyup(function () {
    const value = numberWithCommas($(this).val());

    $(this).val(value);
});

$(".amount").change(function () {
    const value = numberWithCommas($(this).val());

    $(this).val(value);
});

function toUpperCase(string) {
    return string.toUpperCase();
}

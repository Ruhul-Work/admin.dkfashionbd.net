const themeColor = localStorage.getItem("selectedColorValue");
const themeColorHtmlElement = document.getElementsByTagName("html")[0];
themeColorHtmlElement.setAttribute("data-layout-mode", "light_mode");
if (themeColor) {
    const themeColorElement = document.querySelector(`input[value="${themeColor}"]`);
    const themeColorHtmlElement = document.getElementsByTagName("html")[0];
    themeColorHtmlElement.setAttribute("data-layout-mode", themeColor);
    if (themeColorElement) {
        themeColorElement.checked = true;
    }
}
const colorButtons = document.querySelectorAll("input.color-check");
colorButtons.forEach((color) => {
    color.addEventListener("change", (event) => {
        const selectedColor = event.target.value;
        localStorage.setItem("selectedColorValue", selectedColor);
        if (selectedColor) {
            const themeColorHtmlElement = document.getElementsByTagName("html")[0];
            themeColorHtmlElement.setAttribute("data-layout-mode", selectedColor);
        }
    });
});
const dataLayout = localStorage.getItem("selectedLayoutValues");
const dataLayoutHtmlElement = document.getElementsByTagName("html")[0];
dataLayoutHtmlElement.setAttribute("data-layout-style", "default");
if (dataLayout) {
    const dataLayoutElement = document.querySelector(`input[value="${dataLayout}"]`);
    const dataLayoutHtmlElement = document.getElementsByTagName("html")[0];
    dataLayoutHtmlElement.setAttribute("data-layout-style", dataLayout);
    if (dataLayoutElement) {
        dataLayoutElement.checked = true;
    }
}
const layoutButtons = document.querySelectorAll("input.layout-mode");
layoutButtons.forEach((layout) => {
    layout.addEventListener("change", (event) => {
        const selectedValues = event.target.value;
        localStorage.setItem("selectedLayoutValues", selectedValues);
        if (selectedValues) {
            const themeColorHtmlElement = document.getElementsByTagName("html")[0];
            themeColorHtmlElement.setAttribute("data-layout-style", selectedValues);
        }
    });
});
const navigationColor = localStorage.getItem("selectedNavcolorValue");
const navigationColorHtmlElement = document.getElementsByTagName("html")[0];
navigationColorHtmlElement.setAttribute("data-nav-color", "light");
if (navigationColor) {
    const navigationColorElement = document.querySelector(`input[value="${navigationColor}"]`);
    const navigationColorHtmlElement = document.getElementsByTagName("html")[0];
    navigationColorHtmlElement.setAttribute("data-nav-color", navigationColor);
    if (navigationColorElement) {
        navigationColorElement.checked = true;
    }
}
const navcolorButtons = document.querySelectorAll("input.nav-color");
navcolorButtons.forEach((navcolor) => {
    navcolor.addEventListener("change", (event) => {
        const selectedNavcolor = event.target.value;
        localStorage.setItem("selectedNavcolorValue", selectedNavcolor);
        if (selectedNavcolor) {
            const navigationColorHtmlElement = document.getElementsByTagName("html")[0];
            navigationColorHtmlElement.setAttribute("data-nav-color", selectedNavcolor);
        }
    });
});
let enableFirstFunction = true;
function toggleClass() {
    var mainLayout = document.getElementById("layoutDiv");
    if (enableFirstFunction) {
        mainLayout.classList.add("show-settings");
        document.body.style.overflow = "auto";
    } else {
        enableFirstFunction = !enableFirstFunction;
        mainLayout.classList.remove("show-settings");
        document.body.style.overflow = "auto";
    }
}
var darkModeLayout = document.getElementById("dark_mode");
var lightModeLayout = document.getElementById("light_mode");
var boxModeLayout = document.getElementById("box_layout");
if (darkModeLayout) {
    darkModeLayout.addEventListener("click", toggleClass);
} else if (lightModeLayout) {
    lightModeLayout.addEventListener("click", toggleClass);
}
function resetData() {
    document.getElementById("light_mode").checked = true;
    document.getElementById("default_layout").checked = true;
    document.getElementById("light_color").checked = true;
    document.body.style.overflow = "auto";
    const htmlElment = document.getElementsByTagName("html")[0];
    localStorage.setItem("selectedLayoutValues", "default");
    localStorage.setItem("selectedColorValue", "light_mode");
    localStorage.setItem("selectedNavcolorValue", "light");
    htmlElment.setAttribute("data-layout-mode", "light_mode");
    htmlElment.setAttribute("data-layout-style", "default");
    htmlElment.setAttribute("data-nav-color", "light");
    return false;
}

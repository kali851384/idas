document.addEventListener("DOMContentLoaded", function () {

    const element = document.getElementById("symptomSelectInput");

    if (!element) return;

    if (typeof Choices === "undefined") {
        console.error("Choices.js wurde nicht geladen");
        return;
    }

    new Choices(element, {
        searchEnabled: true,
        removeItemButton: true,
        closeDropdownOnSelect: false,
        maxItemCount: -1,
        shouldSort: false
    });

}); 
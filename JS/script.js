 // JS teil um profil menü zu passen -->
const profilImg = document.getElementById("profilImg");
const menu = document.getElementById("profilMenu");

// wenn man den prfilbild klickt
if (profilImg && menu) {
  profilImg.addEventListener("click", function (e) {
    e.stopPropagation();
    menu.classList.toggle("show");
  });

// wenn man außerhalb der Menü klickt
  document.addEventListener("click", function (e) {
    if (!menu.contains(e.target) && !profilImg.contains(e.target)) {
      menu.classList.remove("show");   // menü schließen
    }
  });
}

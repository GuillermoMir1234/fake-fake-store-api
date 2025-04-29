<?php
include("includes/head.html");
include("includes/menu.php");
?>
  <div class="container">
    <h3 id="titol-categoria">PRODUCTES DE LA CATEGORIA - ...</h3>
    <div id="llistat-productes" class="llistat-productes"></div>
  </div>

  <script>
    function getParam(param) {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(param);
    }

    let categoria = getParam('categoria');
    let url = "";

    if (!categoria || categoria === "totes") {
    categoria = "totes";
    url = "api/productes.php";
    } else {
    url = "api/productes.php?category=" + encodeURIComponent(categoria);
    }

    console.log(url);

    document.getElementById("titol-categoria").innerText =
      "PRODUCTES DE LA CATEGORIA - " + categoria.charAt(0).toUpperCase() + categoria.slice(1);

    fetch(url)
      .then(response => response.json())
      .then(data => {
        const container = document.getElementById("llistat-productes");

        if (Array.isArray(data)) {
          data.forEach(product => {
            const div = document.createElement("div");
            div.className = "producte-mini";
            div.innerHTML = `
              <img src="${product.image}" alt="Imatge del producte">
              <div><a href="veureProducte.php?id=${product.id}">${product.title}</a></div>
              <div class="preu">$${product.price}</div>
              <div class="rating">${product.rating.rate} (${product.rating.count})</div>
            `;
            container.appendChild(div);
          });
        } else {
          container.innerHTML = "<p>Error al obtenir les dades de l'API.</p>";
        }
      })
      .catch(error => {
        document.getElementById("llistat-productes").innerHTML =
          "<p>Error de connexió a l'API.</p>";
        console.error(error);
      });

    fetch("includes/menu.php")
      .then(res => res.text())
      .then(html => document.getElementById("menu-container").innerHTML = html);

    fetch("includes/foot.html")
      .then(res => res.text())
      .then(html => document.getElementById("footer-container").innerHTML = html);
  </script>

<?php
include("includes/foot.html");
?>
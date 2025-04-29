<?php
include("includes/head.html");
include("includes/menu.php");
?>
<div class="container">
  <h3 id="titol-categoria">
    GESTIÓ DE PRODUCTES
    <a href="afegirProducte.php" class="btn btn-afegir">+ Gestionar productes</a>
  </h3>
  <div id="llistat-productes" class="llistat-productes"></div>
</div>

<script>
  function getParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  function eliminarProducte(id) {
    if (confirm("Segur que vols eliminar aquest producte?")) {
      fetch(`api/productes.php?id=${id}`, {
        method: "DELETE"
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          alert("Error: " + (data.error || "No s'ha pogut eliminar"));
        }
      })
      .catch(err => {
        console.error("Error eliminant:", err);
        alert("Error de connexió");
      });
    }
  }

  let categoria = getParam('categoria');
  let url = "";

  fetch("api/productes.php")
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById("llistat-productes");

      if (Array.isArray(data)) {
        data.forEach(product => {
          const div = document.createElement("div");
          div.className = "producte-mini";
          div.innerHTML = `
            <img src="${product.image}" alt="Imatge del producte">
            <div class="title">${product.title}</div>
            <div class="preu">$${product.price}</div>
            <div class="rating">${product.rating.rate} (${product.rating.count})</div>
            <div class="accio-producte">
              <button onclick="location.href='modificarProducte.php?id=${product.id}'">Modificar</button>
              <button onclick="eliminarProducte(${product.id})">Eliminar</button>
            </div>
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

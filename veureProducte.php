<?php
include("includes/head.html");
include("includes/menu.php");

if(!isset($_REQUEST['id']) || $_REQUEST['id']==""){
    header("location: index.php");
    exit;
}
?>

<div class="container">
  <div id="producte-container"></div>
</div>

<script>
  function getParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  let id = getParam('id');
  let url = id ? `api/productes.php?id=${id}` : "api/productes.php?id=1";

  fetch(url)
    .then(response => response.json())
    .then(data => {
      const container = document.getElementById("producte-container");

      if (data) {
        const product = data;

        const div = document.createElement("div");
        div.className = "producte";
        div.innerHTML = `
          <img src="${product.image}" alt="Imatge del producte">
          <div class="producte-info">
            <h4>${product.title}</h4>
            <p class="preu">Preu: $${product.price}</p>
            <p>${product.description}</p>
            <p class="categoria">Categoria: <a href="veureProductesCategoria.php?categoria=${encodeURIComponent(product.category)}">${product.category}</a></p>
            <p class="rating">Puntuació: ${product.rating.rate} (${product.rating.count} valoracions)</p>
          </div>
        `;
        container.appendChild(div);
      } else {
        container.innerHTML = "<p>Error al obtenir les dades de l'API.</p>";
      }
    })
    .catch(error => {
      document.getElementById("producte-container").innerHTML =
        "<p>Error de connexió a l'API.</p>";
      console.error(error);
    });
</script>

<?php
include("includes/foot.html");
?>

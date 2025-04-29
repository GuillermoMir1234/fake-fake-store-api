<?php
include("includes/head.html");
include("includes/menu.php");
?>

<div class="container">
  <h3>AFEGIR NOU PRODUCTE</h3>
  <form id="form-producte">
    <label for="title">Nom:</label><br>
    <input type="text" name="title" id="title" required><br><br>

    <label for="price">Preu:</label><br>
    <input type="number" step="0.01" name="price" id="price" required><br><br>

    <label for="description">Descripció:</label><br>
    <textarea name="description" id="description" rows="4" required></textarea><br><br>

    <label for="category">Categoria:</label><br>
    <input list="categories" name="category" id="category" required>
    <datalist id="categories"></datalist><br><br>

    <label for="image">URL de la imatge:</label><br>
    <input type="url" name="image" id="image" required><br><br>

    <button type="submit">Afegir producte</button>
  </form>

  <div id="resposta" style="margin-top:20px;"></div>
</div>

<script>
  // Carregar les categories al datalist
  fetch("api/productes.php?categories=all")
    .then(response => response.json())
    .then(data => {
      const datalist = document.getElementById("categories");
      data.forEach(cat => {
        const opt = document.createElement("option");
        opt.value = cat;
        datalist.appendChild(opt);
      });
    })
    .catch(error => {
      console.error("Error carregant categories:", error);
    });

  // Enviar el formulari amb JSON
  document.getElementById("form-producte").addEventListener("submit", function(e) {
    e.preventDefault();

    const categoria = document.getElementById("category").value;

    const dades = {
      title: document.getElementById("title").value,
      price: parseFloat(document.getElementById("price").value),
      description: document.getElementById("description").value,
      category: document.getElementById("category").value,
      image: document.getElementById("image").value
    };

    fetch("api/productes.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(dades)
    })
    .then(response => response.json())
    .then(data => {
    if (data.success) {
      window.location.href = `veureProductesCategoria.php?categoria=${encodeURIComponent(categoria)}`;
    } else {
      document.getElementById("resposta").innerHTML = `<p style="color: red;">${data.error}</p>`;
    }
    })
    .catch(error => {
      console.error("Error enviant dades:", error);
      document.getElementById("resposta").innerHTML = `<p style="color: red;">Error inesperat</p>`;
    });
  });
</script>

<?php
include("includes/foot.html");
?>

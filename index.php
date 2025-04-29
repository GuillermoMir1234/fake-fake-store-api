<?php
include("includes/head.html");
include("includes/menu.php");
?>

<div class="container">
    <h3>CATEGORIES:</h3>
    <ul class="categories" id="llistaCategories">
        <li>Carregant categories...</li>
    </ul>
</div>

<script>
    fetch('api/productes.php?categories=all')
        .then(response => response.json())
        .then(categories => {
            const llista = document.getElementById('llistaCategories');
            llista.innerHTML = '';

            categories.forEach(category => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = 'veureProductesCategoria.php?categoria=' + encodeURIComponent(category);
                a.textContent = category;
                li.appendChild(a);
                llista.appendChild(li);
            });
        })
        .catch(error => {
            document.getElementById('llistaCategories').innerHTML = '<li>Error en carregar les categories.</li>';
            console.error('Error:', error);
        });
</script>

<?php
include("includes/foot.html");
?>

</main>

<footer class="pied">
    <div class="conteneur pied-int">
        <span>© <?= date('Y') ?> GestionProduits</span>
        <span class="discret">Catalogue, catégories et suivi du stock</span>
    </div>
</footer>

<script>
// Confirmation avant suppression
document.addEventListener('submit', function (ev) {
    var msg = ev.target.getAttribute('data-confirmer');
    if (msg && !window.confirm(msg)) ev.preventDefault();
});
</script>
</body>
</html>

</main>
<footer class="pie">
  <div class="contenedor pie__grid">
    <section aria-label="Identidad del colegio">
      <strong class="pie__titulo"><?= escapar(NOMBRE_COLEGIO) ?></strong>
      <p><?= escapar(LEMA) ?></p>
    </section>
    <section aria-label="Información de contacto">
      <strong class="pie__titulo">Información</strong>
      <p><?= escapar(DIRECCION) ?></p>
      <p><?= escapar(TELEFONO) ?><br><?= escapar(CORREO) ?></p>
    </section>
    <nav aria-label="Navegación del pie">
      <strong class="pie__titulo">Explora</strong>
      <?php foreach (MENU as $archivo => $texto): ?>
        <a href="<?= escapar($archivo) ?>"><?= escapar($texto) ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
  <p class="pie__nota">Sitio ficticio con fines académicos · <?= date('Y') ?></p>
</footer>
<script src="js/app.js" defer></script>
</body>
</html>

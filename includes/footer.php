</main>
<footer class="pie"><div class="contenedor pie__grid">
  <div><strong><?= escapar(NOMBRE_COLEGIO) ?></strong><p><?= escapar(LEMA) ?></p></div>
  <div><p><?= escapar(DIRECCION) ?></p><p><?= escapar(TELEFONO) ?> · <?= escapar(CORREO) ?></p></div>
  <nav aria-label="Pie"><?php foreach (MENU as $a => $t): ?><a href="<?= $a ?>"><?= escapar($t) ?></a> <?php endforeach; ?></nav>
</div><p class="pie__nota">Sitio ficticio con fines académicos · <?= date('Y') ?></p></footer>
<script src="js/app.js"></script></body></html>

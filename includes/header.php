<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
require_once __DIR__.'/funciones.php';
$titulo = $titulo ?? 'Inicio';
?><!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= escapar($titulo) ?> · <?= escapar(NOMBRE_COLEGIO) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;800&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
<link rel="icon" href="img/ccolibri.png"><link rel="stylesheet" href="css/estilos.css"></head>
<body><a class="saltar" href="#contenido">Saltar al contenido</a>
<header class="cabecera"><div class="contenedor barra">
  <a class="marca" href="index.php"><img src="img/ccolibri.png" alt="" width="84" height="84"><span><?= escapar(NOMBRE_COLEGIO) ?></span></a>
  <button class="burger" aria-expanded="false" aria-controls="menu">Menú</button>
  <nav id="menu" class="menu" aria-label="Principal">
  <?php foreach (MENU as $archivo => $texto): ?>
    <a href="<?= $archivo ?>" class="<?= $archivo === 'agenda.php' ? 'menu__cta' : '' ?>"<?= esPaginaActual($archivo) ? ' aria-current="page"' : '' ?>><?= escapar($texto) ?></a>
  <?php endforeach; ?></nav></div></header>
<main id="contenido">

# Pruebas y lista de entrega — Colegio Colibrí

**Importante:** esta lista es una guía para ejecutar pruebas. Las casillas quedan sin marcar hasta que el estudiante realice y documente cada comprobación en su equipo.

## 1. Pruebas funcionales

- [ ] Las cinco páginas cargan: Inicio, Nosotros, Experiencias, Pre-matrícula y Agenda.
- [ ] Los enlaces del menú y del pie llevan a la página correcta.
- [ ] El menú resalta la página actual mediante `aria-current="page"`.
- [ ] La pre-matrícula vacía muestra errores y conserva los valores ingresados.
- [ ] Una pre-matrícula válida con datos ficticios genera un radicado y crea/actualiza `datos/prematriculas.csv`.
- [ ] Se rechaza una fecha de nacimiento futura y edades fuera del rango definido.
- [ ] Se rechaza un grado o parentesco que no pertenezca a las opciones permitidas.
- [ ] La agenda rechaza sábados y domingos.
- [ ] La agenda rechaza fechas fuera del periodo permitido.
- [ ] Se pueden guardar dos citas ficticias en una misma fecha y franja.
- [ ] La tercera cita de la misma fecha y franja se rechaza.
- [ ] Los filtros Todas, Arte, Ciencia, Huerta y Música muestran las tarjetas correctas.
- [ ] La consola del navegador no presenta errores JavaScript.

## 2. Pruebas de seguridad y privacidad

- [ ] `includes/` y `datos/` están fuera del DocumentRoot (`publico/`).
- [ ] La carpeta `datos/` contiene `.htaccess` con denegación de acceso cuando se usa Apache.
- [ ] Enviar texto como `<script>alert(1)</script>` no ejecuta código y se muestra escapado o se rechaza.
- [ ] Probar valores que comiencen por `=`, `+`, `-` y `@`: el CSV debe anteponer apóstrofo mediante `celdaSegura()`.
- [ ] Los formularios validan en PHP aunque se desactive JavaScript.
- [ ] No se incluyen CSV con registros en el ZIP.
- [ ] Todas las pruebas usan datos inventados y correos `.example`.

## 3. Pruebas responsivas

Realice las pruebas con las herramientas de desarrollador del navegador.

- [ ] A 360 px no aparece desplazamiento horizontal.
- [ ] A 390 px el menú móvil, formularios y botones se pueden utilizar.
- [ ] A 768 px las rejillas y campos se adaptan correctamente.
- [ ] A 1280 px se visualizan las columnas de escritorio.
- [ ] El menú móvil abre y cierra con clic.
- [ ] La tecla Escape cierra el menú y devuelve el foco al botón.
- [ ] El contenido sigue siendo utilizable con zoom del navegador al 200 %.

## 4. Accesibilidad básica

- [ ] Se puede recorrer el sitio con Tab y Shift+Tab.
- [ ] El enlace «Saltar al contenido» aparece al recibir foco.
- [ ] Los campos tienen etiquetas `label` asociadas.
- [ ] Los errores se asocian al campo con `aria-describedby` y `aria-invalid`.
- [ ] El menú informa su estado mediante `aria-expanded`.
- [ ] Los filtros informan el estado mediante `aria-pressed`.
- [ ] Las imágenes informativas tienen texto alternativo; las decorativas usan `alt=""`.
- [ ] El movimiento se reduce cuando el sistema tiene activada la preferencia correspondiente.

## 5. Capturas que debe adjuntar el estudiante

- [ ] Inicio en escritorio.
- [ ] Nosotros en escritorio.
- [ ] Experiencias en escritorio.
- [ ] Pre-matrícula en escritorio.
- [ ] Agenda en escritorio.
- [ ] Inicio o menú en celular.
- [ ] Formulario en celular.

## 6. Archivos para entregar

- [ ] ZIP del proyecto sin `.git`, archivos temporales ni datos CSV.
- [ ] `LEEME.txt` con instrucciones para ejecutar.
- [ ] Este documento de pruebas con resultados reales.
- [ ] Reflexión personal de media página, revisada y escrita con las propias palabras del estudiante.

## 7. Rúbrica de referencia

La guía distribuye la evaluación en siete criterios:
- Estructura y organización: 15 %.
- HTML y accesibilidad: 15 %.
- CSS y diseño responsivo: 20 %.
- JavaScript: 10 %.
- PHP y formularios: 25 %.
- Seguridad y privacidad: 10 %.
- Presentación y entrega: 5 %.

La existencia de esta lista no significa que las pruebas estén aprobadas. El estudiante debe ejecutarlas y conservar evidencia.

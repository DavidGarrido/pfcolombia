<link rel="stylesheet" href="styles/reporte.css">
<div class="container">
  <h2 class="title">📊 Generar Reporte</h2>

  <!-- Formulario de selección de fechas -->
  <form id="reporte-form" class="formulario">
    <label for="fecha-inicio">Desde:</label>
    <input type="date" id="fecha-inicio" required>

    <label for="fecha-fin">Hasta:</label>
    <input type="date" id="fecha-fin" required>

    <label for="convocatorias">Convocatorias Realizadas:</label>
    <input type="number" id="convocatorias" required>

    <label for="iglesias">Iglesias en los patios:</label>
    <input type="number" id="iglesias" required>

    <label for="lideres">Líderes Internos:</label>
    <input type="number" id="lideres" required>

    <label for="voluntarios">Voluntarios Nacionales:</label>
    <input type="number" id="voluntarios" required>

    <label for="pospenados">Pospenados Atendidos:</label>
    <input type="number" id="pospenados" required>

    <label class="custom-checkbox">
      <input type="checkbox" id="generar_pdf" name="generar_pdf">
      <span class="checkmark"></span>
      Generar PDF
    </label>

    <button type="submit" class="btn">📈 Generar Reporte</button>
  </form>

  <!-- Contenedor donde se mostrará el reporte -->
  <div id="reporte-container" class="reporte" style="display: none;">
    <h3>📌 Datos del Reporte</h3>
    <div id="datos-reporte"></div> <!-- Aquí se insertarán los datos -->

    <!-- Lienzo para la gráfica -->
    <canvas id="grafica-reporte"></canvas>

    <button id="exportar-pdf" class="btn">📄 Exportar a PDF</button>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Librería para gráficas -->
<script src="scripts/anual_report.js"></script> <!-- JavaScript separado -->

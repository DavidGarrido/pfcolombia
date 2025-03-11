
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("filtroReporte");

  form.addEventListener("submit", function (event) {
    event.preventDefault(); // Evita el envío normal del formulario

    // Obtener los valores de los selects
    const proyecto = document.getElementById("selectorProyecto").value.trim();
    const anio = document.getElementById("anio").value.trim();

    // Validar que se hayan seleccionado valores
    if (!proyecto) {
      console.error("Error: Debes seleccionar un proyecto.");
      alert("Por favor, selecciona un proyecto.");
      return;
    }
    if (!anio) {
      console.error("Error: Debes seleccionar un año.");
      alert("Por favor, selecciona un año.");
      return;
    }

    // Crear FormData y agregar valores
    const formData = new FormData();
    formData.append("proyecto", proyecto);
    formData.append("anio", anio);

    // Enviar la petición a la API
    fetch("/api/anual_report.php", {
      method: "POST",
      body: formData,
    })
      .then(response => response.json())
      .then(data => {
        console.log("Respuesta del servidor:", data);

        // Aquí puedes actualizar la interfaz o mostrar otros datos según la respuesta

        // Si el proyecto es "proyecto-felipe", dibujar la gráfica
        if (proyecto === "proyecto-felipe") {
          // Cargar la librería de Google Charts
          google.charts.load("current", { packages: ["corechart"] });
          google.charts.setOnLoadCallback(function () {
            // Crear la DataTable con los datos retornados por la API
            var chartData = google.visualization.arrayToDataTable([
              ["Elemento", "Valor", { role: "style" }],
              ["Prisioneros invitados al curso", data.total_poblacion, "#239B56"],
              ["Prisioneros inscritos en el curso", data.prns_invitados, "#F39C12"],
              ["Prisioneros que iniciaron el curso", data.prns_iniciaron, "#F1C40F"],
              ["Total de graduados", data.prns_graduados, "#C0392B"],
              ["Cursos completados", data.cursos_act, "#E74C3C"],
              ["Voluntarios internos", data.internos, "#8E44AD"],
              ["Voluntarios externos", data.externos, "#6244AD"]
            ]);

            // Crear una vista para añadir anotaciones
            var view = new google.visualization.DataView(chartData);
            view.setColumns([
              0, 1,
              {
                calc: "stringify",
                sourceColumn: 1,
                type: "string",
                role: "annotation"
              },
              2
            ]);

            // Opciones de la gráfica
            var options = {
              bar: { groupWidth: "95%" },
              legend: { position: "none" },
              // Puedes ajustar otras opciones según necesites
            };

            // Dibujar la gráfica en el div con id "GraficaReporte"
            var chart = new google.visualization.BarChart(document.getElementById("GraficaReporte"));
            chart.draw(view, options);
          });
        }
      })
      .catch(error => {
        console.error("Error en la petición:", error);
      });
  });
});

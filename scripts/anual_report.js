
document.addEventListener("DOMContentLoaded", function() {
  const form = document.getElementById("reporte-form");
  const reporteContainer = document.getElementById("reporte-container");
  const datosReporte = document.getElementById("datos-reporte");
  const graficaCanvas = document.getElementById("grafica-reporte");

  form.addEventListener("submit", function(event) {
    event.preventDefault();

    // Obtener las fechas seleccionadas
    let fechaInicio = document.getElementById("fecha-inicio").value;
    let fechaFin = document.getElementById("fecha-fin").value;
    let convocatorias = document.getElementById("convocatorias").value;
    let iglesias = document.getElementById("iglesias").value;
    let lideres = document.getElementById("lideres").value;
    let voluntarios = document.getElementById("voluntarios").value;
    let pospenados = document.getElementById("pospenados").value;
    let generarPdf = document.getElementById("generar_pdf").checked ? true : false; // true si está marcado

    // Formatear las fechas en el formato dd/mm/yyyy
    let fechaInicioFormateada = formatearFecha(fechaInicio);
    let fechaFinFormateada = formatearFecha(fechaFin);

    // Enviar petición a la API
    fetch("https://pfcolombia.co/api/report.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        fecha_inicio: fechaInicioFormateada,
        fecha_fin: fechaFinFormateada,
        convocatorias: convocatorias,
        iglesias: iglesias,
        lideres: lideres,
        voluntarios: voluntarios,
        pospenados: pospenados,
        pdf: generarPdf
      })
    })
      .then(response => {
        const contentType = response.headers.get("content-type");

        if (contentType === "application/pdf") {
          return response.blob().then(blob => {
            // Descargar PDF automáticamente
            const url = window.URL.createObjectURL(blob);

            //abrir
            window.open(url, '_blanck')
            setTimeout(() => URL.revokeObjectURL(url), 6000)

            //descargar
            //const a = document.createElement('a');
            //a.href = url;
            //a.download = `reporte_${fechaInicio}_${fechaFin}.pdf`;
            //document.body.appendChild(a);
            //a.click();
            //document.body.removeChild(a);
            //window.URL.revokeObjectURL(url);
            return { pdf: true };
          });
        } else {
          return response.json();
        }
        console.log("Content-Type:", response.headers.get("Content-Type")); // Verifica el tipo de contenido
        return response.json(); // Si es JSON, manejar como JSON
      })
      .then(data => {
        console.log(data);
        if (data.error) {
          alert("Error: " + data.error);
          return;
        }

        if (data.pdf) return; // Si ya manejamos el PDF, salir

        // Mostrar contenedor y datos
        reporteContainer.style.display = "block";
        datosReporte.innerHTML = `<p>Reporte generado del <strong>${fechaInicioFormateada}</strong> al <strong>${fechaFinFormateada}</strong></p>`;

        // Generar gráfica si es necesario
        // if (data.grafica) generarGrafica(data.grafica);
        // Generar la gráfica
        // generarGrafica(data.fechas);
      })
      .catch(error => console.error("Error al obtener los datos:", error));
  });

  function formatearFecha(fecha) {
    let partes = fecha.split("-");
    return `${partes[0]}-${partes[1]}-${partes[2]}`; // Convertir a dd/mm/yyyy
  }

  function generarGrafica(datos) {
    let ctx = graficaCanvas.getContext("2d");

    new Chart(ctx, {
      type: "line",
      data: {
        labels: datos.map(item => item.fecha),
        datasets: [
          {
            label: "Datos del Reporte",
            data: datos.map(item => item.valor),
            borderColor: "blue",
            fill: false
          }
        ]
      }
    });
  }
});

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Juegos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(to right, #4b6cb7, #182848); /* Fondo degradado más elegante */
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
            color: #fff;
        }
        .card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 30px;
            width: 600px;
            color: #fff;
        }
        .form-label {
            font-weight: bold;
            color: #e0e0e0;
        }
        .row .col-md-6 {
            padding: 10px;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            transition: all 0.3s ease-in-out;
        }
        .form-control:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.5);
            background: rgba(255, 255, 255, 0.4);
        }
        .img-preview {
            max-width: 150px;
            height: auto;
            margin-top: 15px;
            border-radius: 10px;
            border: 2px solid #6a11cb;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-primary {
            background-color: #6a11cb;
            border: none;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #3a0ca3;
            transform: scale(1.05);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            filter: grayscale(60%);
            box-shadow: none;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 100%;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-success:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #6a11cb;
            margin-top: 20px;
            border-bottom: 2px solid #6a11cb;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="card">
    <h3 class="text-center">📀 Registro de Juegos</h3>

    <form>
        <div class="mb-3">
            <label class="form-label">Buscar por Título</label>
            <div class="input-group">
                <input type="text" class="form-control" id="busqueda" placeholder="Nombre del juego" autocomplete="off" oninput="habilitarBotonBuscar()">
                <select class="form-select" id="plataforma-busqueda" style="max-width:120px;" onchange="habilitarBotonBuscar()">
                  <option value="" selected disabled>Elige plataforma</option>
                  <option value="psx">PSX</option>
                  <option value="ps2">PS2</option>
                </select>
                <span id="plataforma-alerta" class="text-warning ms-3" style="display:none;font-size:0.95em;"></span>
                <button type="button" class="btn btn-primary" id="btn-buscar" onclick="buscarDatosJuego()" disabled>Buscar</button>
            </div>
        </div>
        <div class="section-title">📋 Información del Juego</div>
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">🔢 Serial</label>
                        <input type="text" class="form-control" name="serial" id="serial" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">🎮 Título</label>
                        <input type="text" class="form-control" name="titulo" id="titulo" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">🌍 Región</label>
                        <input type="text" class="form-control" name="region" id="region" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">🕹️ Género</label>
                        <input type="text" class="form-control" name="genero" id="genero" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">🏢 Publisher</label>
                        <input type="text" class="form-control" name="publisher" id="publisher" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">📅 Fecha</label>
                        <input type="text" class="form-control" name="fecha" id="fecha" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">🎮 Plataforma</label>
                        <input type="text" class="form-control" name="plataforma" id="plataforma" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex flex-column align-items-center justify-content-center">
                <label class="form-label" style="font-size:0.95em">📸 Carátula del Juego</label>
                <img id="caratula-preview" src="" class="img-preview mb-2" style="display:none;">
                <input type="hidden" name="caratula" id="caratula">
            </div>
        </div>
        <br>
    </form>
    <div class="mt-4">
      <button type="button" class="btn btn-warning" onclick="mostrarFormularioEdicionIncompleto()">Editar datos incompletos</button>
    </div>
    <div id="form-edicion-incompleto" style="display:none; margin-top:20px;">
      <div class="section-title">✏️ Completar datos de juego</div>
      <div class="mb-2">
        <span id="info-vacios-psx" class="badge bg-secondary me-2">PSX: ...</span>
        <span id="info-vacios-ps2" class="badge bg-secondary">PS2: ...</span>
      </div>
      <div class="mb-2">
        <span id="info-vacios-detalle" class="fw-semibold" style="font-size:1.05em;"></span>
      </div>
      <div class="mb-2">
        <span id="link-ver-juego"></span>
      </div>
      <form onsubmit="guardarEdicionIncompleto(event)">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Plataforma</label>
            <select class="form-select" id="edit-plataforma-incompleto" onchange="cargarSerialesIncompletos()" required>
              <option value="" selected disabled>Elige plataforma</option>
              <option value="psx">PSX</option>
              <option value="ps2">PS2</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Serial incompletos</label>
            <select class="form-select" id="edit-serial-incompleto" onchange="cargarDatosJuegoIncompleto()" required>
              <option value="" selected disabled>Selecciona un serial</option>
            </select>
          </div>
        </div>
        <div id="campos-edicion-incompleto" class="row"></div>
        <div class="mt-3">
          <button type="submit" class="btn btn-success">Guardar cambios</button>
          <button type="button" class="btn btn-secondary ms-2" onclick="ocultarFormularioEdicionIncompleto()">Cancelar</button>
          <span id="edicion-incompleto-msg" class="ms-3"></span>
        </div>
      </form>
    </div>
</div>

<script>
function buscarDatosJuego() {
    // Limpia los campos antes de buscar
    limpiarCampos();
    let valor = document.getElementById('busqueda').value.trim();
    let plataforma = document.getElementById('plataforma-busqueda').value;
    let alerta = document.getElementById('plataforma-alerta');
    alerta.style.display = 'none';
    if (!valor) {
        alert('Introduce el título del juego.');
        return;
    }
    if (!plataforma) {
        alerta.textContent = 'Por favor, elige la plataforma antes de buscar.';
        alerta.style.display = 'inline';
        return;
    }
    let url = `buscar_datos.php?titulo=${encodeURIComponent(valor)}&plataforma=${plataforma}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del backend:', data); // Log para depuración
            if ((data.success === true || data.success === 'true') && data.titulo) {
                // Coincidencia exacta
                rellenarCampos(data);
            } else if ((data.success === 'parcial' || data.success === 'flexible') && Array.isArray(data.matches)) {
                mostrarListaResultados(data.matches);
            } else {
                alert(data.error || 'No se encontraron datos para ese valor.');
            }
        })
        .catch(error => {
            alert('Error al buscar datos: ' + error);
        });
}

function rellenarCampos(juego) {
    document.getElementById('serial').value = juego.serial || '';
    document.getElementById('titulo').value = juego.titulo || '';
    document.getElementById('region').value = (juego.region || '').replace(/^\s+|\s+$/g, '');
    document.getElementById('genero').value = (juego.genero || '').replace(/^\s+|\s+$/g, '');
    document.getElementById('publisher').value = (juego.publisher || '').replace(/^\s+|\s+$/g, '');
    document.getElementById('fecha').value = (juego.fecha || '').replace(/^\s+|\s+$/g, '');
    document.getElementById('plataforma').value = juego.plataforma || '';
    let img = document.getElementById('caratula-preview');
    // Mostrar carátula real si existe, si no, imagen por defecto de la plataforma
    if (juego.caratula_url && juego.caratula_url.startsWith('http')) {
        img.src = juego.caratula_url;
        img.style.display = '';
        document.getElementById('caratula').value = juego.caratula_url;
    } else {
        if (juego.plataforma === 'psx') {
            img.src = 'caratulas/psx.png';
            img.style.display = '';
        } else if (juego.plataforma === 'ps2') {
            img.src = 'caratulas/default.jpg';
            img.style.display = '';
        } else {
            img.src = '';
            img.style.display = 'none';
        }
        document.getElementById('caratula').value = '';
    }
    let existente = document.getElementById('resultados-lista');
    if (existente) existente.remove();
}

function mostrarListaResultados(lista) {
    // Elimina lista previa si existe
    let existente = document.getElementById('resultados-lista');
    if (existente) existente.remove();
    let divLista = document.createElement('div');
    divLista.id = 'resultados-lista';
    divLista.className = 'mt-3';
    divLista.innerHTML = '<div class="alert alert-info">Se encontraron varios juegos. Haz clic en uno para ver los detalles:</div>';
    let ul = document.createElement('ul');
    ul.className = 'list-group';
    lista.forEach(juego => {
        let li = document.createElement('li');
        li.className = 'list-group-item list-group-item-action';
        li.style.cursor = 'pointer';
        li.innerHTML = `<div style=\"display:flex;align-items:center;gap:10px;\">` +
            (juego.caratula_url ? `<img src='${juego.caratula_url}' style=\"max-width:40px;max-height:60px;border-radius:5px;\">` : '') +
            `<span>${juego.titulo} (${juego.plataforma.toUpperCase()} - ${juego.region})</span></div>`;
        li.onclick = function() {
            rellenarCampos(juego);
            divLista.remove();
        };
        ul.appendChild(li);
    });
    divLista.appendChild(ul);
    document.querySelector('.card').appendChild(divLista);
}

function limpiarCampos() {
    document.getElementById('serial').value = '';
    document.getElementById('titulo').value = '';
    document.getElementById('region').value = '';
    document.getElementById('genero').value = '';
    document.getElementById('publisher').value = '';
    document.getElementById('fecha').value = '';
    document.getElementById('plataforma').value = '';
    // Cambiar imagen por defecto según plataforma seleccionada
    let plataforma = document.getElementById('plataforma-busqueda') ? document.getElementById('plataforma-busqueda').value : '';
    let img = document.getElementById('caratula-preview');
    if (plataforma === 'psx') {
        img.src = 'caratulas/psx.png';
        img.style.display = '';
    } else if (plataforma === 'ps2') {
        img.src = 'caratulas/default.jpg';
        img.style.display = '';
    } else {
        img.src = '';
        img.style.display = 'none';
    }
    document.getElementById('caratula').value = '';
    let existente = document.getElementById('resultados-lista');
    if (existente) existente.remove();
}

function habilitarBotonBuscar() {
    let plataforma = document.getElementById('plataforma-busqueda').value;
    let nombre = document.getElementById('busqueda').value.trim();
    document.getElementById('btn-buscar').disabled = !(plataforma && nombre);
    document.getElementById('plataforma-alerta').style.display = 'none';
}

function mostrarFormularioEdicionIncompleto() {
  document.getElementById('form-edicion-incompleto').style.display = 'block';
  document.getElementById('edicion-incompleto-msg').textContent = '';
}

function ocultarFormularioEdicionIncompleto() {
  document.getElementById('form-edicion-incompleto').style.display = 'none';
  document.getElementById('campos-edicion-incompleto').innerHTML = '';
  document.getElementById('edit-serial-incompleto').innerHTML = '<option value="" selected disabled>Selecciona un serial</option>';
  document.getElementById('edit-plataforma-incompleto').value = '';
}

function cargarSerialesIncompletos() {
  let plataforma = document.getElementById('edit-plataforma-incompleto').value;
  let serialSel = document.getElementById('edit-serial-incompleto');
  serialSel.innerHTML = '<option value="" selected disabled>Cargando...</option>';
  fetch('juegos_incompletos.php?plataforma=' + encodeURIComponent(plataforma))
    .then(res => res.text())
    .then(texto => {
      if (!texto.trim()) {
        serialSel.innerHTML = '<option value="" selected disabled>No hay respuesta del servidor</option>';
        return;
      }
      let data = {};
      try { data = JSON.parse(texto); } catch(e) {
        serialSel.innerHTML = '<option value="" selected disabled>Error de formato JSON</option>';
        return;
      }
      if (data.success && data.seriales.length > 0) {
        serialSel.innerHTML = '<option value="" selected disabled>Selecciona un serial</option>' +
          data.seriales.map(s => `<option value="${s}">${s}</option>`).join('');
      } else {
        serialSel.innerHTML = '<option value="" selected disabled>No hay juegos incompletos</option>';
      }
      document.getElementById('campos-edicion-incompleto').innerHTML = '';
    });
  // Mostrar los datos vacíos de ambas plataformas
  fetch('juegos_incompletos.php?plataforma=psx')
    .then(res => res.json())
    .then(data => {
      let n = (data.success && data.seriales) ? data.seriales.length : '...';
      document.getElementById('info-vacios-psx').textContent = `PSX: ${n} vacíos`;
    });
  fetch('juegos_incompletos.php?plataforma=ps2')
    .then(res => res.json())
    .then(data => {
      let n = (data.success && data.seriales) ? data.seriales.length : '...';
      document.getElementById('info-vacios-ps2').textContent = `PS2: ${n} vacíos`;
    });
  // Estadísticas detalladas
  fetch('juegos_incompletos.php?estadisticas_vacios=1')
    .then(res => res.json())
    .then(data => {
      if (!data.success) return;
      let plat = plataforma;
      let est = data.estadisticas[plat];
      let txt = `Faltan: <span class='fw-bold'>Título:</span> <span class='text-danger fw-bold'>${est.titulo}</span>, ` +
                `<span class='fw-bold'>Región:</span> <span class='text-danger fw-bold'>${est.region}</span>, ` +
                `<span class='fw-bold'>Género:</span> <span class='text-danger fw-bold'>${est.genero}</span>, ` +
                `<span class='fw-bold'>Publisher:</span> <span class='text-danger fw-bold'>${est.publisher}</span>, ` +
                `<span class='fw-bold'>Fecha:</span> <span class='text-danger fw-bold'>${est.fecha}</span>`;
      document.getElementById('info-vacios-detalle').innerHTML = txt;
    });
}

function cargarDatosJuegoIncompleto() {
  let plataforma = document.getElementById('edit-plataforma-incompleto').value;
  let serial = document.getElementById('edit-serial-incompleto').value;
  // Limpiar link mientras carga
  document.getElementById('link-ver-juego').innerHTML = '';
  let camposDiv = document.getElementById('campos-edicion-incompleto');
  if (!serial) return;
  fetch('obtener_juego.php?serial=' + encodeURIComponent(serial))
    .then(res => res.json())
    .then(data => {
      if (!data.success || !data.juego) {
        document.getElementById('link-ver-juego').innerHTML = '';
        return;
      }
      let juego = data.juego;
      // Mostrar link solo si hay url_ficha
      if (juego.url_ficha && juego.url_ficha.trim() !== '') {
        document.getElementById('link-ver-juego').innerHTML = `<a href='${juego.url_ficha}' target='_blank' class='btn btn-link p-0 ms-2'><i class='bi bi-link-45deg'></i> Ver ficha web</a>`;
      } else {
        document.getElementById('link-ver-juego').innerHTML = '';
      }
      let campos = [
        {id:'titulo', label:'Título'},
        {id:'region', label:'Región'},
        {id:'genero', label:'Género'},
        {id:'publisher', label:'Publisher'},
        {id:'fecha', label:'Fecha'}
      ];
      camposDiv.innerHTML = `<div class='row g-2'>` + campos.map(c =>
        `<div class='col-md-4 d-flex align-items-end'>
          <div class='w-100'>
            <label class='form-label'>${c.label}</label>
            <input type='text' class='form-control campo-mayus' id='edit-${c.id}-incompleto' value='${juego[c.id] ? juego[c.id] : ''}' placeholder='Completa ${c.label.toLowerCase()}' autocomplete='off'>
          </div>
        </div>`
      ).join('') + `</div>`;
    });
}

function guardarEdicionIncompleto(event) {
  event.preventDefault();
  let plataforma = document.getElementById('edit-plataforma-incompleto').value;
  let serial = document.getElementById('edit-serial-incompleto').value;
  let campos = ['titulo','region','genero','publisher','fecha'];
  let datos = {serial, plataforma};
  campos.forEach(c => {
    let valor = document.getElementById('edit-' + c + '-incompleto').value;
    datos[c] = valor;
  });
  fetch('actualizar_juego.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(datos)
  })
  .then(res => res.json())
  .then(data => {
    let msg = document.getElementById('edicion-incompleto-msg');
    if (data.success) {
      msg.innerHTML = '<span class="text-success">Datos guardados correctamente.</span>';
      // Refrescar campos tras guardar
      setTimeout(() => {
        cargarSerialesIncompletos();
        document.getElementById('edit-serial-incompleto').value = '';
        document.getElementById('campos-edicion-incompleto').innerHTML = '';
        document.getElementById('link-ver-juego').innerHTML = '';
        msg.innerHTML = '';
      }, 1200);
    } else {
      msg.innerHTML = '<span class="text-danger">Error: ' + (data.error || 'No se pudo guardar') + '</span>';
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
    habilitarBotonBuscar(); // Por si hay valores pre-cargados
});

document.addEventListener('input', function(e) {
  if (e.target.classList && e.target.classList.contains('campo-mayus')) {
    let start = e.target.selectionStart;
    let end = e.target.selectionEnd;
    // Convertir a mayúsculas conservando acentos/caracteres especiales
    let upper = e.target.value.toLocaleUpperCase('es-ES');
    if (e.target.value !== upper) {
      e.target.value = upper;
      e.target.setSelectionRange(start, end);
    }
  }
});

document.addEventListener('DOMContentLoaded', function() {
  const img = document.getElementById('caratula-preview');
  if (img) {
    img.style.cursor = 'pointer';
    img.title = 'Haz clic para guardar la imagen en la base de datos';
    img.addEventListener('click', function() {
      let serial = document.getElementById('edit-serial-incompleto').value;
      let url = img.src;
      if (!serial || !url) return;
      fetch('utilidad/guardar_imagen.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({serial: serial, url: url})
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Imagen guardada correctamente en la base de datos');
        } else {
          alert('Error al guardar la imagen: ' + (data.error || 'Desconocido'));
        }
      });
    });
  }
});

// Cambiar imagen por defecto al cambiar plataforma
if (document.getElementById('plataforma-busqueda')) {
  document.getElementById('plataforma-busqueda').addEventListener('change', function() {
    let plataforma = this.value;
    let img = document.getElementById('caratula-preview');
    if (plataforma === 'psx') {
      img.src = 'caratulas/psx.png';
      img.style.display = '';
    } else if (plataforma === 'ps2') {
      img.src = 'caratulas/default.jpg';
      img.style.display = '';
    } else {
      img.src = '';
      img.style.display = 'none';
    }
  });
}
</script>

</body>
</html>
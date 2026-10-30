const API = "api.php";
const form = document.getElementById("formEstudiante");
const mensaje = document.getElementById("mensaje");
const tbody = document.getElementById("tablaEstudiantes");
const total = document.getElementById("totalEstudiantes");
let estudianteSeleccionado = null;

const campos = ["cedula", "nombre1", "nombre2", "apellido1", "apellido2", "telefono", "direccion"];

function mostrarMensaje(texto, tipo = "ok") {
  mensaje.textContent = texto;
  mensaje.className = `mensaje ${tipo}`;
}
function obtenerDatos() {
  const datos = {};
  campos.forEach(id => datos[id] = document.getElementById(id).value.trim());
  return datos;
}
function llenarFormulario(est) {
  campos.forEach(id => document.getElementById(id).value = est[id] ?? "");
  estudianteSeleccionado = est.cedula;
}
function limpiarFormulario() {
  form.reset();
  estudianteSeleccionado = null;
}
async function solicitar(accion, datos = {}) {
  const respuesta = await fetch(API, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ accion, ...datos })
  });
  const resultado = await respuesta.json();
  if (!respuesta.ok || !resultado.ok) throw new Error(resultado.mensaje || "Ocurrió un error.");
  return resultado;
}
function validarRequeridos(datos) {
  if (!datos.cedula || !datos.nombre1 || !datos.apellido1 || !datos.telefono || !datos.direccion) {
    mostrarMensaje("Completa cédula, primer nombre, primer apellido, teléfono y dirección.", "error");
    return false;
  }
  return true;
}
function renderizarTabla(estudiantes) {
  tbody.replaceChildren();
  total.textContent = estudiantes.length;
  if (!estudiantes.length) {
    const fila = document.createElement("tr");
    const celda = document.createElement("td");
    celda.colSpan = 7; celda.className = "vacio"; celda.textContent = "No hay estudiantes registrados.";
    fila.appendChild(celda); tbody.appendChild(fila); return;
  }
  estudiantes.forEach(est => {
    const fila = document.createElement("tr");
    fila.dataset.cedula = est.cedula;
    campos.forEach(id => {
      const celda = document.createElement("td");
      celda.textContent = est[id] ?? "";
      fila.appendChild(celda);
    });
    fila.addEventListener("click", () => {
      llenarFormulario(est);
      mostrarMensaje(`Estudiante ${est.nombre1} ${est.apellido1} seleccionado.`);
    });
    tbody.appendChild(fila);
  });
}
async function listar() {
  const r = await solicitar("listar");
  renderizarTabla(r.estudiantes);
  mostrarMensaje(`Se cargaron ${r.estudiantes.length} estudiante(s).`);
}
async function ejecutar(fn) {
  try { await fn(); }
  catch (error) { mostrarMensaje(error.message || "No se pudo completar la operación.", "error"); }
}

document.getElementById("btnGuardar").addEventListener("click", () => ejecutar(async () => {
  const datos = obtenerDatos();
  if (!validarRequeridos(datos)) return;
  const r = await solicitar("guardar", datos);
  mostrarMensaje(r.mensaje);
  limpiarFormulario();
  await listarSilencioso();
}));

document.getElementById("btnConsultar").addEventListener("click", () => ejecutar(async () => {
  const cedula = document.getElementById("cedula").value.trim() || prompt("Escribe la cédula que deseas consultar:");
  if (!cedula) return;
  const r = await solicitar("consultar", { cedula });
  llenarFormulario(r.estudiante);
  mostrarMensaje("Estudiante encontrado. Puedes modificar sus datos y pulsar Editar.");
}));

document.getElementById("btnEditar").addEventListener("click", () => ejecutar(async () => {
  const datos = obtenerDatos();
  if (!datos.cedula) { mostrarMensaje("Escribe o consulta primero la cédula del estudiante.", "error"); return; }
  if (!validarRequeridos(datos)) return;
  const r = await solicitar("editar", { ...datos, cedulaOriginal: estudianteSeleccionado || datos.cedula });
  mostrarMensaje(r.mensaje);
  estudianteSeleccionado = datos.cedula;
  await listarSilencioso();
}));

document.getElementById("btnEliminar").addEventListener("click", () => ejecutar(async () => {
  const cedula = document.getElementById("cedula").value.trim() || prompt("Escribe la cédula del estudiante que deseas eliminar:");
  if (!cedula) return;
  if (!confirm(`¿Seguro que deseas eliminar al estudiante con cédula ${cedula}?`)) return;
  const r = await solicitar("eliminar", { cedula });
  mostrarMensaje(r.mensaje);
  limpiarFormulario();
  await listarSilencioso();
}));

document.getElementById("btnListar").addEventListener("click", () => ejecutar(listar));
document.getElementById("btnLimpiar").addEventListener("click", () => { limpiarFormulario(); mostrarMensaje("Formulario limpiado."); });
document.getElementById("btnExcel").addEventListener("click", () => { window.location.href = "exportar.php"; });

async function listarSilencioso() {
  try {
    const r = await solicitar("listar");
    renderizarTabla(r.estudiantes);
  } catch (e) {
    mostrarMensaje(e.message || "No se pudo actualizar la tabla.", "error");
  }
}

document.getElementById("filtroTabla").addEventListener("input", event => {
  const filtro = event.target.value.toLocaleLowerCase("es");
  [...tbody.querySelectorAll("tr")].forEach(fila => {
    fila.style.display = fila.textContent.toLocaleLowerCase("es").includes(filtro) ? "" : "none";
  });
});

// Carga inicial de registros desde la base de datos.
ejecutar(listar);

/*----------  Funcion enviar formularios ajax  ----------*/
function enviar_formulario_ajax(e){
    e.preventDefault(); // Prevenir la acción por defecto del formulario

    let data = new FormData(this); // Crear un objeto FormData con los datos del formulario
    let method = this.getAttribute("method"); // Obtener el método del formulario (GET, POST, etc.)
    let action = this.getAttribute("action"); // Obtener la URL de acción del formulario
    let tipo = this.getAttribute("data-form"); // Obtener el tipo de formulario (save, delete, etc.)

    let encabezados = new Headers(); // Crear un objeto Headers

    let config = { 
        method: method,
        headers: encabezados,
        mode: 'cors',
        cache: 'no-cache',
        body: data // Incluir los datos del formulario en el cuerpo de la solicitud
    };

    // Si el formulario es de tipo "delete"
    if(tipo === "delete") {
        // Mostrar una alerta de confirmación usando SweetAlert2
        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.value) {
                // Si el usuario confirma, enviar la solicitud fetch
                fetch(action, config)
                    .then(respuesta => respuesta.json()) // Convertir la respuesta a JSON
                    .then(respuesta =>{
                        return alertas_ajax(respuesta); // Mostrar la alerta correspondiente
                    }); 
            }
        });
    } else {
        // Para otros tipos de formulario, enviar la solicitud fetch directamente
        fetch(action, config)
            .then(respuesta => respuesta.json()) // Convertir la respuesta a JSON
            .then(respuesta =>{
                return alertas_ajax(respuesta); // Mostrar la alerta correspondiente
            });
    }
}

/*----------  Funcion listar formularios  ----------*/
const formularios_ajax = document.querySelectorAll(".FormularioAjax"); // Seleccionar todos los formularios con la clase "FormularioAjax"
formularios_ajax.forEach(formularios => {
    formularios.addEventListener("submit", enviar_formulario_ajax); // Añadir el evento "submit" a cada formulario
});

/*----------  Funcion mostrar alertas  ----------*/
function alertas_ajax(alerta) {
    console.log("Recibida la alerta:", alerta); // Mostrar la alerta recibida en la consola

    if (alerta.Alerta === "simple") {
        // Mostrar una alerta simple usando SweetAlert2
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        });
    } else if (alerta.Alerta === "recargar") {
        // Recargar la página
        location.reload();
    } else if (alerta.Alerta === "limpiar") {
        // Mostrar una alerta y limpiar el formulario si se confirma
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value) {
                document.querySelector("form[data-form='save']").reset(); // Resetear el formulario
            }
        });
    } else if (alerta.Alerta === "redireccionar") {
        console.log("Procediendo a redirigir a:", alerta.URL); // Confirmar la URL antes de la redirección
        window.location.href = alerta.URL; // Ejecutar la redirección
    }
}
<script>
    // Selecciona el botón de salir del sistema
    let btn_salir = document.querySelector('.btn-exit-system');

    // Añade un evento al botón de salir que se activa cuando se hace clic en él
    btn_salir.addEventListener('click', function(e) {
        e.preventDefault(); // Previene el comportamiento por defecto del botón

        // Muestra una alerta de confirmación utilizando SweetAlert2
        Swal.fire({
            title: '¿Quieres salir del sistema?', // Título de la alerta
            text: "La sesión actual se cerrará y saldrás del sistema", // Texto de la alerta
            type: 'question', // Tipo de alerta (pregunta)
            showCancelButton: true, // Muestra el botón de cancelar
            confirmButtonColor: '#3085d6', // Color del botón de confirmar
            cancelButtonColor: '#d33', // Color del botón de cancelar
            confirmButtonText: 'Si, salir', // Texto del botón de confirmar
            cancelButtonText: 'No, cancelar' // Texto del botón de cancelar
        }).then((result) => {
            if (result.value) { // Si el usuario confirma la acción

                // Define la URL para la petición AJAX
                let url = '<?php echo SERVERURL; ?>ajax/loginAjax.php';

                // Obtiene el token y el usuario encriptados
                let token = '<?php echo $lc->encryption($_SESSION['token_xcoring']); ?>';
                let usuario = '<?php echo $lc->encryption($_SESSION['email_xcoring']); ?>';

                // Crea un objeto FormData y añade los datos del token y el usuario
                let datos = new FormData();
                datos.append("token", token);
                datos.append("usuario", usuario);

                // Realiza una petición fetch con método POST y los datos
                fetch(url, {
                    method: 'POST',
                    body: datos
                })
                .then(respuesta => respuesta.json()) // Convierte la respuesta a JSON
                .then(respuesta => {
                    return alertas_ajax(respuesta); // Muestra la alerta AJAX con la respuesta
                });			
            }
        });
    });
</script>
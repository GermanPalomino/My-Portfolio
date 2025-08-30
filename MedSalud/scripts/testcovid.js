// damos al evento submit del elemento formulario la función de validación
document.getElementById("formulario").addEventListener("submit", function(event){
    let hasError = false;
    // obtenemos todos los input radio que esten chequeados
    // si no hay ninguno lanzamos alerta
    if(!document.querySelector('input[name="fiebre"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="tos"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="cansancio"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="gusto"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="olfato"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        } 
    if(!document.querySelector('input[name="smh"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="ss"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    // si hay algún error no efectuamos la acción submit del formulario    
    if(hasError) event.preventDefault();   
});
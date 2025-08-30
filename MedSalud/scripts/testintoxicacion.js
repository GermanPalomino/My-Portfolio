document.getElementById("formulario").addEventListener("submit", function(event){
    let hasError = false;
    if(!document.querySelector('input[name="diarrea"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="vomito"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="dolorabdomen"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="fiebre"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="dolordecabeza"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="paralisis"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="debilidad"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(hasError) event.preventDefault();   
});
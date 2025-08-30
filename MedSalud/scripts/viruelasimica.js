document.getElementById("formulario").addEventListener("submit", function(event){
    let hasError = false;
    if(!document.querySelector('input[name="fiebre"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }

    if(!document.querySelector('input[name="dolordecabeza"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="doloresmusuclares"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="faltadeenergia"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="erupciones"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(hasError) event.preventDefault();
});
   

document.getElementById("formulario").addEventListener("submit", function(event){
    let hasError = false;
    if(!document.querySelector('input[name="fiebre"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="dolormuscular"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="escalofrios"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="mareos"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="dolordecabeza"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }  
    if(!document.querySelector('input[name="cansacio"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="nauseas"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="tos"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="dolordegarganta"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        }
    if(!document.querySelector('input[name="secrecionnasal"]:checked')){
        alert('¡Ups! Te falto una pregunta');
        hasError = true;
        } 
    if(hasError) event.preventDefault();
});
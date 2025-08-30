// Donar Sangre – validación mínima + confirmación
(function () {
  const form   = document.getElementById('donateForm');
  const resume = document.getElementById('resumeList');
  const dialog = document.getElementById('confirmDialog');
  const dateEl = document.getElementById('date');

  // Fecha mínima = hoy
  const today = new Date().toISOString().split('T')[0];
  dateEl.min = today;

  // Helpers
  const showErr = (id, msg) => {
    const el = document.getElementById(id);
    if (el) el.textContent = msg || '';
  };
  const clearErrs = () => {
    ['err-name','err-last','err-age','err-id','err-email','err-date'].forEach(id => showErr(id,''));
  };

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    clearErrs();

    const name     = document.getElementById('name');
    const last     = document.getElementById('lastName');
    const age      = document.getElementById('age');
    const idNumber = document.getElementById('idNumber');
    const email    = document.getElementById('email');
    const date     = dateEl;

    let valid = true;
    if (!name.checkValidity())     { showErr('err-name',  'Nombre inválido.'); valid = false; }
    if (!last.checkValidity())     { showErr('err-last',  'Apellido inválido.'); valid = false; }
    if (!age.checkValidity())      { showErr('err-age',   'Edad entre 18 y 65.'); valid = false; }
    if (!idNumber.checkValidity()) { showErr('err-id',    'Solo números (6–12 dígitos).'); valid = false; }
    if (!email.checkValidity())    { showErr('err-email', 'Correo inválido.'); valid = false; }
    if (!date.checkValidity())     { showErr('err-date',  'Selecciona una fecha válida.'); valid = false; }

    if (!valid) return;

    // Llenar resumen
    resume.innerHTML = `
      <li><strong>Nombre:</strong> ${name.value} ${last.value}</li>
      <li><strong>Edad:</strong> ${age.value}</li>
      <li><strong>Documento:</strong> ${idNumber.value}</li>
      <li><strong>Email:</strong> ${email.value}</li>
      <li><strong>Fecha:</strong> ${date.value}</li>
    `;

    // Confirmación accesible
    if (typeof dialog.showModal === 'function') {
      dialog.showModal();
      const onClose = () => {
        dialog.removeEventListener('close', onClose);
        if (dialog.returnValue === 'confirm') form.submit();
      };
      dialog.addEventListener('close', onClose);
    } else {
      if (confirm('¿Confirmar el envío de los datos para donación?')) form.submit();
    }
  });
})();
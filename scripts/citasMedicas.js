(function () {
  const form  = document.getElementById('form');
  const btn   = document.getElementById('enviar');
  const terms = document.getElementById('condiciones');
  const phone = document.getElementById('phone');
  const fecha = document.getElementById('fecha');
  const dialog = document.getElementById('confirmDialog');
  const resume = document.getElementById('resumeList');

  const toggleSubmit = () => btn.disabled = !terms.checked;
  terms.addEventListener('change', toggleSubmit);
  toggleSubmit();

  const today = new Date().toISOString().split('T')[0];
  fecha.min = today;

  phone.addEventListener('input', () => {
    let d = phone.value.replace(/\D/g, '').slice(0, 10);
    let f = d;
    if (d.length > 6) f = `${d.slice(0,3)}-${d.slice(3,6)}-${d.slice(6)}`;
    else if (d.length > 3) f = `${d.slice(0,3)}-${d.slice(3)}`;
    phone.value = f;
  });

  const messages = {
    name: 'Ingresa un nombre válido (solo letras y espacios).',
    age: 'Ingresa una edad entre 1 y 99.',
    phone: 'Revisa el teléfono (formato 300-000-0000).',
    mail: 'Ingresa un correo válido.',
    fecha: 'Selecciona una fecha igual o posterior a hoy.',
    department: 'Selecciona un departamento.',
    symptom: 'Selecciona un síntoma.',
    terms: 'Debes aceptar los términos.'
  };
  const setError = (input, id, msg) => {
    const el = document.getElementById(id);
    input.setAttribute('aria-invalid', 'true');
    if (el) el.textContent = msg;
  };
  const clearError = (input, id) => {
    const el = document.getElementById(id);
    input.removeAttribute('aria-invalid');
    if (el) el.textContent = '';
  };
  ['name','age','phone','mail','fecha','department','symptom'].forEach(id=>{
    const input = document.getElementById(id === 'mail' ? 'mail' : id);
    const errId = 'err-' + (id === 'mail' ? 'mail' : id);
    if (!input) return;
    input.addEventListener('input', ()=> clearError(input, errId));
    input.addEventListener('change', ()=> clearError(input, errId));
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    let valid = form.checkValidity();
    const gender = form.querySelector('input[name="gender"]:checked');

    const map = [
      ['name','err-name', messages.name],
      ['age','err-age', messages.age],
      ['phone','err-phone', messages.phone],
      ['mail','err-mail', messages.mail],
      ['fecha','err-fecha', messages.fecha],
      ['department','err-dep', messages.department],
      ['symptom','err-symp', messages.symptom]
    ];
    map.forEach(([id, err, msg]) => {
      const input = document.getElementById(id === 'mail' ? 'mail' : id);
      if (!input) return;
      if (!input.checkValidity()) setError(input, err, msg);
    });

    if (!gender) {
      form.querySelector('.radio-group').classList.add('invalid');
      valid = false;
    } else {
      form.querySelector('.radio-group').classList.remove('invalid');
    }
    if (!terms.checked) {
      alert(messages.terms);
      valid = false;
    }

    if (!valid) return;

    resume.innerHTML = `
      <li><strong>Paciente:</strong> ${document.getElementById('name').value}</li>
      <li><strong>Edad:</strong> ${document.getElementById('age').value}</li>
      <li><strong>Teléfono:</strong> ${document.getElementById('phone').value}</li>
      <li><strong>Email:</strong> ${document.getElementById('mail').value}</li>
      <li><strong>Fecha:</strong> ${document.getElementById('fecha').value}</li>
      <li><strong>Género:</strong> ${gender.value}</li>
      <li><strong>Departamento:</strong> ${document.getElementById('department').value}</li>
      <li><strong>Síntoma:</strong> ${document.getElementById('symptom').value}</li>
    `;

    if (typeof dialog.showModal === 'function') {
      dialog.showModal();
      const onClose = () => {
        dialog.removeEventListener('close', onClose);
        if (dialog.returnValue === 'confirm') submitWithLoading();
      };
      dialog.addEventListener('close', onClose);
    } else {
      if (confirm('¿Confirmar envío de la cita?')) submitWithLoading();
    }
  });

  function submitWithLoading(){
    btn.classList.add('loading');
    btn.textContent = 'Enviando…';
    btn.disabled = true;
    form.submit();
  }

  ['name','phone','mail'].forEach(id=>{
    const el = document.getElementById(id);
    if (!el) return;
    el.value = localStorage.getItem('cm_' + id) || el.value;
    el.addEventListener('change', ()=> localStorage.setItem('cm_' + id, el.value));
  });
})();
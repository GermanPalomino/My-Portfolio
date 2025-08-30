// Historial médico – validación, dependencias y confirmación
(function () {
  const form   = document.getElementById('historyForm');
  const bdate  = document.getElementById('birthdate');
  const cell   = document.getElementById('cellphone');

  const allergyRadios = form.querySelectorAll('input[name="hasAllergy"]');
  const allergySel    = document.getElementById('allergyType');

  const medsRadios = form.querySelectorAll('input[name="takesMeds"]');
  const medsSel    = document.getElementById('medType');

  const dialog = document.getElementById('confirmDialog');
  const resume = document.getElementById('resumeList');

  // Fecha de nacimiento: no permitir futuras
  const today = new Date().toISOString().split('T')[0];
  bdate.max = today;

  // Formateo de celular 300-000-0000
  cell.addEventListener('input', () => {
    let d = cell.value.replace(/\D/g, '').slice(0, 10);
    let f = d;
    if (d.length > 6) f = `${d.slice(0,3)}-${d.slice(3,6)}-${d.slice(6)}`;
    else if (d.length > 3) f = `${d.slice(0,3)}-${d.slice(3)}`;
    cell.value = f;
  });

  // Dependencias: alergias / medicamentos
  const toggleDep = (checkedYes, selectEl) => {
    const enable = checkedYes?.value === 'Si' && checkedYes?.checked;
    selectEl.disabled = !enable;
    if (!enable) selectEl.value = '';
  };
  allergyRadios.forEach(r => r.addEventListener('change', () => {
    toggleDep([...allergyRadios].find(x=>x.checked), allergySel);
  }));
  medsRadios.forEach(r => r.addEventListener('change', () => {
    toggleDep([...medsRadios].find(x=>x.checked), medsSel);
  }));

  // Errores personalizados rápidos
  const show = (id, msg) => { const el = document.getElementById(id); if (el) el.textContent = msg || ''; };
  const clearAll = () => ['err-name','err-blood','err-age','err-bdate','err-city','err-id','err-cell','err-address','err-allergy','err-height','err-weight','err-meds']
    .forEach(id => show(id,''));

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    clearAll();

    // Validación nativa
    let valid = form.checkValidity();

    // Si marcó Sí, exigir selección
    const hasAllergyYes = form.querySelector('input[name="hasAllergy"][value="Si"]').checked;
    if (hasAllergyYes && !allergySel.value) { show('err-allergy', 'Selecciona el tipo de alergia.'); valid = false; }

    const takesMedsYes = form.querySelector('input[name="takesMeds"][value="Si"]').checked;
    if (takesMedsYes && !medsSel.value) { show('err-meds', 'Selecciona el medicamento.'); valid = false; }

    // Mostrar mensajes cortos si falla algún campo clave
    const map = [
      ['fullName','err-name','Nombre inválido.'],
      ['bloodType','err-blood','Tipo de sangre inválido (A, B, AB u O con +/-).'],
      ['age','err-age','Edad entre 1 y 99.'],
      ['birthdate','err-bdate','Fecha inválida.'],
      ['city','err-city','Selecciona una ciudad.'],
      ['idNumber','err-id','6–12 dígitos.'],
      ['cellphone','err-cell','Formato 300-000-0000.'],
      ['address','err-address','Completa la dirección.'],
      ['height','err-height','Indica tu estatura en cm.'],
      ['weight','err-weight','Indica tu peso en kg.'],
    ];
    map.forEach(([id, err, msg])=>{
      const el = document.getElementById(id);
      if (el && !el.checkValidity()) show(err, msg);
    });

    if (!valid) return;

    // Resumen
    const sex = form.querySelector('input[name="sex"]:checked')?.value || '';
    const idType = form.querySelector('input[name="idType"]:checked')?.value || '';
    const dis = form.querySelector('input[name="hasDisability"]:checked')?.value || '';
    const all = form.querySelector('input[name="hasAllergy"]:checked')?.value || '';
    const med = form.querySelector('input[name="takesMeds"]:checked')?.value || '';
    const diseases = [...document.getElementById('diseases').selectedOptions].map(o=>o.value).join(', ') || 'N/A';

    resume.innerHTML = `
      <li><strong>Paciente:</strong> ${document.getElementById('fullName').value}</li>
      <li><strong>Sangre:</strong> ${document.getElementById('bloodType').value}</li>
      <li><strong>Edad:</strong> ${document.getElementById('age').value}</li>
      <li><strong>Nacimiento:</strong> ${document.getElementById('birthdate').value} — ${document.getElementById('city').value}</li>
      <li><strong>Sexo:</strong> ${sex}</li>
      <li><strong>ID:</strong> ${idType} ${document.getElementById('idNumber').value}</li>
      <li><strong>Celular:</strong> ${document.getElementById('cellphone').value}</li>
      <li><strong>Domicilio:</strong> ${document.getElementById('address').value}</li>
      <li><strong>Alergias:</strong> ${all}${all==='Si' ? ' — ' + (allergySel.value||'N/A') : ''}</li>
      <li><strong>Peso/Estatura:</strong> ${document.getElementById('weight').value} kg / ${document.getElementById('height').value} cm</li>
      <li><strong>Medicamentos:</strong> ${med}${med==='Si' ? ' — ' + (medsSel.value||'N/A') : ''}</li>
      <li><strong>Enfermedades:</strong> ${diseases}</li>
      <li><strong>Discapacidad:</strong> ${dis}</li>
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
      if (confirm('¿Confirmar envío del historial?')) form.submit();
    }
  });
})();
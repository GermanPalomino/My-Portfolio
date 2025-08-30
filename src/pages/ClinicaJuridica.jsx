import React from "react";

export default function ClinicaJuridica() {
  return (
    <>
      {/* Hero */}
      <section
        className="bg-cover bg-center text-white text-center py-24"
        style={{ backgroundImage: "url('https://source.unsplash.com/1600x900/?justice')" }}
      >
        <div className="bg-black bg-opacity-50 p-6 inline-block rounded max-w-3xl mx-auto">
          <h1 className="text-4xl font-bold uppercase leading-relaxed mb-4">
            Clínica Jurídica Template
          </h1>
          <p className="text-lg leading-relaxed">
            Ofrecemos asesoría legal gratuita y acompañamiento en procesos
            judiciales y administrativos para garantizar el acceso a la justicia
            de personas y comunidades vulneradas. Nuestra clínica jurídica
            diseña herramientas legales innovadoras y promueve litigio
            estratégico para la defensa de derechos fundamentales.
          </p>
        </div>
      </section>

      {/* CTA final */}
      <section className="bg-[var(--amarillo-foidhd)] py-10 text-center text-black">
        <h3 className="text-xl font-bold mb-4">
          ¿Necesitas asesoría jurídica o apoyo psicosocial?
        </h3>
        <p className="mb-6">
          Contacta con la Clínica Jurídica FOIDHD o el área de Salud Mental para
          recibir acompañamiento profesional y gratuito.
        </p>
        <a
          href="#"
          className="bg-[var(--azul-foidhd)] text-white px-6 py-2 rounded hover:bg-blue-900 inline-block"
        >
          Contáctanos
        </a>
      </section>
    </>
  );
}
import React from "react";
import { Link } from "react-router-dom";

export default function SaludMental() {
  return (
    <>
      {/* Hero */}
      <section
        className="bg-cover bg-center text-white text-center py-24"
        style={{
          backgroundImage:
            "url('https://source.unsplash.com/1600x900/?mental-health,woman')",
        }}
      >
        <div className="bg-black bg-opacity-50 p-6 inline-block rounded max-w-3xl mx-auto">
          <h1 className="text-4xl font-bold uppercase leading-relaxed mb-4">
            Salud Mental y VBG
          </h1>
          <p className="text-lg leading-relaxed">
            Brindamos apoyo psicosocial y acompañamiento especializado a
            víctimas de violencia basada en género, promoviendo procesos de
            reparación, autocuidado y fortalecimiento emocional. Trabajamos para
            visibilizar y erradicar la violencia estructural que afecta la salud
            mental de las personas en contextos de vulnerabilidad.
          </p>
        </div>
      </section>

      {/* CTA final */}
      <section className="bg-[var(--amarillo-foidhd)] py-10 text-center text-black">
        <h3 className="text-xl font-bold mb-4">
          ¿Necesitas asesoría jurídica o apoyo psicosocial?
        </h3>
        <p className="mb-6">
          Contacta con la Clínica Jurídica FOIDHD o el área de Salud Mental
          para recibir acompañamiento profesional y gratuito.
        </p>
        <Link
          to="/clinica-juridica"
          className="inline-block bg-[var(--azul-foidhd)] text-white px-6 py-2 rounded hover:bg-blue-900"
        >
          Contáctanos
        </Link>
      </section>
    </>
  );
}
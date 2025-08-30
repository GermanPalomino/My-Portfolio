import React from "react";
import { Link } from "react-router-dom";

export default function Datos() {
  return (
    <>
      {/* Hero */}
      <section
        className="bg-cover bg-center text-white text-center py-24"
        style={{ backgroundImage: "url('https://source.unsplash.com/1600x900/?data,technology')" }}
      >
        <div className="bg-black bg-opacity-50 p-6 inline-block rounded max-w-3xl mx-auto">
          <h1 className="text-4xl font-bold uppercase leading-relaxed mb-4">
            Datos al Servicio de los Derechos Humanos
          </h1>
          <p className="text-lg leading-relaxed">
            Desarrollamos un sistema robusto de recopilación, análisis y
            visualización de datos que permite monitorear violaciones a los
            derechos humanos en tiempo real. Esta plataforma facilita el acceso
            a información confiable para la ciudadanía, organizaciones sociales,
            académicos y tomadores de decisiones, fortaleciendo la transparencia
            y la incidencia basada en evidencia.
          </p>
        </div>
      </section>

      {/* Proyecto de visualización */}
      <section className="max-w-6xl mx-auto py-16 px-6">
        <h2 className="text-2xl font-bold text-center text-[var(--azul-foidhd)] mb-8">
          Ejemplo de Visualización de Datos
        </h2>

        <div className="bg-white rounded shadow-md overflow-hidden">
          <img
            src="https://biist.pro/wp-content/uploads/2020/06/Captura-informe-curso-ES.png"
            alt="Ejemplo Power BI - FOIDHD"
            className="w-full object-cover"
          />
          <div className="p-6">
            <h3 className="text-xl font-semibold text-[var(--azul-foidhd)] mb-2">
              Informe de Monitoreo en Derechos Humanos
            </h3>
            <p className="text-gray-700 text-sm">
              Esta visualización muestra un informe de ejemplo basado en datos
              estructurados sobre vulneraciones a los derechos humanos. Permite
              explorar la información de forma dinámica y fortalecer la toma de
              decisiones con evidencia clara y accesible.
            </p>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="bg-[var(--amarillo-foidhd)] py-10 text-center text-black">
        <h3 className="text-xl font-bold mb-4">
          ¿Necesitas asesoría jurídica o apoyo psicosocial?
        </h3>
        <p className="mb-6">
          Contacta con la Clínica Jurídica FOIDHD o el área de Salud Mental para
          recibir acompañamiento profesional y gratuito.
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
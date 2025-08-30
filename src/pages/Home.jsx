import React from "react";
import { Link } from "react-router-dom";

export default function Home() {
  return (
    <>
      {/* Hero */}
      <section
        className="bg-cover bg-center text-white text-center py-24"
        style={{ backgroundImage: "url('https://source.unsplash.com/1600x900/?human-rights')" }}
      >
        <div className="bg-black bg-opacity-50 p-6 inline-block rounded max-w-3xl mx-auto">
          <h1 className="text-4xl font-bold uppercase leading-relaxed mb-4">
            Template
          </h1>
          <p className="text-lg leading-relaxed">
            Template es una organización dedicada a la defensa, promoción y
            protección de los derechos humanos, comprometida con la
            transformación social para garantizar justicia, equidad y dignidad a
            las comunidades históricamente vulneradas. Creemos en el poder de la
            información, la asesoría jurídica, el cuidado integral y el
            monitoreo constante para construir una sociedad más justa y
            respetuosa de los derechos fundamentales.
          </p>
        </div>
      </section>

      {/* Herramientas */}
      <section className="bg-[var(--gris-claro)] py-12">
        <div className="max-w-6xl mx-auto px-4">
          <h2 className="text-2xl font-bold mb-8 text-center text-[var(--azul-foidhd)]">
            Herramientas y Recursos
          </h2>

          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div className="bg-[var(--azul-foidhd)] text-white p-4 rounded shadow text-center">
              <span className="material-icons text-4xl mb-2">gavel</span>
              <h3 className="font-semibold mb-2">Directorio de Servicios</h3>
              <p className="text-sm">Encuentra abogados, psicólogos y redes de apoyo.</p>
            </div>

            <div className="bg-[var(--amarillo-foidhd)] text-black p-4 rounded shadow text-center">
              <span className="material-icons text-4xl mb-2">menu_book</span>
              <h3 className="font-semibold mb-2">Guías y Manuales</h3>
              <p className="text-sm">Materiales para empoderarte en tus derechos.</p>
            </div>

            <div className="bg-white text-gray-800 p-4 rounded shadow border text-center">
              <span className="material-icons text-4xl mb-2 text-[var(--azul-foidhd)]">bar_chart</span>
              <h3 className="font-semibold mb-2">Informes y Monitoreo</h3>
              <p className="text-sm">Datos y reportes sobre vulneraciones y avances.</p>
            </div>

            <div className="bg-gray-200 p-4 rounded shadow text-center">
              <span className="material-icons text-4xl mb-2 text-[var(--azul-foidhd)]">report</span>
              <h3 className="font-semibold mb-2">Plataforma de Denuncias</h3>
              <p className="text-sm">Reporta violaciones de manera segura.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Noticias */}
      <section className="py-12 max-w-6xl mx-auto px-4">
        <h2 className="text-2xl font-bold mb-8 text-center text-[var(--azul-foidhd)]">
          Noticias y Eventos
        </h2>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="shadow rounded overflow-hidden">
            <img
              src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-fuw5a8h7KZzPubPmlMH-8TCOzSR4ap46dQ&s"
              alt="Taller sobre violencia de género"
              className="w-full h-48 object-cover"
            />
            <div className="p-4">
              <p className="text-sm text-gray-500">Abril 2024</p>
              <h3 className="font-semibold text-[var(--azul-foidhd)]">Taller sobre violencia de género</h3>
            </div>
          </div>

          <div className="shadow rounded overflow-hidden">
            <img
              src="https://derecho.uniandes.edu.co/wp-content/uploads/2024/04/clinica-juridica-web-2180-x-636px.png"
              alt="Nuevas alianzas jurídicas"
              className="w-full h-48 object-cover"
            />
            <div className="p-4">
              <p className="text-sm text-gray-500">Marzo 2024</p>
              <h3 className="font-semibold text-[var(--azul-foidhd)]">Nuevas alianzas jurídicas</h3>
            </div>
          </div>

          <div className="shadow rounded overflow-hidden">
            <img
              src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvnOwomb4HtHk2hAO27jZyKuH4Sco7wQfQ8Q&s"
              alt="Informe anual de vulneraciones"
              className="w-full h-48 object-cover"
            />
            <div className="p-4">
              <p className="text-sm text-gray-500">Febrero 2024</p>
              <h3 className="font-semibold text-[var(--azul-foidhd)]">Informe anual de vulneraciones</h3>
            </div>
          </div>
        </div>
      </section>

      {/* CTA final */}
      <section className="bg-[var(--amarillo-foidhd)] py-10 text-center text-black">
        <h3 className="text-xl font-bold mb-4">¿Necesitas asesoría jurídica o apoyo psicosocial?</h3>
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
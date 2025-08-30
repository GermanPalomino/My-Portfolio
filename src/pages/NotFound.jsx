import React from "react";
import { Link } from "react-router-dom";

export default function NotFound() {
  return (
    <section className="py-20 text-center">
      <h1 className="text-4xl font-bold text-[var(--azul-foidhd)] mb-4">404</h1>
      <p className="text-gray-600 mb-6">Página no encontrada.</p>
      <Link to="/" className="text-white bg-[var(--azul-foidhd)] px-4 py-2 rounded">
        Volver al inicio
      </Link>
    </section>
  );
}

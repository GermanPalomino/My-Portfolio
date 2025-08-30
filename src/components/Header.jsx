import React from "react";
import { Link, NavLink } from "react-router-dom";

export default function Header() {
  const base = "hover:text-[var(--amarillo-foidhd)]";
  const active = "text-[var(--amarillo-foidhd)]";

  return (
    <header className="bg-[var(--azul-foidhd)] text-white shadow">
      <div className="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        {/* Logo + Marca (sin imagen local) */}
        <Link to="/" className="flex items-center space-x-3">
          <img
            src="https://cdn-icons-png.flaticon.com/512/2061/2061398.png"
            alt="Logo FOIDHD"
            className="h-10 w-auto"
          />
          <span className="text-2xl font-bold">Template</span>
        </Link>

        {/* Menú */}
        <nav className="space-x-6 text-sm font-medium">
          <NavLink to="/" end className={({isActive}) => isActive ? active : base}>
            Inicio
          </NavLink>
          <NavLink to="/clinica-juridica" className={({isActive}) => isActive ? active : base}>
            Clínica Jurídica
          </NavLink>
          <NavLink to="/datos" className={({isActive}) => isActive ? active : base}>
            Datos
          </NavLink>
          <NavLink to="/salud-mental" className={({isActive}) => isActive ? active : base}>
            Salud Mental
          </NavLink>
        </nav>
      </div>
    </header>
  );
}
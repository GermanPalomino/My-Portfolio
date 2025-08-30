import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Header from "./components/Header.jsx";
import Footer from "./components/Footer.jsx";
import Home from "./pages/Home.jsx";
import ClinicaJuridica from "./pages/ClinicaJuridica.jsx";
import Datos from "./pages/Datos.jsx";
import SaludMental from "./pages/SaludMental.jsx";
import NotFound from "./pages/NotFound.jsx";

export default function App() {
  return (
    <BrowserRouter>
      <div className="min-h-screen flex flex-col">
        <Header />
        <main className="flex-1">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/clinica-juridica" element={<ClinicaJuridica />} />
            <Route path="/datos" element={<Datos />} />
            <Route path="/salud-mental" element={<SaludMental />} />
            <Route path="*" element={<NotFound />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </BrowserRouter>
  );
}
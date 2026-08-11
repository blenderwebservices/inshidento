import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { ArrowRight, Building2, CheckCircle2, Send } from 'lucide-react';
import './CTA.css';

const CTA = () => {
  const [submitted, setSubmitted] = useState(false);
  const [formData, setFormData] = useState({
    nombre: '',
    empresa: '',
    email: '',
    sucursales: '1-10 sucursales'
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    setSubmitted(true);
  };

  return (
    <section id="cta" className="cta-section section-padding container">
      <div className="cta-card glass-card">
        <div className="cta-content">
          <span className="section-badge">Prueba Empresarial</span>
          <h2>Transforma la Gestión de Incidencias en tus Sucursales</h2>
          <p>
            Agenda una sesión guiada con uno de nuestros especialistas de infraestructura y descubre cómo Inshidento optimiza tus tiempos y presupuestos.
          </p>

          <ul className="cta-bullets">
            <li><CheckCircle2 size={16} className="check-blue" /> Configuración inicial Multi-Tenant en menos de 24 hrs.</li>
            <li><CheckCircle2 size={16} className="check-blue" /> Prueba gratuita con hasta 5 sucursales y 10 notificadores.</li>
            <li><CheckCircle2 size={16} className="check-blue" /> Capacitación para tu personal técnico e integración con finanzas.</li>
          </ul>
        </div>

        <div className="cta-form-box">
          {submitted ? (
            <div className="submitted-message">
              <CheckCircle2 size={48} className="success-icon" />
              <h3>¡Solicitud Recibida!</h3>
              <p>Un consultor de Inshidento se pondrá en contacto en breve para agendar la demo de tu empresa.</p>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="demo-form">
              <h3>Solicitar Demo Personalizada</h3>
              
              <div className="form-group">
                <label>Nombre Completo</label>
                <input 
                  type="text" 
                  required 
                  placeholder="Ej. Carlos Mendoza" 
                  value={formData.nombre}
                  onChange={(e) => setFormData({...formData, nombre: e.target.value})}
                />
              </div>

              <div className="form-group">
                <label>Empresa / Organización</label>
                <input 
                  type="text" 
                  required 
                  placeholder="Ej. Corporativo Retail S.A." 
                  value={formData.empresa}
                  onChange={(e) => setFormData({...formData, empresa: e.target.value})}
                />
              </div>

              <div className="form-group">
                <label>Correo Empresarial</label>
                <input 
                  type="email" 
                  required 
                  placeholder="carlos@empresa.com" 
                  value={formData.email}
                  onChange={(e) => setFormData({...formData, email: e.target.value})}
                />
              </div>

              <div className="form-group">
                <label>Número de Sucursales / Edificios</label>
                <select 
                  value={formData.sucursales}
                  onChange={(e) => setFormData({...formData, sucursales: e.target.value})}
                >
                  <option value="1-10 sucursales">1 a 10 sucursales</option>
                  <option value="11-50 sucursales">11 a 50 sucursales</option>
                  <option value="50+ sucursales">Más de 50 sucursales</option>
                </select>
              </div>

              <button type="submit" className="btn-primary form-submit-btn">
                <span>Enviar Solicitud</span>
                <Send size={16} />
              </button>
            </form>
          )}
        </div>
      </div>
    </section>
  );
};

export default CTA;

import React from 'react';
import { motion } from 'framer-motion';
import { CheckCircle2, Building2, Store, Sparkles, ShieldCheck, ArrowRight, MessageSquareCode } from 'lucide-react';
import './PricingSection.css';

const PricingSection = () => {
  return (
    <section id="precios" className="pricing-section section-padding">
      <div className="container">
        <div className="section-header text-center">
          <span className="section-badge">
            <Sparkles size={14} className="sparkle-icon" /> Planes & Licenciamiento Transparentes
          </span>
          <h2 className="gradient-text">Planes Diseñados para Escalar con tus Sucursales</h2>
          <p className="section-subtitle">
            Elige el plan adecuado según la cantidad de sucursales o edificios que administras. Sin costos ocultos y con licencias de notificador ilimitadas.
          </p>
        </div>

        <div className="pricing-grid">
          {/* Plan 1: Licencia por Sucursal */}
          <motion.div 
            initial={{ opacity: 0, y: 25 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5 }}
            className="pricing-card glass-card"
          >
            <div className="plan-header">
              <div className="plan-icon-wrapper blue">
                <Store size={24} />
              </div>
              <h3 className="plan-title">Licencia por Sucursal</h3>
              <p className="plan-subtitle">Para sucursales u operar punto a punto</p>
            </div>

            <div className="plan-price-block">
              <div className="price-amount">
                <span className="currency">USD $</span>
                <span className="amount">70</span>
              </div>
              <span className="price-period">/ Mes / Sucursal</span>
            </div>

            <ul className="plan-features">
              <li>
                <CheckCircle2 size={18} className="check-icon" />
                <span><strong>Administración de incidencias end-to-end</strong> (Flujo completo de 10 pasos).</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon" />
                <span><strong>Incluye licencia de Stakeholder</strong> (Acceso a reportes y métricas ejecutivas).</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon" />
                <span><strong>Licencias sin límite</strong> para la App de Registro de Incidencias (Notificadores en campo), enlazadas a la licencia de la sucursal.</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon" />
                <span>Módulo de Cotizaciones y Generación de Órdenes de Compra (OCs).</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon" />
                <span>Asignación a Fixers Internos o Proveedores Externos.</span>
              </li>
            </ul>

            <div className="plan-cta">
              <a href="/reports/dashboard" className="btn-secondary full-width">
                <span>Comenzar Ahora</span>
                <ArrowRight size={16} />
              </a>
            </div>
          </motion.div>

          {/* Plan 2: Licencia Multisucursal (Destacado) */}
          <motion.div 
            initial={{ opacity: 0, y: 25 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5, delay: 0.1 }}
            className="pricing-card glass-card popular"
          >
            <div className="popular-badge">
              <Sparkles size={13} /> Recomendado &bull; A partir de 3 sucursales
            </div>

            <div className="plan-header">
              <div className="plan-icon-wrapper amber">
                <Building2 size={24} />
              </div>
              <h3 className="plan-title">Licencia Multisucursal</h3>
              <p className="plan-subtitle">A partir de 3 sucursales registradas</p>
            </div>

            <div className="plan-price-block">
              <div className="price-amount">
                <span className="currency">USD $</span>
                <span className="amount highlight">55</span>
              </div>
              <span className="price-period">/ Mes / Sucursal</span>
            </div>

            <ul className="plan-features">
              <li>
                <CheckCircle2 size={18} className="check-icon amber" />
                <span><strong>Todas las ventajas</strong> de la Licencia por Sucursal.</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon amber" />
                <span><strong>Ahorro del 21%</strong> por volumen desde la 3ra sucursal.</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon amber" />
                <span><strong>Incluye licencias de Stakeholder</strong> para consulta ejecutiva multi-zona.</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon amber" />
                <span><strong>Licencias sin límite</strong> para Notificadores en todas tus sucursales.</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon amber" />
                <span>Métricas regionales comparativas por zona geográfica.</span>
              </li>
            </ul>

            <div className="plan-cta">
              <a href="/reports/dashboard" className="btn-primary full-width">
                <span>Contratar Multisucursal</span>
                <ArrowRight size={16} />
              </a>
            </div>
          </motion.div>

          {/* Plan 3: Enterprise */}
          <motion.div 
            initial={{ opacity: 0, y: 25 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="pricing-card glass-card"
          >
            <div className="plan-header">
              <div className="plan-icon-wrapper purple">
                <ShieldCheck size={24} />
              </div>
              <h3 className="plan-title">Enterprise</h3>
              <p className="plan-subtitle">Grandes cadenas & redes masivas</p>
            </div>

            <div className="plan-price-block">
              <div className="price-amount custom">
                <span>Acércate y hablamos</span>
              </div>
              <span className="price-period">Planes a la medida para redes de gran escala</span>
            </div>

            <ul className="plan-features">
              <li>
                <CheckCircle2 size={18} className="check-icon purple" />
                <span><strong>Sucursales ilimitadas</strong> a nivel regional o nacional.</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon purple" />
                <span>Integraciones API / Webhooks personalizadas con ERPs (SAP, Oracle, NetSuite).</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon purple" />
                <span>Infraestructura dedicada, SLA de atención 24/7 y Account Manager exclusivo.</span>
              </li>
              <li>
                <CheckCircle2 size={18} className="check-icon purple" />
                <span>Entrenamientos a medida e inteligencia artificial entrenada en tu histórico.</span>
              </li>
            </ul>

            <div className="plan-cta">
              <a href="#flujo" className="btn-secondary full-width">
                <MessageSquareCode size={16} />
                <span>Contactar a Ventas</span>
              </a>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default PricingSection;

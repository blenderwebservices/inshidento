import React from 'react';
import { motion } from 'framer-motion';
import { 
  Receipt, UserCheck, Briefcase, FileText, CheckCircle2, 
  DollarSign, ArrowUpRight, ShieldAlert, CreditCard
} from 'lucide-react';
import './BillingSection.css';

const BillingSection = () => {
  return (
    <section id="fixers" className="billing-section section-padding">
      <div className="container">
        <div className="section-header text-center">
          <span className="section-badge">Módulo Financiero & Cobranza</span>
          <h2 className="gradient-text">Fixers Internos, Externos y Facturación Automática</h2>
          <p className="section-subtitle">
            Combina el trabajo de tu personal de plantilla con proveedores tercerizados. Integra incidencias resueltas en paquetes de cobro dirigidos al área de finanzas.
          </p>
        </div>

        <div className="fixers-dual-grid">
          {/* Card Fixer Interno */}
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5 }}
            className="fixer-type-card glass-card interno"
          >
            <div className="type-badge">Personal de Plantilla</div>
            <div className="type-header">
              <UserCheck size={32} className="type-icon" />
              <div>
                <h3>Fixer Interno</h3>
                <p>Técnicos contratados directamente por la empresa</p>
              </div>
            </div>

            <ul className="type-features">
              <li>
                <CheckCircle2 size={16} className="check-blue" />
                <span>Asignación directa basada en turnos y horas laborales.</span>
              </li>
              <li>
                <CheckCircle2 size={16} className="check-blue" />
                <span>Control de inventario interno de refacciones y materiales.</span>
              </li>
              <li>
                <CheckCircle2 size={16} className="check-blue" />
                <span>Reportes consolidados de mantenimiento preventivo y correctivo.</span>
              </li>
            </ul>
          </motion.div>

          {/* Card Fixer Externo */}
          <motion.div 
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5, delay: 0.1 }}
            className="fixer-type-card glass-card externo"
          >
            <div className="type-badge alt">Contratistas & Freelancers</div>
            <div className="type-header">
              <Briefcase size={32} className="type-icon alt" />
              <div>
                <h3>Fixer Externo</h3>
                <p>Proveedores tercerizados que cobran por servicio</p>
              </div>
            </div>

            <ul className="type-features">
              <li>
                <CheckCircle2 size={16} className="check-purple" />
                <span>Auto-reclamo en la Cola Pública de incidencias disponibles.</span>
              </li>
              <li>
                <CheckCircle2 size={16} className="check-purple" />
                <span>Cotización de mano de obra y refacciones con aprobación de Gestor.</span>
              </li>
              <li>
                <CheckCircle2 size={16} className="check-purple" />
                <span>Agrupación automática de tickets cerrados para envío de factura.</span>
              </li>
            </ul>
          </motion.div>
        </div>

        {/* Invoice Package Preview Box */}
        <motion.div 
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.2 }}
          className="billing-preview-card glass-card"
        >
          <div className="preview-top">
            <div className="top-title">
              <Receipt size={24} className="icon-purple" />
              <div>
                <h4>Consolidador de Reportes para Facturación</h4>
                <p>Agrupa incidencias solas o en paquetes por proveedor/fixer externo</p>
              </div>
            </div>
            <span className="status-badge-green">Aprobado para Pago</span>
          </div>

          <div className="invoice-items-table">
            <div className="table-row header">
              <span>Folio Ticket</span>
              <span>Sucursal</span>
              <span>Descripción / Trabajo</span>
              <span>Fixer</span>
              <span className="text-right">Monto</span>
            </div>
            <div className="table-row">
              <span className="folio">#INC-8802</span>
              <span>Sucursal Polanco</span>
              <span>Cambio de Balastro Eléctrico</span>
              <span>Servimant S.A.</span>
              <span className="amount text-right">$85.00 USD</span>
            </div>
            <div className="table-row">
              <span className="folio">#INC-8841</span>
              <span>Sucursal Guadalajara</span>
              <span>Reparación de Tubería 2"</span>
              <span>Servimant S.A.</span>
              <span className="amount text-right">$140.00 USD</span>
            </div>
            <div className="table-row">
              <span className="folio">#INC-8899</span>
              <span>Planta Querétaro</span>
              <span>Mantenimiento Preventivo HVAC</span>
              <span>Servimant S.A.</span>
              <span className="amount text-right">$320.00 USD</span>
            </div>
          </div>

          <div className="preview-bottom">
            <div className="total-label">
              <span>Monto Total del Lote de Facturación:</span>
              <strong className="total-amount">$545.00 USD</strong>
            </div>
            <button className="btn-primary btn-sm">
              <span>Generar Pre-Factura en PDF / XML</span>
              <ArrowUpRight size={16} />
            </button>
          </div>
        </motion.div>
      </div>
    </section>
  );
};

export default BillingSection;

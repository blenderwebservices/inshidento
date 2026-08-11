import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { Building2, Store, MapPin, Users, ShieldCheck, Check } from 'lucide-react';
import './MultiTenantSection.css';

const sampleCompanies = [
  {
    id: 'retail',
    name: 'Grupo Comercial Retail S.A.',
    type: 'Cadena de Tiendas & Franquicias',
    branchesCount: 42,
    notifiersCount: 180,
    activeIncidents: 6,
    branches: [
      'Sucursal Polanco (CDMX)',
      'Sucursal Guadalajara Centro',
      'Sucursal Monterrey Plaza',
      'Planta Logística Querétaro'
    ]
  },
  {
    id: 'corporate',
    name: 'Corporativo Financiero Global',
    type: 'Complejos de Edificios de Oficinas',
    branchesCount: 15,
    notifiersCount: 350,
    activeIncidents: 3,
    branches: [
      'Torre Reforma - Piso 12',
      'Torre Financiera Santa Fe',
      'Centro de Datos Insurgentes',
      'Campus Corporativo Guadalajara'
    ]
  },
  {
    id: 'industrial',
    name: 'Industrias Automotrices del Norte',
    type: 'Plantas Industriales & Almacenes',
    branchesCount: 8,
    notifiersCount: 220,
    activeIncidents: 9,
    branches: [
      'Planta Ensamble Ramos Arizpe',
      'Almacén Central Silao',
      'Parque Industrial Puebla',
      'Planta Estampados Toluca'
    ]
  }
];

const MultiTenantSection = () => {
  const [selectedCompany, setSelectedCompany] = useState(sampleCompanies[0]);

  return (
    <section id="empresas" className="multitenant-section section-padding">
      <div className="container">
        <div className="section-header text-center">
          <span className="section-badge">Arquitectura Multi-Tenant</span>
          <h2 className="gradient-text">Estructura para Múltiples Empresas y Sucursales</h2>
          <p className="section-subtitle">
            Alberga en una sola plataforma múltiples organizaciones independientes, cada una administrando sus sucursales, franquicias, edificios y puntos de levantamiento.
          </p>
        </div>

        <div className="multitenant-grid">
          <div className="company-selector-panel">
            <h3 className="panel-title">Selecciona un Modelo de Empresa</h3>
            <div className="company-cards-list">
              {sampleCompanies.map((comp) => (
                <button
                  key={comp.id}
                  onClick={() => setSelectedCompany(comp)}
                  className={`company-card-btn glass-card ${selectedCompany.id === comp.id ? 'selected' : ''}`}
                >
                  <div className="comp-icon-box">
                    <Building2 size={22} />
                  </div>
                  <div className="comp-info">
                    <h4>{comp.name}</h4>
                    <span className="comp-type">{comp.type}</span>
                  </div>
                </button>
              ))}
            </div>
          </div>

          <motion.div 
            key={selectedCompany.id}
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.4 }}
            className="company-detail-panel glass-card"
          >
            <div className="detail-header">
              <div>
                <span className="tenant-tag">Tenant Activo</span>
                <h3>{selectedCompany.name}</h3>
              </div>
              <div className="stats-row">
                <div className="stat-box">
                  <span className="stat-num">{selectedCompany.branchesCount}</span>
                  <span className="stat-lbl">Sucursales</span>
                </div>
                <div className="stat-box">
                  <span className="stat-num">{selectedCompany.notifiersCount}</span>
                  <span className="stat-lbl">Notificadores</span>
                </div>
              </div>
            </div>

            <div className="branches-preview-block">
              <h4>Puntos Agrupadores / Sucursales Asociadas</h4>
              <div className="branches-list">
                {selectedCompany.branches.map((branch, bIdx) => (
                  <div key={bIdx} className="branch-item-chip">
                    <MapPin size={15} className="pin-icon" />
                    <span>{branch}</span>
                    <span className="active-dot-green"></span>
                  </div>
                ))}
              </div>
            </div>

            <div className="tenant-features-list">
              <div className="feature-check-item">
                <Check size={16} className="check-green" />
                <span>Aislamiento completo de datos por Empresa / Tenant.</span>
              </div>
              <div className="feature-check-item">
                <Check size={16} className="check-green" />
                <span>Notificadores asignados a sucursales o edificios específicos.</span>
              </div>
              <div className="feature-check-item">
                <Check size={16} className="check-green" />
                <span>Fixers Internos dedicados o Fixers Externos contratados por sucursal.</span>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default MultiTenantSection;

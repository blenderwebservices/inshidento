import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  Sparkles, ArrowRight, Camera, Mic, Video, Building2, 
  UserCheck, Receipt, Play, CheckCircle2, ShieldAlert,
  Clock, DollarSign, ChevronRight
} from 'lucide-react';
import './Hero.css';

const stepsSimulator = [
  {
    id: 1,
    stage: '1. Detección en Sucursal',
    actor: 'Notificador (Personal de Campo)',
    title: 'Fuga de agua en UMA-04',
    location: 'Sucursal Polanco - Piso 3, Cuarto de Máquinas',
    evidence: [
      { type: 'foto', label: 'Foto Fuga.jpg' },
      { type: 'audio', label: 'Nota_Voz_01.aac (0:18s)' },
      { type: 'video', label: 'Video_Diagnostico.mp4' }
    ],
    status: 'Abierta',
    statusColor: '#eab308'
  },
  {
    id: 2,
    stage: '2. Asignación a Fixer',
    actor: 'Gestor / Cola Pública',
    title: 'Evaluación y Triaje Técnico',
    assignedTo: 'Fixer Externo: Servimant HVAC S.A.',
    mode: 'Reclamo en Cola Pública ($140.00 USD)',
    status: 'Asignada',
    statusColor: '#3b82f6'
  },
  {
    id: 3,
    stage: '3. Atención y Cierre',
    actor: 'Fixer (Técnico de Campo)',
    title: 'Reparación de Valvula y Prueba de Presión',
    resolutionEvidence: 'Foto_Cierre_Reparado.jpg',
    cost: '$140.00 USD (Mano de Obra + Refacciones)',
    status: 'Resuelta',
    statusColor: '#10b981'
  },
  {
    id: 4,
    stage: '4. Liquidación & Facturación',
    actor: 'Departamento de Finanzas',
    title: 'Reporte de Cobro Consolidado #FAC-2026-904',
    billingStatus: 'Lote de 5 Incidencias -> Aprobado para Pago',
    status: 'Cerrada & Facturada',
    statusColor: '#8b5cf6'
  }
];

const Hero = () => {
  const [activeStep, setActiveStep] = useState(0);

  const nextStep = () => {
    setActiveStep((prev) => (prev + 1) % stepsSimulator.length);
  };

  return (
    <section className="hero-section">
      <div className="container">
        <div className="hero-grid">
          <div className="hero-content">
            <motion.div 
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
              className="hero-badge"
            >
              <Sparkles size={16} className="sparkle-icon" />
              <span>Gestión Operativa Multi-Tenant & IA</span>
            </motion.div>

            <motion.h1 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.1 }}
              className="hero-title"
            >
              Gestiona Incidencias en <span className="gradient-text">Sucursales y Edificios</span> de Extremo a Extremo
            </motion.h1>

            <motion.p 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="hero-description"
            >
              Conecta el reporte multimedia (**foto, audio y video**) en tus sucursales con la asignación a **Fixers internos o externos**, el seguimiento en tiempo real y la **generación automática de reportes para facturación**.
            </motion.p>

            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.3 }}
              className="hero-tags"
            >
              <div className="tag-item"><Building2 size={15} /> Multi-Empresa</div>
              <div className="tag-item"><Camera size={15} /> Foto, Audio & Video</div>
              <div className="tag-item"><UserCheck size={15} /> Fixer Interno/Externo</div>
              <div className="tag-item"><Receipt size={15} /> Módulo de Cobro</div>
            </motion.div>

            <motion.div 
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.4 }}
              className="hero-actions"
            >
              <a href="/reports/dashboard" className="btn-primary hero-btn-main">
                <span>Ir al Dashboard & Tablero de Incidencias</span>
                <ArrowRight size={18} />
              </a>
            </motion.div>
          </div>

          {/* Interactive Live Simulator */}
          <motion.div 
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.7, delay: 0.3 }}
            className="hero-simulator-wrapper"
          >
            <div className="simulator-card glass-card">
              <div className="simulator-header">
                <div className="sim-dots">
                  <span className="dot red"></span>
                  <span className="dot yellow"></span>
                  <span className="dot green"></span>
                </div>
                <div className="sim-title">
                  <span className="live-dot"></span>
                  Simulador del Ciclo de Vida de Incidencia
                </div>
                <div className="sim-tenant-badge">
                  <Building2 size={13} /> Grupo Retail Global
                </div>
              </div>

              <div className="simulator-stepper">
                {stepsSimulator.map((step, idx) => (
                  <button
                    key={step.id}
                    onClick={() => setActiveStep(idx)}
                    className={`stepper-btn ${idx === activeStep ? 'active' : ''} ${idx < activeStep ? 'completed' : ''}`}
                  >
                    <span className="step-num">{idx + 1}</span>
                  </button>
                ))}
              </div>

              <div className="simulator-body">
                <AnimatePresence mode="wait">
                  <motion.div 
                    key={activeStep}
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    exit={{ opacity: 0, x: -20 }}
                    transition={{ duration: 0.3 }}
                    className="step-detail-card"
                  >
                    <div className="step-badge-row">
                      <span className="stage-pill">{stepsSimulator[activeStep].stage}</span>
                      <span 
                        className="status-pill" 
                        style={{ 
                          backgroundColor: `${stepsSimulator[activeStep].statusColor}20`,
                          color: stepsSimulator[activeStep].statusColor,
                          border: `1px solid ${stepsSimulator[activeStep].statusColor}40`
                        }}
                      >
                        {stepsSimulator[activeStep].status}
                      </span>
                    </div>

                    <h3 className="sim-task-title">{stepsSimulator[activeStep].title}</h3>
                    <p className="sim-actor"><UserCheck size={14} /> <strong>Actor:</strong> {stepsSimulator[activeStep].actor}</p>

                    {/* Step 1 specifics */}
                    {activeStep === 0 && (
                      <div className="sim-content-block">
                        <p className="sim-location">📍 {stepsSimulator[activeStep].location}</p>
                        <div className="media-pills-grid">
                          <div className="media-pill foto"><Camera size={13} /> Foto Fuga</div>
                          <div className="media-pill audio"><Mic size={13} /> Audio (0:18s)</div>
                          <div className="media-pill video"><Video size={13} /> Video corto</div>
                        </div>
                      </div>
                    )}

                    {/* Step 2 specifics */}
                    {activeStep === 1 && (
                      <div className="sim-content-block">
                        <div className="fixer-info-box">
                          <p><strong>Asignado a:</strong> {stepsSimulator[activeStep].assignedTo}</p>
                          <p className="highlight-text">⚡ {stepsSimulator[activeStep].mode}</p>
                        </div>
                      </div>
                    )}

                    {/* Step 3 specifics */}
                    {activeStep === 2 && (
                      <div className="sim-content-block">
                        <p className="sim-location">✅ Evidencia de Cierre: <code>Foto_Reparado_Final.jpg</code></p>
                        <p className="cost-tag"><DollarSign size={14} /> Costo Calculado: {stepsSimulator[activeStep].cost}</p>
                      </div>
                    )}

                    {/* Step 4 specifics */}
                    {activeStep === 3 && (
                      <div className="sim-content-block">
                        <div className="billing-summary-box">
                          <Receipt size={24} className="receipt-icon" />
                          <div>
                            <h4>{stepsSimulator[activeStep].title}</h4>
                            <p>{stepsSimulator[activeStep].billingStatus}</p>
                          </div>
                        </div>
                      </div>
                    )}
                  </motion.div>
                </AnimatePresence>
              </div>

              <div className="simulator-footer">
                <button className="btn-sim-next" onClick={nextStep}>
                  <span>Siguiente Etapa del Flujo</span>
                  <ChevronRight size={16} />
                </button>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default Hero;

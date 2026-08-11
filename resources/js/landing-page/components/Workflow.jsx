import React from 'react';
import { motion } from 'framer-motion';
import { 
  Camera, Mic, Video, UserCheck, ShieldAlert, CheckCircle2, 
  Receipt, ArrowRight, Layers, Clock, FileCheck, DollarSign
} from 'lucide-react';
import './Workflow.css';

const workflowSteps = [
  {
    step: 'Etapa 01',
    title: 'Detección y Levantamiento Multimedia',
    actor: 'Notificador en Sucursal / Edificio',
    description: 'El personal en sucursal o edificio detecta la falla y levanta la incidencia en menos de 30 segundos capturando evidencia completa.',
    details: [
      'Captura de fotografías HD de la falla',
      'Grabación de nota de voz (audio) explicando los síntomas',
      'Adjunto opcional de video corto HD (mp4)',
      'Selección de sucursal y geolocalización de precisión'
    ],
    icon: <Camera size={26} />,
    badgeColor: '#3b82f6'
  },
  {
    step: 'Etapa 02',
    title: 'Triaje, Clasificación y Asignación Flexible',
    actor: 'Gestor de Operaciones / Algoritmo IA',
    description: 'El sistema valida la incidencia y permite la doble modalidad de asignación según el tipo de atención requerida.',
    details: [
      'Modalidad A: Asignación directa a Fixer Interno de plantilla',
      'Modalidad B: Publicación en Cola Pública para Fixers Externos',
      'Clasificación automática por categoría y nivel de prioridad',
      'Notificaciones push inmediatas a dispositivos de campo'
    ],
    icon: <UserCheck size={26} />,
    badgeColor: '#8b5cf6'
  },
  {
    step: 'Etapa 03',
    title: 'Atención y Seguimiento en Campo',
    actor: 'Fixer (Técnico Interno / Externo)',
    description: 'El técnico acude a la sucursal, inicia el ticket y mantiene informado en tiempo real al gestor y notificador.',
    details: [
      'Cambio a estado "En Progreso" y registro de cronómetro',
      'Registro de refacciones y materiales utilizados',
      'Soporte para trabajo offline si la sucursal pierde señal',
      'Opción de pausar ticket por espera de suministros'
    ],
    icon: <Clock size={26} />,
    badgeColor: '#eab308'
  },
  {
    step: 'Etapa 04',
    title: 'Evidencia de Cierre y Verificación',
    actor: 'Fixer & Notificador',
    description: 'Obligatoriedad de comprobación fotográfica/video de la solución antes de formalizar el cierre técnico.',
    details: [
      'Subida de fotografía/video de la falla resuelta (Antes vs. Después)',
      'Firma digital o comentario de conformidad del notificador',
      'Consolidación final de costos (mano de obra + refacciones)',
      'Cambio a estado congelado "Resuelta / Cerrada"'
    ],
    icon: <CheckCircle2 size={26} />,
    badgeColor: '#10b981'
  },
  {
    step: 'Etapa 05',
    title: 'Agrupación en Reportes y Facturación',
    actor: 'Departamento de Finanzas & Cobranza',
    description: 'Las incidencias cerradas se integran solas o en paquetes para su envío a facturación y pago.',
    details: [
      'Agrupación de incidencias cerradas en Lotes de Pre-Factura',
      'Generación de reportes de cobro para Fixers Externos / Contratistas',
      'Reportes de costo y control de horas para Fixers Internos',
      'Aprobación contable y liquidación sin fricción'
    ],
    icon: <Receipt size={26} />,
    badgeColor: '#ec4899'
  }
];

const Workflow = () => {
  return (
    <section id="flujo" className="workflow-section section-padding">
      <div className="container">
        <div className="section-header text-center">
          <span className="section-badge">Trazabilidad End-to-End</span>
          <h2 className="gradient-text">El Flujo Operativo de Inshidento</h2>
          <p className="section-subtitle">
            Desde que se detecta una falla en la sucursal hasta que el reporte llega a finanzas para su cobro.
          </p>
        </div>

        <div className="workflow-timeline">
          {workflowSteps.map((step, idx) => (
            <motion.div 
              key={idx}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: idx * 0.1 }}
              className="workflow-card glass-card"
            >
              <div className="workflow-card-header">
                <div 
                  className="step-icon-wrapper"
                  style={{ backgroundColor: `${step.badgeColor}20`, color: step.badgeColor, borderColor: `${step.badgeColor}40` }}
                >
                  {step.icon}
                </div>
                <div className="step-meta">
                  <span className="step-number-tag">{step.step}</span>
                  <h3 className="step-title">{step.title}</h3>
                </div>
              </div>

              <div className="workflow-card-body">
                <div className="actor-badge">
                  <UserCheck size={14} /> {step.actor}
                </div>
                <p className="step-description">{step.description}</p>
                <ul className="step-details-list">
                  {step.details.map((detail, dIdx) => (
                    <li key={dIdx}>
                      <FileCheck size={14} className="check-icon" />
                      <span>{detail}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Workflow;

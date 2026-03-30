import React from 'react';
import { motion } from 'framer-motion';
import { FileEdit, Cpu, Send, CheckCircle2 } from 'lucide-react';
import './Workflow.css';

const steps = [
  {
    icon: <FileEdit size={24} />,
    title: 'Reporte',
    description: 'El usuario crea la incidencia en segundos.'
  },
  {
    icon: <Cpu size={24} />,
    title: 'Análisis IA',
    description: 'Nuestro motor clasifica y analiza el sentimiento.'
  },
  {
    icon: <Send size={24} />,
    title: 'Propuesta',
    description: 'Se genera una respuesta sugerida inteligente.'
  },
  {
    icon: <CheckCircle2 size={24} />,
    title: 'Resolución',
    description: 'Incidencia cerrada con éxito y feedback guardado.'
  }
];

const Workflow = () => {
  return (
    <section id="workflow" className="workflow-section section-padding">
      <div className="container">
        <div className="section-header">
          <h2 className="gradient-text">Flujo de Trabajo Inteligente</h2>
          <p>Mira cómo Inshidento transforma el caos de los reportes en una resolución estructurada.</p>
        </div>

        <div className="steps-container">
          {steps.map((step, index) => (
            <motion.div 
              key={index}
              initial={{ opacity: 0, x: -20 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="step-item"
            >
              <div className="step-point glass-card">
                <div className="step-icon">{step.icon}</div>
                <div className="step-number">{index + 1}</div>
              </div>
              <div className="step-content">
                <h3>{step.title}</h3>
                <p>{step.description}</p>
              </div>
              {index < steps.length - 1 && <div className="step-connector"></div>}
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Workflow;

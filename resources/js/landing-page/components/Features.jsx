import React from 'react';
import { motion } from 'framer-motion';
import { Brain, Zap, BarChart3, Lock } from 'lucide-react';
import './Features.css';

const features = [
  {
    icon: <Brain size={32} />,
    title: 'Clasificación Inteligente',
    description: 'Nuestra IA analiza el contenido de cada incidencia para categorizarla automáticamente, eliminando el trabajo manual.'
  },
  {
    icon: <Zap size={32} />,
    title: 'Respuestas Sugeridas',
    description: 'Integración profunda con OpenAI para generar borradores de respuesta precisos y rápidos basados en el historial.'
  },
  {
    icon: <BarChart3 size={32} />,
    title: 'Seguimiento Real-time',
    description: 'Visualiza el estado de todas tus incidencias en un dashboard moderno, intuitivo y altamente reactivo.'
  },
  {
    icon: <Lock size={32} />,
    title: 'Seguridad Empresarial',
    description: 'Tus datos están protegidos con encriptación de grado bancario y una arquitectura diseñada para la escalabilidad.'
  }
];

const Features = () => {
  return (
    <section id="features" className="features-section container section-padding">
      <div className="section-header">
        <h2 className="gradient-text">Potencia tu Soporte Técnico</h2>
        <p>Descubre por qué Inshidento es la herramienta definitiva para equipos de alto rendimiento.</p>
      </div>

      <div className="features-grid">
        {features.map((feature, index) => (
          <motion.div 
            key={index}
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5, delay: index * 0.1 }}
            className="feature-card glass-card"
          >
            <div className="feature-icon">{feature.icon}</div>
            <h3>{feature.title}</h3>
            <p>{feature.description}</p>
          </motion.div>
        ))}
      </div>
    </section>
  );
};

export default Features;

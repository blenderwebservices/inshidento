import React from 'react';
import { motion } from 'framer-motion';
import { Brain, Camera, Mic, Sparkles, CheckCircle2, ShieldCheck, Zap } from 'lucide-react';
import './AIFeatures.css';

const aiCapas = [
  {
    icon: <Camera size={28} />,
    title: 'Visión por Computadora (Vision AI)',
    description: 'Análisis instantáneo de fotos y videos subidos por el notificador para identificar la falla automáticamente y verificar la evidencia visual antes/después del cierre.'
  },
  {
    icon: <Mic size={28} />,
    title: 'Transcripción y NLP de Audio',
    description: 'Conversión automática de notas de voz capturadas en sucursales a texto estructurado, sintetizando requerimientos técnicos y urgencia.'
  },
  {
    icon: <Sparkles size={28} />,
    title: 'Smart Dispatcher & Matching',
    description: 'Recomendación en tiempo real del Fixer ideal (Interno vs. Externo) según cercanía a la sucursal, especialidad y carga de trabajo.'
  },
  {
    icon: <ShieldCheck size={28} />,
    title: 'Auditoría Anti-Sobrecostos',
    description: 'Agentes de IA que auditan cotizaciones de contratistas externos en pre-facturas comparándolas con tabuladores de mercado.'
  }
];

const AIFeatures = () => {
  return (
    <section id="ia" className="ai-section section-padding">
      <div className="container">
        <div className="section-header text-center">
          <span className="section-badge alt">Inteligencia Artificial Multimodal</span>
          <h2 className="gradient-text">Potenciado por Modelos de IA Avanzados</h2>
          <p className="section-subtitle">
            Combina visión artificial, análisis de voz y algoritmos predictivos para acelerar la resolución y evitar sobrecostos.
          </p>
        </div>

        <div className="ai-grid">
          {aiCapas.map((capa, idx) => (
            <motion.div 
              key={idx}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: idx * 0.1 }}
              className="ai-card glass-card"
            >
              <div className="ai-icon-box">{capa.icon}</div>
              <h3>{capa.title}</h3>
              <p>{capa.description}</p>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default AIFeatures;

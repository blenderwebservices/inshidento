import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles, ArrowRight, Bot } from 'lucide-react';
import './Hero.css';

const Hero = () => {
  return (
    <section className="hero-section container section-padding">
      <div className="hero-content">
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          className="badge glass-card"
        >
          <Sparkles size={16} />
          <span>Impulsado por Inteligencia Artificial</span>
        </motion.div>

        <motion.h1 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.2 }}
        >
          Resuelve Incidentes <span className="gradient-text">Más Rápido</span> que Nunca
        </motion.h1>

        <motion.p 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="hero-subtitle"
        >
          Inshidento automatiza la clasificación y respuesta de incidencias mediante modelos GPT avanzados, permitiendo que tu equipo se enfoque en lo que realmente importa.
        </motion.p>

        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, delay: 0.6 }}
          className="hero-actions"
        >
          <button className="btn-primary flex-center">
            Probar Demo Gratis <ArrowRight size={18} />
          </button>
          <button className="btn-secondary flex-center">
            <Bot size={18} /> Ver Documentación
          </button>
        </motion.div>
      </div>

      <motion.div 
        initial={{ opacity: 0, scale: 0.9 }}
        animate={{ opacity: 1, scale: 1 }}
        transition={{ duration: 0.8, delay: 0.4 }}
        className="hero-visual"
      >
        <div className="glass-card main-preview">
          <div className="preview-header">
            <div className="dot red"></div>
            <div className="dot yellow"></div>
            <div className="dot green"></div>
          </div>
          <div className="preview-body">
            <div className="skeleton-item title"></div>
            <div className="skeleton-item line long"></div>
            <div className="skeleton-item line mid"></div>
            <div className="ai-badge">AI analizando...</div>
          </div>
        </div>
        <div className="floating-blob"></div>
      </motion.div>
    </section>
  );
};

export default Hero;

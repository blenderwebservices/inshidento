import React from 'react';
import { motion } from 'framer-motion';
import { Rocket, Send } from 'lucide-react';
import './CTA.css';

const CTA = () => {
  return (
    <section className="cta-section container section-padding">
      <motion.div 
        initial={{ opacity: 0, y: 30 }}
        whileInView={{ opacity: 1, y: 0 }}
        viewport={{ once: true }}
        transition={{ duration: 0.8 }}
        className="cta-card glass-card"
      >
        <div className="cta-content">
          <h2 className="gradient-text">¿Listo para Optimizar tus Incidencias?</h2>
          <p>
            Únete a cientos de organizaciones que ya están ahorrando tiempo y recursos con nuestra plataforma de IA.
          </p>
          <div className="cta-buttons">
            <button className="btn-primary flex-center">
              Empezar Ahora <Rocket size={20} />
            </button>
            <button className="btn-secondary flex-center">
              Agendar Demo <Send size={20} />
            </button>
          </div>
        </div>
        <div className="cta-glow"></div>
      </motion.div>
    </section>
  );
};

export default CTA;

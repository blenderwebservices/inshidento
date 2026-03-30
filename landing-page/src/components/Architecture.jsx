import React from 'react';
import { motion } from 'framer-motion';
import { Server, Database, Layout, Globe2 } from 'lucide-react';
import './Architecture.css';

const techStack = [
  {
    icon: <Server size={32} />,
    title: 'Backend Laravel',
    description: 'Robusto, seguro y eficiente. Desarrollado con PHP 8.2+ para una gestión de datos impecable.',
    color: '#F05340'
  },
  {
    icon: <Database size={32} />,
    title: 'MySQL / MariaDB',
    description: 'Bases de datos relacionales escalables para almacenar cada detalle operativo con integridad.',
    color: '#00758F'
  },
  {
    icon: <Layout size={32} />,
    title: 'Frontend React',
    description: 'Interfaz moderna en React.js con Tailwind CSS para una experiencia de usuario fluida y reactiva.',
    color: '#61DAFB'
  },
  {
    icon: <Globe2 size={32} />,
    title: 'IA OpenAI',
    description: 'Integración inteligente con modelos GPT para clasificación automática y sugerencias dinámicas.',
    color: '#10A37F'
  }
];

const Architecture = () => {
  return (
    <section id="architecture" className="architecture-section container section-padding">
      <div className="section-header">
        <h2 className="gradient-text">Arquitectura de Vanguardia</h2>
        <p>Una base tecnológica sólida diseñada para escalar junto a tu organización.</p>
      </div>

      <div className="tech-stack-grid">
        {techStack.map((tech, index) => (
          <motion.div 
            key={index}
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.5, delay: index * 0.1 }}
            className="tech-card glass-card"
          >
            <div className="tech-icon" style={{ borderColor: tech.color, color: tech.color }}>
              {tech.icon}
            </div>
            <h3>{tech.title}</h3>
            <p>{tech.description}</p>
          </motion.div>
        ))}
      </div>
    </section>
  );
};

export default Architecture;

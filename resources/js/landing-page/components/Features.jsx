import React from 'react';
import { motion } from 'framer-motion';
import { ShieldCheck, Clock, Layers, DollarSign, Smartphone, BarChart3 } from 'lucide-react';
import './Features.css';

const businessBenefits = [
  {
    icon: <Clock size={28} />,
    title: '-65% en Tiempos de Resolución',
    description: 'Elimina intermediarios manuales con asignación directa o automática en cola pública a Fixers en campo.'
  },
  {
    icon: <ShieldCheck size={28} />,
    title: '100% Trazabilidad Multimedia',
    description: 'Cada incidencia cuenta con registro fotográfico, notas de voz y evidencias de cierre congeladas en auditoría.'
  },
  {
    icon: <Layers size={28} />,
    title: 'Multi-Empresa & Sucursales',
    description: 'Soporte nativo Multi-Tenant para gestionar corporativos con decenas de tiendas, edificios o franquicias.'
  },
  {
    icon: <DollarSign size={28} />,
    title: 'Liquidación de Facturas Transparente',
    description: 'Agrupa incidencias cerradas para autorizar pagos a contratistas externos o controlar costos de plantilla.'
  },
  {
    icon: <Smartphone size={28} />,
    title: 'Sincronización Offline-First',
    description: 'Las apps de notificadores y fixers siguen funcionando en sótano o zonas con baja señal y sincronizan al salir.'
  },
  {
    icon: <BarChart3 size={28} />,
    title: 'Analítica & KPIs en Tiempo Real',
    description: 'Dashboards financieros y de rendimiento para conocer los costos de mantenimiento por sucursal.'
  }
];

const Features = () => {
  return (
    <section id="beneficios" className="features-section section-padding">
      <div className="container">
        <div className="section-header text-center">
          <span className="section-badge">Beneficios Corporativos</span>
          <h2 className="gradient-text">¿Por qué Elegir Inshidento para tu Empresa?</h2>
          <p className="section-subtitle">
            Diseñado para maximizar la disponibilidad de infraestructura y optimizar presupuestos operativos.
          </p>
        </div>

        <div className="features-grid">
          {businessBenefits.map((benefit, index) => (
            <motion.div 
              key={index}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="feature-card glass-card"
            >
              <div className="feature-icon">{benefit.icon}</div>
              <h3>{benefit.title}</h3>
              <p>{benefit.description}</p>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Features;

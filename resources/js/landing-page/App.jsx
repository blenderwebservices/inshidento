import React from 'react';
import Navbar from './components/Navbar';
import Hero from './components/Hero';
import Workflow from './components/Workflow';
import MultiTenantSection from './components/MultiTenantSection';
import BillingSection from './components/BillingSection';
import AIFeatures from './components/AIFeatures';
import Features from './components/Features';
import CTA from './components/CTA';
import './index.css';

function App() {
  return (
    <div className="app-wrapper">
      <Navbar />
      <main>
        <Hero />
        <Workflow />
        <MultiTenantSection />
        <BillingSection />
        <AIFeatures />
        <Features />
        <CTA />
      </main>
      <footer className="footer-container">
        <div className="container footer-content">
          <div className="footer-brand">
            <span className="footer-logo">Inshidento<span>.ai</span></span>
            <p>Ecosistema Digital Multi-Tenant de Gestión de Incidencias e Infraestructura Empresarial.</p>
          </div>
          <div className="footer-copyright">
            <p>&copy; 2026 Inshidento AI Platform. Todos los derechos reservados.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}

export default App;

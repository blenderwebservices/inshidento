import React from 'react';
import Navbar from './components/Navbar';
import Hero from './components/Hero';
import Features from './components/Features';
import Workflow from './components/Workflow';
import Architecture from './components/Architecture';
import CTA from './components/CTA';
import './index.css';

function App() {
  return (
    <div className="app-wrapper">
      <Navbar />
      <main>
        <Hero />
        <Features />
        <Workflow />
        <Architecture />
        <CTA />
      </main>
      <footer className="container" style={{ padding: '2rem 0', textAlign: 'center', color: 'var(--text-muted)', borderTop: '1px solid var(--glass-border)' }}>
        <p>&copy; 2026 Inshidento AI Platform. Todos los derechos reservados.</p>
      </footer>
    </div>
  );
}

export default App;

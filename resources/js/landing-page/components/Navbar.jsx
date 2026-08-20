import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { ShieldCheck, Menu, X, ArrowRight, Building2, Sparkles, BarChart2 } from 'lucide-react';
import './Navbar.css';

const Navbar = () => {
  const [scrolled, setScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <header className={`navbar-header ${scrolled ? 'scrolled' : ''}`}>
      <div className="container navbar-container">
        <a href="#" className="navbar-logo">
          <div className="logo-icon">
            <ShieldCheck size={26} className="logo-svg" />
          </div>
          <div className="logo-text">
            <span className="logo-title">Inshidento<span className="dot-primary">.ai</span></span>
            <span className="logo-subtitle">Enterprise OS</span>
          </div>
        </a>

        <nav className={`navbar-links ${mobileMenuOpen ? 'active' : ''}`}>
          <a href="#flujo" onClick={() => setMobileMenuOpen(false)}>Flujo End-to-End</a>
          <a href="#empresas" onClick={() => setMobileMenuOpen(false)}>Empresas & Sucursales</a>
          <a href="#fixers" onClick={() => setMobileMenuOpen(false)}>Fixers & Facturación</a>
          <a href="#ia" onClick={() => setMobileMenuOpen(false)}>Inteligencia Artificial</a>
          <a href="#beneficios" onClick={() => setMobileMenuOpen(false)}>Beneficios</a>
        </nav>

        <div className="navbar-actions">
          <a href="/reports/dashboard" className="btn-secondary nav-btn-login">
            <BarChart2 size={16} /> Dashboard Ejecutivo
          </a>
          <a href="/admin" className="btn-secondary nav-btn-login">
            <Building2 size={16} /> Panel Admin
          </a>

          <button 
            className="mobile-menu-btn" 
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            aria-label="Menu"
          >
            {mobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
          </button>
        </div>
      </div>
    </header>
  );
};

export default Navbar;

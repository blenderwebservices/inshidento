import React from 'react';
import { Shield, Menu, X } from 'lucide-react';
import './Navbar.css';

const Navbar = () => {
  const [isOpen, setIsOpen] = React.useState(false);

  return (
    <nav className="navbar glass-card">
      <div className="container nav-container">
        <div className="logo">
          <Shield className="logo-icon" />
          <span className="logo-text">Inshidento</span>
        </div>
        
        <div className={`nav-links ${isOpen ? 'open' : ''}`}>
          <a href="#features" onClick={() => setIsOpen(false)}>Características</a>
          <a href="#workflow" onClick={() => setIsOpen(false)}>Proceso</a>
          <a href="#architecture" onClick={() => setIsOpen(false)}>Tecnología</a>
          <button className="btn-primary">Empezar Ahora</button>
        </div>

        <button className="menu-toggle" onClick={() => setIsOpen(!isOpen)}>
          {isOpen ? <X /> : <Menu />}
        </button>
      </div>
    </nav>
  );
};

export default Navbar;

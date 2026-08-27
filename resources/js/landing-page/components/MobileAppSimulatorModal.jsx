import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { 
  X, Camera, Smartphone, CheckCircle2, ShieldAlert, Sparkles, 
  MapPin, RefreshCw, UploadCloud, Check, Wifi, Battery, AlertCircle, ArrowRight
} from 'lucide-react';
import './MobileAppSimulatorModal.css';

const samplePhotos = [
  {
    url: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
    title: 'Fuga_Tubería_UMA_04.jpg',
    size: '2.4 MB • HD Geotagged'
  },
  {
    url: 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=600&q=80',
    title: 'Tablero_Electrico_Falla.jpg',
    size: '1.8 MB • HD Geotagged'
  }
];

const MobileAppSimulatorModal = ({ isOpen, onClose }) => {
  const [sucursal, setSucursal] = useState('Waldo\'s Monterrey Centro (WAL-001)');
  const [titulo, setTitulo] = useState('Fuga de Agua en UMA-04');
  const [categoria, setCategoria] = useState('HVAC');
  const [prioridad, setPrioridad] = useState('alta');
  const [esEmergencia, setEsEmergencia] = useState(false);
  const [fotoCapturada, setFotoCapturada] = useState(null);
  const [capturando, setCapturando] = useState(false);
  const [enviando, setEnviando] = useState(false);
  const [enviado, setEnviado] = useState(false);
  const [ticketGenerado, setTicketGenerado] = useState(null);

  if (!isOpen) return null;

  const handleCapturarFoto = () => {
    setCapturando(true);
    setTimeout(() => {
      const selected = samplePhotos[Math.floor(Math.random() * samplePhotos.length)];
      setFotoCapturada(selected);
      setCapturando(false);
    }, 600);
  };

  const handleEnviar = (e) => {
    e.preventDefault();
    setEnviando(true);
    setTimeout(() => {
      const randomCode = 'INC-2026-' + Math.floor(1000 + Math.random() * 9000);
      setTicketGenerado(randomCode);
      setEnviando(false);
      setEnviado(true);
    }, 1000);
  };

  const resetForm = () => {
    setEnviado(false);
    setFotoCapturada(null);
    setTitulo('Fuga de Agua en UMA-04');
    setEsEmergencia(false);
  };

  return (
    <AnimatePresence>
      <div className="mobile-modal-overlay">
        <motion.div 
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="modal-backdrop"
          onClick={onClose}
        />

        <motion.div 
          initial={{ opacity: 0, scale: 0.9, y: 20 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.9, y: 20 }}
          transition={{ type: 'spring', damping: 25, stiffness: 300 }}
          className="mobile-modal-container"
        >
          <button className="modal-close-btn" onClick={onClose} aria-label="Cerrar">
            <X size={20} />
          </button>

          {/* Smartphone Frame Outer Shell */}
          <div className="smartphone-shell">
            {/* Notch / Camera Speaker Island */}
            <div className="phone-island">
              <span className="camera-lens"></span>
              <span className="speaker-grille"></span>
            </div>

            {/* Status Bar */}
            <div className="phone-status-bar">
              <span className="status-time">9:41 AM</span>
              <div className="status-icons">
                <Wifi size={13} />
                <span className="network-tag">5G</span>
                <Battery size={15} />
              </div>
            </div>

            {/* Simulated Phone Screen Content */}
            <div className="phone-screen">
              {/* App Top Bar */}
              <div className="app-header-bar">
                <div className="app-brand flex items-center space-x-2">
                  <div className="w-6 h-6 rounded-lg bg-amber-500 text-slate-950 font-black flex items-center justify-center text-xs">
                    W
                  </div>
                  <div>
                    <h4 className="app-name">Inshidento Mobile App</h4>
                    <span className="app-tenant">Waldo's Campo v2.5</span>
                  </div>
                </div>
                <span className="online-indicator">🟢 Online</span>
              </div>

              {!enviado ? (
                <form onSubmit={handleEnviar} className="app-form-body">
                  <div className="app-section-title">
                    <span>📍 Nueva Incidencia en Campo</span>
                  </div>

                  {/* Sucursal selection */}
                  <div className="app-input-group">
                    <label>Sucursal Waldo's</label>
                    <div className="input-with-icon">
                      <MapPin size={15} className="input-icon" />
                      <select value={sucursal} onChange={(e) => setSucursal(e.target.value)}>
                        <option value="Waldo's Monterrey Centro (WAL-001)">Waldo's Monterrey Centro (WAL-001)</option>
                        <option value="Waldo's León Campestre (WAL-006)">Waldo's León Campestre (WAL-006)</option>
                        <option value="Waldo's Mérida Montejo (WAL-016)">Waldo's Mérida Montejo (WAL-016)</option>
                        <option value="Waldo's Satélite (WAL-021)">Waldo's Satélite (WAL-021)</option>
                      </select>
                    </div>
                  </div>

                  {/* Falla Title */}
                  <div className="app-input-group">
                    <label>Título / Síntoma de la Falla</label>
                    <input 
                      type="text" 
                      value={titulo} 
                      onChange={(e) => setTitulo(e.target.value)}
                      placeholder="Ej. Fuga de agua o fallo eléctrico"
                      required
                    />
                  </div>

                  {/* Category & Priority grid */}
                  <div className="app-grid-2">
                    <div className="app-input-group">
                      <label>Disciplina</label>
                      <select value={categoria} onChange={(e) => setCategoria(e.target.value)}>
                        <option value="HVAC">Climatización (HVAC)</option>
                        <option value="Eléctrica">Eléctrica</option>
                        <option value="Plomería">Plomería</option>
                        <option value="Obra Civil">Obra Civil</option>
                        <option value="TI">TI & POS</option>
                      </select>
                    </div>

                    <div className="app-input-group">
                      <label>Prioridad</label>
                      <select value={prioridad} onChange={(e) => setPrioridad(e.target.value)}>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica</option>
                      </select>
                    </div>
                  </div>

                  {/* Switch Emergencia */}
                  <div className="app-switch-card">
                    <div className="flex items-center space-x-2">
                      <input 
                        type="checkbox" 
                        id="sim_emergencia" 
                        checked={esEmergencia}
                        onChange={(e) => setEsEmergencia(e.target.checked)}
                      />
                      <label htmlFor="sim_emergencia" className="text-xs font-bold text-rose-400 cursor-pointer">
                        🚨 Declarar Emergencia Crítica
                      </label>
                    </div>
                  </div>

                  {/* Cámara / Captura de Evidencia Fotográfica */}
                  <div className="app-camera-section">
                    <label className="text-xs font-bold text-slate-300 block mb-1">Evidencia Fotográfica HD</label>
                    
                    {!fotoCapturada ? (
                      <button 
                        type="button" 
                        onClick={handleCapturarFoto}
                        disabled={capturando}
                        className="btn-app-camera"
                      >
                        {capturando ? (
                          <>
                            <RefreshCw size={18} className="animate-spin" />
                            <span>Capturando Evidencia...</span>
                          </>
                        ) : (
                          <>
                            <Camera size={20} />
                            <span>📷 Tomar / Adjuntar Foto de Evidencia</span>
                          </>
                        )}
                      </button>
                    ) : (
                      <div className="foto-preview-box">
                        <img src={fotoCapturada.url} alt="Evidencia Falla" className="preview-img" />
                        <div className="foto-overlay">
                          <span className="foto-title">{fotoCapturada.title}</span>
                          <span className="foto-geotag">📍 Geotagged Lat: 25.6866, Long: -100.3161</span>
                        </div>
                        <button type="button" onClick={handleCapturarFoto} className="btn-retake">
                          <RefreshCw size={13} /> Retomar
                        </button>
                      </div>
                    )}
                  </div>

                  {/* Submit Button */}
                  <button 
                    type="submit" 
                    disabled={enviando}
                    className="btn-app-submit"
                  >
                    {enviando ? (
                      <>
                        <RefreshCw size={18} className="animate-spin" />
                        <span>Transmitiendo Ticket...</span>
                      </>
                    ) : (
                      <>
                        <span>🚀 Registrar Incidencia en App</span>
                        <ArrowRight size={16} />
                      </>
                    )}
                  </button>
                </form>
              ) : (
                /* Success Animated Screen */
                <motion.div 
                  initial={{ opacity: 0, scale: 0.95 }}
                  animate={{ opacity: 1, scale: 1 }}
                  className="app-success-screen"
                >
                  <div className="success-icon-badge">
                    <CheckCircle2 size={44} className="text-emerald-400" />
                  </div>
                  <h3 className="success-title">¡Incidencia Transmitida!</h3>
                  <span className="ticket-badge">{ticketGenerado}</span>

                  <div className="success-details-card">
                    <div className="detail-line">
                      <span>Sucursal:</span>
                      <strong>{sucursal.split(' ')[0]} {sucursal.split(' ')[1]}</strong>
                    </div>
                    <div className="detail-line">
                      <span>Estado:</span>
                      <strong className="text-amber-400">1. Notificada (Triaje)</strong>
                    </div>
                    <div className="detail-line">
                      <span>Evidencia:</span>
                      <strong className="text-emerald-400">✅ Foto HD Geotagged</strong>
                    </div>
                  </div>

                  <p className="success-hint">
                    ⚡ El gestor de operaciones ya recibió la alerta y el algoritmo IA ha iniciado el triaje de asignación.
                  </p>

                  <button onClick={resetForm} className="btn-app-secondary">
                    <span>✨ Probar Otra Incidencia</span>
                  </button>
                </motion.div>
              )}

              {/* Mobile Home Bar Handle */}
              <div className="phone-home-bar"></div>
            </div>
          </div>
        </motion.div>
      </div>
    </AnimatePresence>
  );
};

export default MobileAppSimulatorModal;

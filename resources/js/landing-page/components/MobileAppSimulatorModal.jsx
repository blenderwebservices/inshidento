import React, { useState, useEffect, useRef, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  X, Smartphone, CheckCircle2, Wifi, Battery,
  MapPin, RefreshCw, ArrowRight, Mic, MicOff, Video,
  Image, Trash2, Clock, AlertCircle, History, Plus
} from 'lucide-react';
import './MobileAppSimulatorModal.css';

// ─── Estado de color para badges ────────────────────────────────────────────
const ESTADO_COLOR = {
  registrada:             { bg: '#1d4ed8', text: '#93c5fd' },
  proveedor_asignado:     { bg: '#1e3a5f', text: '#60a5fa' },
  diagnostico_cargado:    { bg: '#1e3a5f', text: '#60a5fa' },
  cotizacion_propuesta:   { bg: '#3b1f6a', text: '#c4b5fd' },
  cotizacion_validada:    { bg: '#3b1f6a', text: '#c4b5fd' },
  oc_emitida:             { bg: '#164e63', text: '#67e8f9' },
  en_ejecucion:           { bg: '#78350f', text: '#fbbf24' },
  entrega_validada:       { bg: '#064e3b', text: '#6ee7b7' },
  proceso_administrativo: { bg: '#0c4a6e', text: '#7dd3fc' },
  cerrada:                { bg: '#14532d', text: '#86efac' },
};
const PRIO_COLOR = { critica: '#ef4444', alta: '#f97316', media: '#f59e0b', baja: '#64748b' };

// ─── Helper: format seconds ──────────────────────────────────────────────────
const fmtTime = (s) => Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');

// ─── WaveBar mini animation ───────────────────────────────────────────────────
const AudioWave = () => (
  <div className="audio-wave">
    {Array.from({ length: 16 }, (_, i) => (
      <div key={i} className="wave-bar" style={{ animationDelay: `${i * 0.07}s` }} />
    ))}
  </div>
);

// ─── History Item ────────────────────────────────────────────────────────────
const HistoryItem = ({ inc }) => {
  const ec = ESTADO_COLOR[inc.estado] || { bg: '#1e293b', text: '#94a3b8' };
  return (
    <div className="history-item" style={{ borderLeftColor: PRIO_COLOR[inc.prioridad] || '#334155' }}>
      <div className="history-header">
        <span className="ticket-code">{inc.codigo_ticket}</span>
        <span className="estado-badge" style={{ background: ec.bg, color: ec.text }}>
          {inc.estado.replace(/_/g, ' ')}
        </span>
      </div>
      <div className="history-titulo">{inc.titulo}</div>
      <div className="history-meta">
        <span>👤 <strong>{inc.notifier?.name || '—'}</strong></span>
        <span>📍 {inc.branch?.nombre || '—'} · <em>{inc.company?.nombre || ''}</em></span>
        <div className="history-footer-row">
          <span>📂 {inc.categoria || '—'}</span>
          <span className="history-time">{inc.created_human}</span>
        </div>
        {inc.media_count > 0 && (
          <span className="media-badge">📎 {inc.media_count} archivo(s): {inc.media_tipos.join(', ')}</span>
        )}
        {inc.es_emergencia && (
          <span className="emergency-badge">🚨 Emergencia Crítica</span>
        )}
      </div>
    </div>
  );
};

// ─── Main Component ──────────────────────────────────────────────────────────
const MobileAppSimulatorModal = ({ isOpen, onClose }) => {
  // Tab state
  const [activeTab, setActiveTab] = useState('report');

  // API Data
  const [users,    setUsers]    = useState([]);
  const [branches, setBranches] = useState([]);
  const [cats,     setCats]     = useState([]);
  const [dataLoaded, setDataLoaded] = useState(false);

  // Form fields
  const [notifierId,  setNotifierId]  = useState('');
  const [branchId,    setBranchId]    = useState('');
  const [titulo,      setTitulo]      = useState('');
  const [descripcion, setDescripcion] = useState('');
  const [catId,       setCatId]       = useState('');
  const [prioridad,   setPrioridad]   = useState('media');
  const [emergencia,  setEmergencia]  = useState(false);
  const [userInfo,    setUserInfo]    = useState(null);

  // Images (up to 10)
  const [imgFiles,    setImgFiles]    = useState([]);
  const [imgPreviews, setImgPreviews] = useState([]);

  // Audio recorder
  const [audioState,   setAudioState]   = useState('idle'); // idle | recording | done
  const [audioBlob,    setAudioBlob]    = useState(null);
  const [audioSecs,    setAudioSecs]    = useState(0);
  const audioRecorder  = useRef(null);
  const audioTimer     = useRef(null);
  const audioChunks    = useRef([]);

  // Video
  const [videoFile,    setVideoFile]    = useState(null);
  const [videoDur,     setVideoDur]     = useState(0);
  const [videoError,   setVideoError]   = useState('');

  // Submission
  const [sending,  setSending]  = useState(false);
  const [sent,     setSent]     = useState(false);
  const [ticket,   setTicket]   = useState('');
  const [mediaCount, setMediaCount] = useState(0);
  const [formError, setFormError] = useState('');

  // History
  const [history,      setHistory]      = useState([]);
  const [historyLoading, setHistoryLoading] = useState(false);

  // Clock
  const [clockStr, setClockStr] = useState('');

  // ── Clock tick ─────────────────────────────────────────────────────────────
  useEffect(() => {
    const tick = () => {
      const now = new Date();
      let h = now.getHours(), m = now.getMinutes();
      const ampm = h >= 12 ? 'PM' : 'AM';
      h = h % 12 || 12;
      setClockStr(`${h}:${String(m).padStart(2, '0')} ${ampm}`);
    };
    tick();
    const iv = setInterval(tick, 30000);
    return () => clearInterval(iv);
  }, []);

  // ── Load API data ───────────────────────────────────────────────────────────
  useEffect(() => {
    if (isOpen && !dataLoaded) { loadData(); }
    
    // Add ESC key support
    const handleEsc = (e) => {
      if (e.key === 'Escape' && isOpen) onClose();
    };
    if (isOpen) window.addEventListener('keydown', handleEsc);
    return () => window.removeEventListener('keydown', handleEsc);
  }, [isOpen]);

  const loadData = async () => {
    try {
      const [uRes, bRes, cRes] = await Promise.all([
        fetch('/api/app/users'),
        fetch('/api/app/branches'),
        fetch('/api/app/categories'),
      ]);
      const [u, b, c] = await Promise.all([uRes.json(), bRes.json(), cRes.json()]);
      setUsers(u);
      setBranches(b);
      setCats(c);
      setDataLoaded(true);
    } catch (e) {
      console.error('MobileAppSimulator loadData error', e);
    }
  };

  // ── User change → auto-fill branch ─────────────────────────────────────────
  const handleUserChange = (uid) => {
    setNotifierId(uid);
    const u = users.find(x => x.id === uid);
    if (u) {
      setUserInfo(u);
      setBranchId(u.branch_id || (u.branch ? u.branch.id : ''));
    } else {
      setUserInfo(null);
      setBranchId('');
    }
  };

  // ── Images ─────────────────────────────────────────────────────────────────
  const handleImages = (e) => {
    const files = Array.from(e.target.files);
    const limited = files.slice(0, 10 - imgFiles.length).slice(0, 10);
    const newFiles = [...imgFiles, ...limited].slice(0, 10);
    setImgFiles(newFiles);
    const previews = [];
    newFiles.forEach(f => {
      const reader = new FileReader();
      reader.onload = ev => {
        previews.push(ev.target.result);
        if (previews.length === newFiles.length) setImgPreviews([...previews]);
      };
      reader.readAsDataURL(f);
    });
    if (newFiles.length === 0) setImgPreviews([]);
  };

  const removeImg = (idx) => {
    const nf = [...imgFiles]; nf.splice(idx, 1);
    const np = [...imgPreviews]; np.splice(idx, 1);
    setImgFiles(nf); setImgPreviews(np);
  };

  // ── Audio recorder ─────────────────────────────────────────────────────────
  const startRecording = async () => {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      audioChunks.current = [];
      const recorder = new MediaRecorder(stream);
      recorder.ondataavailable = e => audioChunks.current.push(e.data);
      recorder.onstop = () => {
        const blob = new Blob(audioChunks.current, { type: 'audio/webm' });
        setAudioBlob(blob);
        setAudioState('done');
        clearInterval(audioTimer.current);
        stream.getTracks().forEach(t => t.stop());
      };
      recorder.start(100);
      audioRecorder.current = recorder;
      setAudioSecs(0);
      setAudioState('recording');
      audioTimer.current = setInterval(() => setAudioSecs(s => s + 1), 1000);
    } catch (e) {
      alert('No se pudo acceder al micrófono: ' + e.message);
    }
  };

  const stopRecording = () => {
    if (audioRecorder.current?.state !== 'inactive') audioRecorder.current?.stop();
    clearInterval(audioTimer.current);
  };

  const removeAudio = () => {
    setAudioBlob(null); setAudioSecs(0); setAudioState('idle');
  };

  // ── Video ───────────────────────────────────────────────────────────────────
  const handleVideo = (e) => {
    const file = e.target.files[0];
    setVideoError(''); setVideoFile(null); setVideoDur(0);
    if (!file) return;
    const url = URL.createObjectURL(file);
    const vid = document.createElement('video');
    vid.preload = 'metadata';
    vid.onloadedmetadata = () => {
      URL.revokeObjectURL(url);
      if (vid.duration > 62) {
        setVideoError(`Video de ${Math.round(vid.duration)}s rechazado. Máximo: 60s.`);
        e.target.value = '';
      } else {
        setVideoFile(file); setVideoDur(Math.round(vid.duration));
      }
    };
    vid.src = url;
  };

  // ── Submit ──────────────────────────────────────────────────────────────────
  const handleSubmit = async (e) => {
    e.preventDefault();
    setFormError('');

    if (!notifierId || !branchId || !titulo.trim() || !catId) {
      setFormError('Completa los campos obligatorios: usuario, sucursal, título y categoría.');
      return;
    }

    setSending(true);
    const fd = new FormData();
    fd.append('notifier_id',   notifierId);
    fd.append('branch_id',     branchId);
    fd.append('titulo',        titulo.trim());
    fd.append('descripcion',   descripcion.trim() || 'Reportada desde App Móvil');
    fd.append('categoria_id',  catId);
    fd.append('prioridad',     prioridad);
    fd.append('es_emergencia', emergencia ? '1' : '0');

    imgFiles.forEach(f => fd.append('imagenes[]', f));

    if (audioBlob) {
      fd.append('audio', audioBlob, 'audio_' + Date.now() + '.webm');
    }
    if (videoFile) {
      fd.append('video', videoFile);
      fd.append('video_duracion', videoDur);
    }

    // CSRF
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const headers  = csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {};

    try {
      const res  = await fetch('/api/app/incidents', { method: 'POST', body: fd, headers, credentials: 'include' });
      const data = await res.json();

      if (!res.ok || !data.success) throw new Error(data.message || 'Error del servidor.');

      setTicket(data.codigo_ticket);
      setMediaCount(data.media_count || 0);
      setSent(true);
    } catch (err) {
      setFormError('❌ ' + (err.message || 'Error de conexión. Intenta de nuevo.'));
    } finally {
      setSending(false);
    }
  };

  // ── Reset ───────────────────────────────────────────────────────────────────
  const resetForm = () => {
    setSent(false); setTicket(''); setFormError('');
    setTitulo(''); setDescripcion(''); setEmergencia(false);
    setNotifierId(''); setBranchId(''); setCatId(''); setPrioridad('media');
    setImgFiles([]); setImgPreviews([]);
    setAudioBlob(null); setAudioSecs(0); setAudioState('idle');
    setVideoFile(null); setVideoDur(0); setVideoError('');
    setUserInfo(null);
  };

  // ── Load history ─────────────────────────────────────────────────────────────
  const loadHistory = useCallback(async () => {
    setHistoryLoading(true);
    try {
      const qs = branchId ? `?branch_id=${branchId}` : '';
      const res  = await fetch(`/api/app/incidents${qs}`, { credentials: 'include' });
      const data = await res.json();
      setHistory(data);
    } catch {
      setHistory([]);
    } finally {
      setHistoryLoading(false);
    }
  }, [branchId]);

  useEffect(() => {
    if (activeTab === 'history' && isOpen) { loadHistory(); }
  }, [activeTab, isOpen, branchId, loadHistory]);

  if (!isOpen) return null;

  const selectedUser   = users.find(u => u.id === notifierId);
  const selectedBranch = branches.find(b => b.id === branchId);

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
          initial={{ opacity: 0, scale: 0.9, y: 24 }}
          animate={{ opacity: 1, scale: 1, y: 0 }}
          exit={{ opacity: 0, scale: 0.9, y: 24 }}
          transition={{ type: 'spring', damping: 26, stiffness: 300 }}
          className="mobile-modal-container"
        >
          <button className="modal-close-btn" onClick={onClose} aria-label="Cerrar">
            <X size={20} />
          </button>

          {/* Smartphone Frame */}
          <div className="smartphone-shell">
            {/* Dynamic Island */}
            <div className="phone-island">
              <span className="camera-lens" />
              <span className="speaker-grille" />
            </div>

            {/* Status Bar */}
            <div className="phone-status-bar">
              <span className="status-time">{clockStr || '9:41 AM'}</span>
              <div className="status-icons">
                <Wifi size={12} />
                <span className="network-tag">5G</span>
                <Battery size={14} />
              </div>
            </div>

            {/* App Header */}
            <div className="app-header-bar">
              <div className="app-brand">
                <div className="app-logo-dot">W</div>
                <div>
                  <h4 className="app-name">Inshidento App</h4>
                  <span className="app-tenant">Campo v2.5</span>
                </div>
              </div>
              <span className="online-indicator">🟢 Online</span>
            </div>

            {/* Tab Bar */}
            <div className="app-tab-bar">
              <button
                onClick={() => setActiveTab('report')}
                className={`app-tab ${activeTab === 'report' ? 'app-tab--active' : ''}`}
              >
                📍 Reportar
              </button>
              <button
                onClick={() => setActiveTab('history')}
                className={`app-tab ${activeTab === 'history' ? 'app-tab--active' : ''}`}
              >
                📋 Historial
              </button>
            </div>

            {/* Screen */}
            <div className="phone-screen">

              {/* ─── TAB: REPORT ─────────────────────────────────────── */}
              {activeTab === 'report' && (
                sent ? (
                  /* Success screen */
                  <motion.div
                    initial={{ opacity: 0, scale: 0.95 }}
                    animate={{ opacity: 1, scale: 1 }}
                    className="app-success-screen"
                  >
                    <div className="success-icon-badge">
                      <CheckCircle2 size={44} className="text-emerald" />
                    </div>
                    <h3 className="success-title">¡Incidencia Registrada!</h3>
                    <span className="ticket-badge">{ticket}</span>

                    <div className="success-details-card">
                      <div className="detail-line"><span>Ticket:</span>       <strong className="amber">{ticket}</strong></div>
                      {selectedUser && <div className="detail-line"><span>Usuario:</span><strong>{selectedUser.name}</strong></div>}
                      {selectedBranch && <div className="detail-line"><span>Sucursal:</span><strong>{selectedBranch.nombre}</strong></div>}
                      {selectedBranch?.company && <div className="detail-line"><span>Empresa:</span><strong>{selectedBranch.company.nombre}</strong></div>}
                      <div className="detail-line"><span>Estado:</span>       <strong className="amber">1. Registrada</strong></div>
                      {mediaCount > 0 && <div className="detail-line"><span>Multimedia:</span><strong className="emerald">{mediaCount} archivo(s)</strong></div>}
                    </div>

                    <p className="success-hint">
                      ⚡ El gestor de operaciones recibió la alerta. Puedes consultar el historial completo en la pestaña <strong>Historial</strong>.
                    </p>

                    <div className="success-actions">
                      <button onClick={resetForm} className="btn-app-secondary">✨ Nuevo Reporte</button>
                      <button onClick={() => setActiveTab('history')} className="btn-app-history">📋 Historial</button>
                    </div>
                  </motion.div>
                ) : (
                  /* Form */
                  <form onSubmit={handleSubmit} className="app-form-body">
                    <div className="app-section-title">📍 Nueva Incidencia en Campo</div>

                    {/* Usuario */}
                    <div className="app-input-group">
                      <label>👤 Usuario Notificador</label>
                      <select value={notifierId} onChange={e => handleUserChange(e.target.value)}>
                        <option value="">— Seleccionar usuario —</option>
                        {users.map(u => (
                          <option key={u.id} value={u.id}>
                            {u.name} · {u.branch?.nombre || 'Sin sucursal'}
                          </option>
                        ))}
                      </select>
                      {userInfo && (
                        <div className="user-info-chip">
                          <MapPin size={11} />
                          <span>{userInfo.branch?.nombre || 'Sin sucursal'}</span>
                          {userInfo.company && <span className="chip-dot">·</span>}
                          {userInfo.company && <span className="chip-company">{userInfo.company.nombre}</span>}
                        </div>
                      )}
                    </div>

                    {/* Sucursal */}
                    <div className="app-input-group">
                      <label><MapPin size={11} style={{ display: 'inline', verticalAlign: 'middle' }} /> Sucursal (Asignada automáticamente)</label>
                      <select value={branchId} disabled className="opacity-75 cursor-not-allowed">
                        <option value="">— Seleccionar sucursal —</option>
                        {branches.map(b => (
                          <option key={b.id} value={b.id}>
                            {b.nombre} ({b.zona}) — {b.company?.nombre || ''}
                          </option>
                        ))}
                      </select>
                    </div>

                    {/* Título */}
                    <div className="app-input-group">
                      <label>📝 Título / Síntoma</label>
                      <input
                        type="text"
                        value={titulo}
                        onChange={e => setTitulo(e.target.value)}
                        placeholder="ej. Fuga de agua, falla eléctrica..."
                        required
                      />
                    </div>

                    {/* Cat + Prioridad */}
                    <div className="app-grid-2">
                      <div className="app-input-group">
                        <label>Disciplina</label>
                        <select value={catId} onChange={e => setCatId(e.target.value)}>
                          <option value="">Categoría...</option>
                          {cats.map(c => <option key={c.id} value={c.id}>{c.nombre}</option>)}
                        </select>
                      </div>
                      <div className="app-input-group">
                        <label>Prioridad</label>
                        <select value={prioridad} onChange={e => setPrioridad(e.target.value)}>
                          <option value="baja">Baja</option>
                          <option value="media">Media</option>
                          <option value="alta">Alta</option>
                          <option value="critica">Crítica</option>
                        </select>
                      </div>
                    </div>

                    {/* Descripción */}
                    <div className="app-input-group">
                      <label>Descripción</label>
                      <textarea
                        value={descripcion}
                        onChange={e => setDescripcion(e.target.value)}
                        rows={2}
                        placeholder="Detalles adicionales de la falla..."
                      />
                    </div>

                    {/* Emergencia */}
                    <div className="app-switch-card">
                      <label className="app-switch-label">
                        <input
                          type="checkbox"
                          checked={emergencia}
                          onChange={e => setEmergencia(e.target.checked)}
                        />
                        <span>🚨 Declarar Emergencia Crítica</span>
                      </label>
                    </div>

                    {/* ── Multimedia ── */}
                    <div className="multimedia-section">
                      <div className="multimedia-title">📎 Multimedia</div>

                      {/* Images */}
                      <label className="btn-app-camera">
                        <Image size={16} />
                        <span>Fotos {imgFiles.length > 0 ? `(${imgFiles.length}/10)` : '— hasta 10'}</span>
                        <input
                          type="file"
                          multiple
                          accept="image/*"
                          style={{ display: 'none' }}
                          onChange={handleImages}
                          disabled={imgFiles.length >= 10}
                        />
                      </label>

                      {/* Image grid (WhatsApp style) */}
                      {imgPreviews.length > 0 && (
                        <div className="img-grid">
                          {imgPreviews.map((src, i) => (
                            <div key={i} className="img-thumb">
                              <img src={src} alt={`foto-${i + 1}`} />
                              <button type="button" className="img-remove" onClick={() => removeImg(i)}>
                                <X size={9} />
                              </button>
                            </div>
                          ))}
                          {imgFiles.length < 10 && (
                            <label className="img-add-more">
                              <Plus size={16} />
                              <input type="file" multiple accept="image/*" style={{ display: 'none' }} onChange={handleImages} />
                            </label>
                          )}
                        </div>
                      )}

                      {/* Audio recorder */}
                      {audioState === 'idle' && (
                        <button type="button" className="btn-audio" onClick={startRecording}>
                          <Mic size={15} /> Grabar Audio
                        </button>
                      )}
                      {audioState === 'recording' && (
                        <div className="audio-recording-card">
                          <div className="audio-rec-header">
                            <span className="rec-dot" />
                            <span>Grabando... {fmtTime(audioSecs)}</span>
                            <button type="button" onClick={stopRecording} className="btn-stop-audio">
                              <MicOff size={13} /> Detener
                            </button>
                          </div>
                          <AudioWave />
                        </div>
                      )}
                      {audioState === 'done' && audioBlob && (
                        <div className="audio-done-card">
                          <Mic size={14} />
                          <span>Audio grabado · {fmtTime(audioSecs)}</span>
                          <button type="button" onClick={removeAudio} className="btn-remove-media">
                            <Trash2 size={12} />
                          </button>
                        </div>
                      )}

                      {/* Video */}
                      <label className="btn-video">
                        <Video size={15} />
                        <span>Video {videoFile ? `· ${fmtTime(videoDur)}` : '≤ 1 min'}</span>
                        <input type="file" accept="video/*" style={{ display: 'none' }} onChange={handleVideo} />
                      </label>
                      {videoError && <p className="media-error">{videoError}</p>}
                      {videoFile && (
                        <div className="video-done-card">
                          <Video size={14} />
                          <span>{videoFile.name.slice(0, 26)}... · {fmtTime(videoDur)}</span>
                          <button type="button" onClick={() => { setVideoFile(null); setVideoDur(0); }} className="btn-remove-media">
                            <Trash2 size={12} />
                          </button>
                        </div>
                      )}
                    </div>

                    {/* Error */}
                    {formError && (
                      <div className="form-error-card">
                        <AlertCircle size={14} />
                        <span>{formError}</span>
                      </div>
                    )}

                    {/* Submit */}
                    <button type="submit" disabled={sending} className="btn-app-submit">
                      {sending ? (
                        <><RefreshCw size={16} className="spin" /><span>Transmitiendo...</span></>
                      ) : (
                        <><span>🚀 Registrar Incidencia</span><ArrowRight size={15} /></>
                      )}
                    </button>
                  </form>
                )
              )}

              {/* ─── TAB: HISTORY ─────────────────────────────────────── */}
              {activeTab === 'history' && (
                <div className="history-panel">
                  <div className="history-toolbar">
                    <span className="history-label">
                      {selectedBranch ? `Historial: ${selectedBranch.nombre}` : 'Últimas incidencias'}
                    </span>
                    <button onClick={loadHistory} className="btn-refresh" title="Actualizar">
                      <RefreshCw size={13} className={historyLoading ? 'spin' : ''} />
                    </button>
                  </div>
                  {historyLoading ? (
                    <div className="history-loading">
                      <RefreshCw size={20} className="spin" /> Cargando...
                    </div>
                  ) : history.length === 0 ? (
                    <div className="history-empty">No hay incidencias registradas aún.</div>
                  ) : (
                    <div className="history-list">
                      {history.map(inc => <HistoryItem key={inc.id} inc={inc} />)}
                    </div>
                  )}
                </div>
              )}
            </div>

            {/* Bottom Home Indicator */}
            <div className="phone-home-bar" onClick={onClose} style={{cursor: 'pointer'}} title="Cerrar App" />
          </div>
        </motion.div>
        
        {/* Floating Close Button for Small Screens */}
        <button 
          onClick={onClose}
          style={{
            position: 'absolute',
            bottom: '20px',
            background: 'rgba(239, 68, 68, 0.9)',
            color: '#fff',
            border: 'none',
            padding: '10px 20px',
            borderRadius: '20px',
            fontWeight: 'bold',
            boxShadow: '0 4px 12px rgba(0,0,0,0.5)',
            zIndex: 100,
            cursor: 'pointer'
          }}
        >
          Cerrar Simulador (Esc)
        </button>
      </div>
    </AnimatePresence>
  );
};

export default MobileAppSimulatorModal;

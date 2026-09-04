{{-- ─────────────────────────────────────────────────────────────────────────
     MOBILE APP FAB MODAL — partial Blade
     Incluido desde layouts/app.blade.php
     Conectado a: /api/app/* (MobileAppController)
───────────────────────────────────────────────────────────────────────── --}}

{{-- ── FAB Button ──────────────────────────────────────────────────────────── --}}
<button
  id="fab-mobile-app"
  onclick="fabModalOpen()"
  title="Reportar Incidencia — App Móvil"
  style="
    position:fixed; bottom:28px; right:28px; z-index:9800;
    width:60px; height:60px; border-radius:50%;
    background:linear-gradient(135deg,#f59e0b,#d97706);
    border:none; cursor:pointer;
    box-shadow:0 8px 30px rgba(245,158,11,.55);
    display:flex; align-items:center; justify-content:center;
    transition:transform .2s, box-shadow .2s;
  "
  onmouseenter="this.style.transform='scale(1.12)'; this.style.boxShadow='0 12px 40px rgba(245,158,11,.7)'"
  onmouseleave="this.style.transform='scale(1)';   this.style.boxShadow='0 8px 30px rgba(245,158,11,.55)'"
>
  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
    <line x1="12" y1="18" x2="12" y2="18.01"/>
  </svg>
  {{-- Pulse ring --}}
  <span style="position:absolute;width:60px;height:60px;border-radius:50%;border:2px solid rgba(245,158,11,.5);animation:fabPulse 2s infinite;pointer-events:none;"></span>
</button>

{{-- ── Modal Overlay ───────────────────────────────────────────────────────── --}}
<div id="fab-modal-overlay" onclick="if(event.target===this)fabModalClose()" style="
  display:none; position:fixed; inset:0; z-index:9900;
  background:rgba(2,6,23,.82); backdrop-filter:blur(14px);
  align-items:center; justify-content:center; padding:1rem;
">
  <div id="fab-modal-box" style="
    position:relative; width:100%; max-width:400px;
    background:#090d16; border-radius:44px;
    border:10px solid #1e293b;
    box-shadow:0 25px 80px -10px rgba(0,0,0,.95), 0 0 0 2px rgba(255,255,255,.12);
    overflow:hidden; max-height:90vh; display:flex; flex-direction:column;
    transform:scale(.88) translateY(24px); transition:transform .3s cubic-bezier(.34,1.56,.64,1), opacity .25s;
    opacity:0;
  ">
    {{-- Close btn --}}
    <button onclick="fabModalClose()" style="
      position:absolute;top:12px;right:14px;z-index:10;
      width:32px;height:32px;border-radius:50%;
      background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);
      color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;
      font-size:18px;line-height:1;transition:background .2s, transform .2s;
    " onmouseenter="this.style.background='rgba(239,68,68,.8)';this.style.transform='rotate(90deg)'"
       onmouseleave="this.style.background='rgba(255,255,255,.12)';this.style.transform='rotate(0)'">✕</button>

    {{-- Dynamic Island --}}
    <div style="position:absolute;top:8px;left:50%;transform:translateX(-50%);
      width:100px;height:20px;background:#020617;border-radius:18px;z-index:9;"></div>

    {{-- Status bar --}}
    <div id="fab-status-bar" style="height:34px;padding:10px 20px 0;display:flex;
      align-items:center;justify-content:space-between;font-size:.72rem;font-weight:700;color:#94a3b8;">
      <span id="fab-clock">9:41 AM</span>
      <span style="display:flex;align-items:center;gap:5px;font-size:.7rem;">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><circle cx="12" cy="20" r="1"/></svg>
        5G
        <svg width="15" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="18" height="11" rx="2"/><path d="M22 11v3"/></svg>
      </span>
    </div>

    {{-- App header --}}
    <div style="padding:10px 16px;background:rgba(30,41,59,.95);border-bottom:1px solid rgba(255,255,255,.08);
      display:flex;align-items:center;justify-content:space-between;">
      <div style="display:flex;align-items:center;gap:8px;">
        <div style="width:24px;height:24px;border-radius:8px;background:#f59e0b;
          display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.7rem;color:#0f172a;">W</div>
        <div>
          <div style="font-size:.8rem;font-weight:800;color:#fff;line-height:1.1;">Inshidento App</div>
          <div style="font-size:.65rem;color:#f59e0b;font-weight:600;">Panel Ejecutivo v2.5</div>
        </div>
      </div>
      <span style="font-size:.65rem;font-weight:700;color:#10b981;">🟢 Online</span>
    </div>

    {{-- Tab bar --}}
    <div style="display:flex;background:#0f172a;border-bottom:1px solid rgba(255,255,255,.08);">
      <button onclick="fabTab('report')" id="fab-tab-report" style="
        flex:1;padding:9px 0;font-size:.72rem;font-weight:800;color:#f59e0b;
        background:rgba(245,158,11,.12);border:none;border-bottom:2px solid #f59e0b;cursor:pointer;transition:all .2s;">
        📍 Reportar
      </button>
      <button onclick="fabTab('history')" id="fab-tab-history" style="
        flex:1;padding:9px 0;font-size:.72rem;font-weight:800;color:#64748b;
        background:transparent;border:none;border-bottom:2px solid transparent;cursor:pointer;transition:all .2s;">
        📋 Historial
      </button>
    </div>

    {{-- Scrollable body --}}
    <div style="flex:1;overflow-y:auto;background:#090d16;" id="fab-body">

      {{-- ── TAB: REPORT ───────────────────────────────────────────────────── --}}
      <div id="fab-panel-report" style="padding:14px 16px 20px;display:flex;flex-direction:column;gap:11px;">

        <div id="fab-success-screen" style="display:none;flex-direction:column;align-items:center;
          text-align:center;gap:10px;padding:10px 0 20px;">
          <div style="width:60px;height:60px;border-radius:50%;background:rgba(16,185,129,.15);
            border:2px solid #10b981;display:flex;align-items:center;justify-content:center;font-size:2rem;">✅</div>
          <h3 style="color:#fff;font-size:1rem;font-weight:900;margin:0;">¡Incidencia Registrada!</h3>
          <span id="fab-ticket-badge" style="font-size:.9rem;font-weight:900;color:#fbbf24;
            background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);
            padding:4px 14px;border-radius:20px;"></span>
          <div id="fab-success-details" style="width:100%;background:#1e293b;border:1px solid #334155;
            border-radius:10px;padding:10px;font-size:.75rem;color:#cbd5e1;text-align:left;
            display:flex;flex-direction:column;gap:5px;"></div>
          <p style="font-size:.7rem;color:#64748b;line-height:1.4;">
            ⚡ El gestor de operaciones ya recibió la alerta. Puedes ver el historial completo en la pestaña <strong style="color:#fff;">Historial</strong>.
          </p>
          <div style="display:flex;gap:8px;width:100%;">
            <button onclick="fabResetForm()" style="flex:1;padding:9px;background:#1e293b;color:#fff;border:1px solid #334155;
              border-radius:10px;font-size:.78rem;font-weight:700;cursor:pointer;">✨ Nuevo Reporte</button>
            <button onclick="fabTab('history')" style="flex:1;padding:9px;background:rgba(245,158,11,.15);color:#fbbf24;border:1px solid rgba(245,158,11,.3);
              border-radius:10px;font-size:.78rem;font-weight:700;cursor:pointer;">📋 Ver Historial</button>
          </div>
        </div>

        <div id="fab-form-wrapper">
          {{-- Usuario --}}
          <div style="display:flex;flex-direction:column;gap:3px;">
            <label style="font-size:.68rem;font-weight:700;color:#cbd5e1;text-transform:uppercase;">👤 Usuario Notificador</label>
            <select id="fab-user-id" onchange="fabOnUserChange()" style="width:100%;background:#1e293b;border:1px solid #334155;
              color:#fff;font-size:.78rem;padding:8px 10px;border-radius:10px;outline:none;">
              <option value="">Cargando usuarios...</option>
            </select>
            <div id="fab-user-info" style="font-size:.68rem;color:#64748b;padding:2px 0;min-height:16px;"></div>
          </div>

          {{-- Sucursal --}}
          <div style="display:flex;flex-direction:column;gap:3px;">
            <label style="font-size:.68rem;font-weight:700;color:#cbd5e1;text-transform:uppercase;">📍 Sucursal (Asignada automáticamente)</label>
            <select id="fab-branch-id" disabled style="width:100%;background:#1e293b;border:1px solid #334155;
              color:#94a3b8;font-size:.78rem;padding:8px 10px;border-radius:10px;outline:none;cursor:not-allowed;">
              <option value="">Cargando sucursales...</option>
            </select>
          </div>

          {{-- Título + Categoría + Prioridad --}}
          <div style="display:flex;flex-direction:column;gap:3px;">
            <label style="font-size:.68rem;font-weight:700;color:#cbd5e1;text-transform:uppercase;">📝 Título / Síntoma</label>
            <input id="fab-titulo" type="text" placeholder="ej. Fuga de agua, falla eléctrica..."
              style="width:100%;background:#1e293b;border:1px solid #334155;color:#fff;font-size:.78rem;
              padding:8px 10px;border-radius:10px;outline:none;box-sizing:border-box;">
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div style="display:flex;flex-direction:column;gap:3px;">
              <label style="font-size:.68rem;font-weight:700;color:#cbd5e1;text-transform:uppercase;">Disciplina</label>
              <select id="fab-cat-id" style="width:100%;background:#1e293b;border:1px solid #334155;
                color:#fff;font-size:.75rem;padding:8px 8px;border-radius:10px;outline:none;">
                <option value="">Categoría...</option>
              </select>
            </div>
            <div style="display:flex;flex-direction:column;gap:3px;">
              <label style="font-size:.68rem;font-weight:700;color:#cbd5e1;text-transform:uppercase;">Prioridad</label>
              <select id="fab-prioridad" style="width:100%;background:#1e293b;border:1px solid #334155;
                color:#fff;font-size:.75rem;padding:8px 8px;border-radius:10px;outline:none;">
                <option value="baja">Baja</option>
                <option value="media" selected>Media</option>
                <option value="alta">Alta</option>
                <option value="critica">Crítica</option>
              </select>
            </div>
          </div>

          {{-- Emergencia toggle --}}
          <div style="background:rgba(244,63,94,.1);border:1px solid rgba(244,63,94,.3);padding:8px 10px;border-radius:10px;">
            <label style="display:flex;align-items:center;gap:7px;cursor:pointer;">
              <input type="checkbox" id="fab-emergencia" style="width:14px;height:14px;">
              <span style="font-size:.75rem;font-weight:800;color:#f87171;">🚨 Declarar Emergencia Crítica</span>
            </label>
          </div>

          {{-- Descripción breve --}}
          <div style="display:flex;flex-direction:column;gap:3px;">
            <label style="font-size:.68rem;font-weight:700;color:#cbd5e1;text-transform:uppercase;">Descripción</label>
            <textarea id="fab-descripcion" rows="2" placeholder="Detalles de la falla..."
              style="width:100%;background:#1e293b;border:1px solid #334155;color:#fff;font-size:.76rem;
              padding:8px 10px;border-radius:10px;outline:none;resize:none;box-sizing:border-box;"></textarea>
          </div>

          {{-- ── Multimedia ───────────────────────────────────────────────── --}}
          <div>
            <div style="font-size:.68rem;font-weight:800;color:#3b82f6;text-transform:uppercase;letter-spacing:.05em;margin-bottom:7px;">
              📎 Multimedia (hasta 10 fotos · audio · video ≤1min)
            </div>

            {{-- Imágenes --}}
            <label id="fab-img-btn" style="
              display:flex;align-items:center;justify-content:center;gap:7px;
              width:100%;padding:10px;
              background:linear-gradient(135deg,#1e293b,#0f172a);
              border:2px dashed #f59e0b;border-radius:10px;
              color:#fbbf24;font-size:.78rem;font-weight:800;cursor:pointer;">
              📷 Agregar Fotos (max 10)
              <input type="file" id="fab-imgs" multiple accept="image/*" style="display:none" onchange="fabImgPreview(this)">
            </label>
            <div id="fab-img-preview" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:6px;"></div>
            <div id="fab-img-count" style="font-size:.65rem;color:#64748b;margin-top:2px;"></div>

            {{-- Audio Recorder --}}
            <div style="margin-top:8px;">
              <div id="fab-audio-idle" style="display:flex;gap:6px;">
                <button type="button" onclick="fabAudioStart()" style="
                  flex:1;padding:8px;background:#1e293b;border:1px solid #334155;border-radius:10px;
                  color:#a78bfa;font-size:.75rem;font-weight:700;cursor:pointer;
                  display:flex;align-items:center;justify-content:center;gap:5px;">
                  🎙️ Grabar Audio
                </button>
              </div>
              <div id="fab-audio-recording" style="display:none;background:rgba(167,139,250,.1);
                border:1px solid rgba(167,139,250,.3);border-radius:10px;padding:8px 10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                  <span style="font-size:.72rem;font-weight:700;color:#a78bfa;">⏺ Grabando... <span id="fab-audio-timer">0:00</span></span>
                  <button type="button" onclick="fabAudioStop()" style="padding:4px 10px;background:#ef4444;border:none;
                    border-radius:7px;color:#fff;font-size:.7rem;font-weight:700;cursor:pointer;">⏹ Detener</button>
                </div>
                <div id="fab-audio-wave" style="height:24px;margin-top:6px;display:flex;align-items:center;gap:2px;"></div>
              </div>
              <div id="fab-audio-preview" style="display:none;margin-top:6px;">
                <div style="display:flex;align-items:center;gap:6px;background:#1e293b;border:1px solid #334155;
                  border-radius:10px;padding:7px 10px;">
                  <span style="font-size:.72rem;color:#a78bfa;font-weight:700;">🎙️ Audio grabado</span>
                  <span id="fab-audio-dur" style="font-size:.68rem;color:#64748b;margin-left:auto;"></span>
                  <button type="button" onclick="fabAudioRemove()" style="padding:2px 7px;background:#334155;border:none;
                    border-radius:6px;color:#94a3b8;font-size:.65rem;cursor:pointer;">✕</button>
                </div>
              </div>
            </div>

            {{-- Video --}}
            <div style="margin-top:8px;">
              <label id="fab-vid-btn" style="
                display:flex;align-items:center;justify-content:center;gap:7px;
                width:100%;padding:8px;
                background:#1e293b;border:1px solid #334155;border-radius:10px;
                color:#34d399;font-size:.75rem;font-weight:700;cursor:pointer;">
                🎬 Agregar Video (≤ 1 min)
                <input type="file" id="fab-video-file" accept="video/*" style="display:none" onchange="fabVideoPreview(this)">
              </label>
              <div id="fab-video-preview" style="display:none;margin-top:6px;"></div>
              <div id="fab-video-error" style="font-size:.65rem;color:#f87171;margin-top:3px;"></div>
            </div>
          </div>

          {{-- Error / Sending state --}}
          <div id="fab-error-msg" style="display:none;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);
            border-radius:9px;padding:8px;font-size:.72rem;color:#fca5a5;"></div>

          {{-- Submit --}}
          <button type="button" onclick="fabSubmit()" id="fab-submit-btn" style="
            width:100%;padding:12px;
            background:linear-gradient(135deg,#f59e0b,#d97706);
            color:#0f172a;border:none;border-radius:12px;
            font-size:.85rem;font-weight:900;cursor:pointer;
            box-shadow:0 4px 15px rgba(245,158,11,.35);
            display:flex;align-items:center;justify-content:center;gap:7px;
            transition:transform .15s, box-shadow .15s;"
            onmouseenter="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 25px rgba(245,158,11,.45)'"
            onmouseleave="this.style.transform='';this.style.boxShadow='0 4px 15px rgba(245,158,11,.35)'">
            🚀 Registrar Incidencia
          </button>
        </div>{{-- /form-wrapper --}}
      </div>{{-- /panel-report --}}

      {{-- ── TAB: HISTORY ──────────────────────────────────────────────────── --}}
      <div id="fab-panel-history" style="display:none;padding:12px 14px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
          <span id="fab-history-label" style="font-size:.72rem;font-weight:800;color:#94a3b8;text-transform:uppercase;">Últimas incidencias reportadas</span>
          <button onclick="fabLoadHistory()" style="font-size:.65rem;color:#38bdf8;background:transparent;border:none;cursor:pointer;font-weight:700;">🔄 Actualizar</button>
        </div>
        <div id="fab-history-list" style="display:flex;flex-direction:column;gap:8px;">
          <div style="text-align:center;color:#475569;font-size:.75rem;padding:20px 0;">Cargando historial...</div>
        </div>
      </div>

    </div>{{-- /fab-body --}}

    {{-- Home bar --}}
    <div style="width:100px;height:4px;background:#334155;border-radius:4px;margin:10px auto 8px;"></div>
  </div>{{-- /fab-modal-box --}}
</div>{{-- /fab-modal-overlay --}}

<style>
@keyframes fabPulse {
  0%   { transform: scale(1);   opacity: .7; }
  70%  { transform: scale(1.45); opacity: 0; }
  100% { transform: scale(1.45); opacity: 0; }
}
</style>

<script>
/* ─── FAB Modal state ─────────────────────────────────────────── */
let fabAudioRecorder = null, fabAudioChunks = [], fabAudioBlob = null;
let fabAudioTimer = null, fabAudioSecs = 0;
let fabImgFiles = [];
let fabVideoFile = null, fabVideoDuracion = 0;
let fabDataLoaded = false;

const fabOverlay  = () => document.getElementById('fab-modal-overlay');
const fabBox      = () => document.getElementById('fab-modal-box');

function fabModalOpen() {
  const ov = fabOverlay();
  ov.style.display = 'flex';
  setTimeout(() => {
    fabBox().style.opacity    = '1';
    fabBox().style.transform  = 'scale(1) translateY(0)';
  }, 10);
  fabStartClock();
  if (!fabDataLoaded) { fabLoadData(); }
}
function fabModalClose() {
  fabBox().style.opacity   = '0';
  fabBox().style.transform = 'scale(.88) translateY(24px)';
  setTimeout(() => { fabOverlay().style.display = 'none'; }, 280);
}

/* ─── Clock ──────────────────────────────────────────────────── */
function fabStartClock() {
  const el = document.getElementById('fab-clock');
  if (!el) return;
  const tick = () => {
    const now = new Date();
    let h = now.getHours(), m = now.getMinutes();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    el.textContent = h + ':' + String(m).padStart(2,'0') + ' ' + ampm;
  };
  tick(); setInterval(tick, 30000);
}

/* ─── Tabs ───────────────────────────────────────────────────── */
function fabTab(tab) {
  const isReport = tab === 'report';
  document.getElementById('fab-panel-report').style.display  = isReport ? 'flex' : 'none';
  document.getElementById('fab-panel-history').style.display = isReport ? 'none' : 'block';
  document.getElementById('fab-panel-report').style.flexDirection = 'column';

  const tReport  = document.getElementById('fab-tab-report');
  const tHistory = document.getElementById('fab-tab-history');
  tReport.style.color            = isReport ? '#f59e0b' : '#64748b';
  tReport.style.background       = isReport ? 'rgba(245,158,11,.12)' : 'transparent';
  tReport.style.borderBottomColor= isReport ? '#f59e0b' : 'transparent';
  tHistory.style.color            = isReport ? '#64748b' : '#f59e0b';
  tHistory.style.background       = isReport ? 'transparent' : 'rgba(245,158,11,.12)';
  tHistory.style.borderBottomColor= isReport ? 'transparent' : '#f59e0b';

  if (!isReport) { fabLoadHistory(); }
}

/* ─── Load data from API ─────────────────────────────────────── */
async function fabLoadData() {
  try {
    const [usersRes, branchesRes, catsRes] = await Promise.all([
      fetch('/api/app/users'),
      fetch('/api/app/branches'),
      fetch('/api/app/categories'),
    ]);
    const users    = await usersRes.json();
    const branches = await branchesRes.json();
    const cats     = await catsRes.json();

    const uSel = document.getElementById('fab-user-id');
    uSel.innerHTML = '<option value="">— Selecciona usuario —</option>'
      + users.map(u => `<option value="${u.id}" data-branch="${u.branch_id||''}" 
          data-branch-name="${u.branch?.nombre||''}" data-company="${u.company?.nombre||''}"
          >${u.name} · ${u.branch?.nombre||'Sin sucursal'}</option>`).join('');

    const bSel = document.getElementById('fab-branch-id');
    bSel.innerHTML = '<option value="">— Selecciona sucursal —</option>'
      + branches.map(b => `<option value="${b.id}">${b.nombre} (${b.zona}) — ${b.company?.nombre||''}</option>`).join('');

    const cSel = document.getElementById('fab-cat-id');
    cSel.innerHTML = '<option value="">Categoría...</option>'
      + cats.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');

    fabDataLoaded = true;
  } catch(e) {
    console.error('FAB loadData error', e);
  }
}

function fabOnUserChange() {
  const sel = document.getElementById('fab-user-id');
  const opt = sel.selectedOptions[0];
  const info = document.getElementById('fab-user-info');
  const bSel = document.getElementById('fab-branch-id');
  if (!opt || !opt.value) { info.textContent = ''; return; }
  const branchId   = opt.dataset.branch;
  const branchName = opt.dataset.branchName;
  const company    = opt.dataset.company;
  info.innerHTML = branchName
    ? `<span style="color:#38bdf8">📍 ${branchName}</span> · <span style="color:#94a3b8">${company}</span>`
    : '<span style="color:#64748b">Sin sucursal asignada</span>';
  // Auto-select branch
  if (branchId) {
    for (const o of bSel.options) { if (o.value === branchId) { o.selected = true; break; } }
  } else {
    bSel.selectedIndex = 0;
  }
}

/* ─── Image preview (WhatsApp-style grid) ───────────────────── */
function fabImgPreview(input) {
  const files = Array.from(input.files);
  fabImgFiles = [];
  const preview = document.getElementById('fab-img-preview');
  const count   = document.getElementById('fab-img-count');
  preview.innerHTML = '';

  if (files.length > 10) {
    alert('Máximo 10 imágenes. Se tomarán las primeras 10.');
    fabImgFiles = files.slice(0, 10);
  } else {
    fabImgFiles = files;
  }

  fabImgFiles.forEach((f, i) => {
    const reader = new FileReader();
    reader.onload = ev => {
      const div = document.createElement('div');
      div.style.cssText = 'position:relative;width:60px;height:60px;border-radius:8px;overflow:hidden;border:1px solid #334155;';
      div.innerHTML = `<img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;">
        <button type="button" onclick="fabImgRemove(${i})" style="position:absolute;top:1px;right:1px;
          width:16px;height:16px;border-radius:50%;background:rgba(2,6,23,.8);border:none;
          color:#fff;font-size:10px;cursor:pointer;line-height:1;display:flex;align-items:center;justify-content:center;">✕</button>`;
      preview.appendChild(div);
    };
    reader.readAsDataURL(f);
  });

  count.textContent = fabImgFiles.length > 0 ? `${fabImgFiles.length}/10 foto(s) seleccionada(s)` : '';
}

function fabImgRemove(idx) {
  fabImgFiles.splice(idx, 1);
  const preview = document.getElementById('fab-img-preview');
  const count   = document.getElementById('fab-img-count');
  preview.innerHTML = '';
  fabImgFiles.forEach((f, i) => {
    const reader = new FileReader();
    reader.onload = ev => {
      const div = document.createElement('div');
      div.style.cssText = 'position:relative;width:60px;height:60px;border-radius:8px;overflow:hidden;border:1px solid #334155;';
      div.innerHTML = `<img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;">
        <button type="button" onclick="fabImgRemove(${i})" style="position:absolute;top:1px;right:1px;
          width:16px;height:16px;border-radius:50%;background:rgba(2,6,23,.8);border:none;
          color:#fff;font-size:10px;cursor:pointer;line-height:1;display:flex;align-items:center;justify-content:center;">✕</button>`;
      preview.appendChild(div);
    };
    reader.readAsDataURL(f);
  });
  count.textContent = fabImgFiles.length > 0 ? `${fabImgFiles.length}/10 foto(s)` : '';
}

/* ─── Audio Recorder ─────────────────────────────────────────── */
async function fabAudioStart() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    fabAudioChunks = [];
    fabAudioRecorder = new MediaRecorder(stream);
    fabAudioRecorder.ondataavailable = e => fabAudioChunks.push(e.data);
    fabAudioRecorder.onstop = () => {
      fabAudioBlob = new Blob(fabAudioChunks, { type: 'audio/webm' });
      document.getElementById('fab-audio-recording').style.display = 'none';
      document.getElementById('fab-audio-preview').style.display   = 'block';
      document.getElementById('fab-audio-dur').textContent          = fabFmtTime(fabAudioSecs);
      clearInterval(fabAudioTimer);
      stream.getTracks().forEach(t => t.stop());
    };
    fabAudioRecorder.start(100);
    fabAudioSecs = 0;
    document.getElementById('fab-audio-idle').style.display      = 'none';
    document.getElementById('fab-audio-recording').style.display = 'block';
    fabAudioTimer = setInterval(() => {
      fabAudioSecs++;
      document.getElementById('fab-audio-timer').textContent = fabFmtTime(fabAudioSecs);
      fabRenderWave();
    }, 1000);
  } catch(e) {
    alert('No se pudo acceder al micrófono: ' + e.message);
  }
}
function fabAudioStop() {
  if (fabAudioRecorder && fabAudioRecorder.state !== 'inactive') { fabAudioRecorder.stop(); }
}
function fabAudioRemove() {
  fabAudioBlob = null; fabAudioSecs = 0;
  document.getElementById('fab-audio-preview').style.display   = 'none';
  document.getElementById('fab-audio-idle').style.display      = 'flex';
  document.getElementById('fab-audio-recording').style.display = 'none';
}
function fabRenderWave() {
  const wave = document.getElementById('fab-audio-wave');
  wave.innerHTML = Array.from({length: 18}, () => {
    const h = 4 + Math.random() * 18;
    return `<div style="width:3px;height:${h}px;background:#a78bfa;border-radius:2px;opacity:.8;"></div>`;
  }).join('');
}
function fabFmtTime(s) {
  return Math.floor(s/60) + ':' + String(s%60).padStart(2,'0');
}

/* ─── Video Preview ──────────────────────────────────────────── */
function fabVideoPreview(input) {
  const file = input.files[0];
  const errEl = document.getElementById('fab-video-error');
  const preEl = document.getElementById('fab-video-preview');
  errEl.textContent = '';
  preEl.style.display = 'none';
  fabVideoFile = null; fabVideoDuracion = 0;

  if (!file) return;

  const url = URL.createObjectURL(file);
  const vid = document.createElement('video');
  vid.preload = 'metadata';
  vid.onloadedmetadata = () => {
    URL.revokeObjectURL(url);
    if (vid.duration > 62) {
      errEl.textContent = `⚠️ El video dura ${Math.round(vid.duration)}s. Máximo permitido: 60s.`;
      input.value = '';
    } else {
      fabVideoFile     = file;
      fabVideoDuracion = Math.round(vid.duration);
      preEl.style.display = 'block';
      preEl.innerHTML = `
        <div style="display:flex;align-items:center;gap:7px;background:#1e293b;border:1px solid #334155;border-radius:10px;padding:7px 10px;">
          <span style="font-size:.72rem;color:#34d399;font-weight:700;">🎬 ${file.name.slice(0,28)}...</span>
          <span style="font-size:.67rem;color:#64748b;margin-left:auto;">${fabFmtTime(fabVideoDuracion)}</span>
          <button type="button" onclick="fabVideoRemove()" style="padding:2px 7px;background:#334155;border:none;border-radius:6px;color:#94a3b8;font-size:.65rem;cursor:pointer;">✕</button>
        </div>`;
    }
  };
  vid.src = url;
}
function fabVideoRemove() {
  fabVideoFile = null; fabVideoDuracion = 0;
  document.getElementById('fab-video-preview').style.display = 'none';
  document.getElementById('fab-video-file').value = '';
}

/* ─── Submit ─────────────────────────────────────────────────── */
async function fabSubmit() {
  const errEl = document.getElementById('fab-error-msg');
  errEl.style.display = 'none';

  const userId   = document.getElementById('fab-user-id').value;
  const branchId = document.getElementById('fab-branch-id').value;
  const titulo   = document.getElementById('fab-titulo').value.trim();
  const catId    = document.getElementById('fab-cat-id').value;
  const prioridad = document.getElementById('fab-prioridad').value;
  const emergencia = document.getElementById('fab-emergencia').checked;
  const descripcion = document.getElementById('fab-descripcion').value.trim();

  if (!userId || !branchId || !titulo || !catId) {
    errEl.textContent = '⚠️ Completa los campos obligatorios: usuario, sucursal, título y categoría.';
    errEl.style.display = 'block';
    return;
  }

  const btn = document.getElementById('fab-submit-btn');
  btn.disabled = true;
  btn.innerHTML = '⏳ Transmitiendo...';

  const fd = new FormData();
  fd.append('notifier_id',   userId);
  fd.append('branch_id',     branchId);
  fd.append('titulo',        titulo);
  fd.append('descripcion',   descripcion || 'Reportada desde App Móvil / Panel Ejecutivo');
  fd.append('categoria_id',  catId);
  fd.append('prioridad',     prioridad);
  fd.append('es_emergencia', emergencia ? '1' : '0');

  fabImgFiles.forEach(f => fd.append('imagenes[]', f));

  if (fabAudioBlob) {
    fd.append('audio', fabAudioBlob, 'audio_' + Date.now() + '.webm');
  }
  if (fabVideoFile) {
    fd.append('video', fabVideoFile);
    fd.append('video_duracion', fabVideoDuracion);
  }

  // CSRF token
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const headers  = csrfMeta ? { 'X-CSRF-TOKEN': csrfMeta.content } : {};

  try {
    const res  = await fetch('/api/app/incidents', { method:'POST', body: fd, headers, credentials: 'include' });
    const data = await res.json();

    if (!res.ok || !data.success) {
      throw new Error(data.message || 'Error al registrar la incidencia.');
    }

    // Show success
    document.getElementById('fab-form-wrapper').style.display  = 'none';
    document.getElementById('fab-success-screen').style.display = 'flex';
    document.getElementById('fab-success-screen').style.flexDirection = 'column';
    document.getElementById('fab-ticket-badge').textContent    = data.codigo_ticket;

    const userOpt   = document.getElementById('fab-user-id').selectedOptions[0];
    const branchOpt = document.getElementById('fab-branch-id').selectedOptions[0];
    document.getElementById('fab-success-details').innerHTML = `
      <div style="display:flex;justify-content:space-between;"><span>Ticket:</span><strong style="color:#fbbf24;">${data.codigo_ticket}</strong></div>
      <div style="display:flex;justify-content:space-between;"><span>Usuario:</span><strong style="color:#fff;">${userOpt?.text?.split('·')[0]?.trim()}</strong></div>
      <div style="display:flex;justify-content:space-between;"><span>Sucursal:</span><strong style="color:#fff;">${branchOpt?.text?.split('(')[0]?.trim()}</strong></div>
      <div style="display:flex;justify-content:space-between;"><span>Multimedia:</span><strong style="color:#34d399;">${data.media_count} archivo(s)</strong></div>
      <div style="display:flex;justify-content:space-between;"><span>Estado:</span><strong style="color:#f59e0b;">1. Registrada</strong></div>`;

  } catch(e) {
    errEl.textContent = '❌ ' + (e.message || 'Error de conexión');
    errEl.style.display = 'block';
  } finally {
    btn.disabled = false;
    btn.innerHTML = '🚀 Registrar Incidencia';
  }
}

function fabResetForm() {
  document.getElementById('fab-form-wrapper').style.display   = 'block';
  document.getElementById('fab-success-screen').style.display = 'none';
  document.getElementById('fab-titulo').value      = '';
  document.getElementById('fab-descripcion').value = '';
  document.getElementById('fab-emergencia').checked = false;
  document.getElementById('fab-img-preview').innerHTML = '';
  document.getElementById('fab-img-count').textContent   = '';
  fabImgFiles = []; fabAudioBlob = null; fabVideoFile = null; fabVideoDuracion = 0;
  fabAudioRemove();
  fabVideoRemove();
}

/* ─── History ─────────────────────────────────────────────────── */
async function fabLoadHistory() {
  const list = document.getElementById('fab-history-list');
  const bSel = document.getElementById('fab-branch-id');
  const branchId = bSel ? bSel.value : '';
  const branchOpt = bSel && bSel.selectedOptions[0] && bSel.value !== '' ? bSel.selectedOptions[0] : null;
  const branchName = branchOpt ? branchOpt.text.split(' (')[0] : null;

  const label = document.getElementById('fab-history-label');
  if (label) {
    label.textContent = branchName ? `Historial: ${branchName}` : 'Últimas incidencias';
  }

  list.innerHTML = '<div style="text-align:center;color:#475569;font-size:.75rem;padding:20px 0;">Cargando...</div>';
  try {
    const qs = branchId ? `?branch_id=${branchId}` : '';
    const res  = await fetch(`/api/app/incidents${qs}`, { credentials: 'include' });
    const data = await res.json();

    if (!data.length) {
      list.innerHTML = '<div style="text-align:center;color:#475569;font-size:.75rem;padding:20px 0;">No hay incidencias registradas aún.</div>';
      return;
    }

    const estadoColor = { registrada:'#3b82f6', en_ejecucion:'#f59e0b', cerrada:'#10b981', cotizacion_validada:'#8b5cf6' };
    const prioColor   = { critica:'#ef4444', alta:'#f97316', media:'#f59e0b', baja:'#64748b' };

    list.innerHTML = data.map(inc => `
      <div style="background:#1e293b;border:1px solid #334155;border-radius:10px;padding:10px 12px;
        border-left:3px solid ${prioColor[inc.prioridad]||'#334155'};">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:4px;margin-bottom:5px;">
          <span style="font-size:.75rem;font-weight:900;color:#fbbf24;">${inc.codigo_ticket}</span>
          <span style="font-size:.65rem;font-weight:700;color:${estadoColor[inc.estado]||'#94a3b8'};
            background:rgba(255,255,255,.06);border-radius:6px;padding:2px 6px;white-space:nowrap;">${inc.estado.replace(/_/g,' ')}</span>
        </div>
        <div style="font-size:.78rem;color:#fff;font-weight:700;margin-bottom:3px;">${inc.titulo}</div>
        <div style="font-size:.68rem;color:#64748b;display:flex;flex-direction:column;gap:2px;">
          <span>👤 <strong style="color:#94a3b8;">${inc.notifier?.name || '—'}</strong></span>
          <span>📍 ${inc.branch?.nombre || '—'} · <span style="color:#475569">${inc.company?.nombre || ''}</span></span>
          <span style="display:flex;justify-content:space-between;">
            <span>📂 ${inc.categoria || '—'}</span>
            <span style="color:#334155">${inc.created_human}</span>
          </span>
          ${inc.media_count > 0 ? `<span>📎 ${inc.media_count} archivo(s): ${inc.media_tipos.join(', ')}</span>` : ''}
          ${inc.es_emergencia ? '<span style="color:#f87171;font-weight:700;">🚨 Emergencia Crítica</span>' : ''}
        </div>
      </div>`).join('');
  } catch(e) {
    list.innerHTML = '<div style="text-align:center;color:#f87171;font-size:.75rem;padding:20px 0;">Error al cargar historial.</div>';
  }
}
</script>

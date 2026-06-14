<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Super Admin Dashboard | Bantay Barangay</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: #eef2f7;
      font-family: 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
      padding: 1.5rem;
      min-height: 100vh;
    }

    .dash { max-width: 1400px; margin: 0 auto; }

    /* ── TOPBAR ── */
    .topbar {
      background: #020353;
      border-radius: 14px;
      padding: 1rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 3px solid #f4b942;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 0.75rem;
    }
    .topbar-title {
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .topbar-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .badge-admin {
      background: #f4b942;
      color: #3a2200;
      font-size: 0.75rem;
      font-weight: 700;
      padding: 4px 14px;
      border-radius: 2rem;
    }
    .btn-logout {
      background: rgba(255, 255, 255, 0.15);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.3);
      padding: 6px 16px;
      border-radius: 2rem;
      font-size: 0.8rem;
      cursor: pointer;
      font-family: inherit;
      transition: background 0.15s;
    }
    .btn-logout:hover { background: rgba(255, 255, 255, 0.28); }

    /* ── STATS ROW ── */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1rem 1.25rem;
      border-top: 3px solid #f4b942;
      box-shadow: 0 1px 4px rgba(0,0,0,0.07);
      transition: transform 0.15s, box-shadow 0.15s;
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    .stat-label {
      font-size: 0.72rem;
      color: #5a7a6e;
      font-weight: 600;
      letter-spacing: 0.4px;
      margin-bottom: 6px;
      text-transform: uppercase;
    }
    .stat-num {
      font-size: 2rem;
      font-weight: 700;
      color: #0b3b2f;
    }

    /* ── CONTENT LAYOUT ── */
    .content-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
      gap: 1.25rem;
    }
    .panel {
      background: white;
      border-radius: 14px;
      padding: 1.25rem 1.5rem;
      box-shadow: 0 1px 4px rgba(0,0,0,0.07);
      overflow: hidden;
      transition: box-shadow 0.15s;
    }
    .panel:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
    .panel-title {
      font-size: 1rem;
      font-weight: 700;
      color: #0b3b2f;
      margin-bottom: 1rem;
      padding-bottom: 8px;
      border-bottom: 2.5px solid #f4b942;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* ── RECENT REPORTS ── */
    .report-list {
      max-height: 340px;
      overflow-y: auto;
    }
    .report-item {
      padding: 10px 4px;
      border-bottom: 1px solid #e8eeed;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 10px;
    }
    .report-item:last-child { border-bottom: none; }
    .report-type { font-size: 0.83rem; font-weight: 600; color: #1a2e28; }
    .report-desc { font-size: 0.75rem; color: #4a6358; margin-top: 2px; margin-bottom: 4px; }
    .report-meta { font-size: 0.7rem; color: #8da396; margin-top: 3px; }

    /* Status Select Dropdowns */
    .status-sel {
      font-size: 0.72rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 2rem;
      border: 1.5px solid transparent;
      cursor: pointer;
      font-family: inherit;
      outline: none;
      transition: border-color 0.15s, opacity 0.15s;
      flex-shrink: 0;
    }
    .status-sel:focus { border-color: #f4b942; }
    .status-sel:disabled { opacity: 0.6; cursor: not-allowed; }

    /* Report Status Themes */
    .sel-pending  { background: #fff3e0; color: #804d00; }
    .sel-progress { background: #e3f0fc; color: #0d4b8a; }
    .sel-review   { background: #f3e8ff; color: #5b21b6; }
    .sel-resolved { background: #e1f7e9; color: #145c35; }

    /* Verification Status Themes */
    .sel-notver   { background: #fff3e0; color: #804d00; }
    .sel-verified { background: #e1f7e9; color: #145c35; }
    .sel-rejected { background: #fdecec; color: #8b1c1c; }

    /* ── RESIDENT DIRECTORY ── */
    .overflow-wrap { overflow-x: auto; }
    .res-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.78rem;
    }
    .res-table th {
      font-size: 0.7rem;
      color: #4a6358;
      font-weight: 700;
      text-align: left;
      padding: 6px 8px;
      border-bottom: 1px solid #e8eeed;
      text-transform: uppercase;
    }
    .res-table td {
      padding: 9px 8px;
      border-bottom: 1px solid #e8eeed;
      color: #1a2e28;
      vertical-align: middle;
    }
    .view-id-btn {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      background: #eef5ee;
      color: #1a4d2e;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 2rem;
      text-decoration: none;
    }
    .view-id-btn:hover { background: #f4b942; color: #3a2200; }
    .no-id { font-size: 0.7rem; color: #a0b4aa; font-style: italic; }

    .del-btn {
      background: #fff0f0;
      border: none;
      border-radius: 8px;
      padding: 5px 8px;
      cursor: pointer;
      font-size: 0.85rem;
      color: #e24b4a;
      transition: background 0.15s;
    }
    .del-btn:hover { background: #fdecec; }

    /* ── APPROVE BUTTON ── */
    .btn-approve {
      background: #e1f7e9;
      border: none;
      border-radius: 2rem;
      padding: 5px 14px;
      font-size: 0.75rem;
      font-weight: 700;
      color: #145c35;
      cursor: pointer;
      font-family: inherit;
      transition: background 0.15s;
    }
    .btn-approve:hover { background: #b6edca; }
    .btn-approve:disabled { opacity: 0.5; cursor: not-allowed; }

    /* ── DELETE MODAL ── */
    .modal-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 999;
      align-items: center;
      justify-content: center;
    }
    .modal-backdrop.active { display: flex; }
    .modal-box {
      background: white;
      border-radius: 16px;
      padding: 1.75rem;
      max-width: 380px;
      width: 90%;
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
      animation: slideUp 0.2s ease;
    }
    @keyframes slideUp {
      from { transform: translateY(20px); opacity: 0; }
      to   { transform: translateY(0);    opacity: 1; }
    }
    .modal-icon  { font-size: 2rem; margin-bottom: 0.75rem; }
    .modal-title { font-size: 1rem; font-weight: 700; color: #1a2e28; margin-bottom: 8px; }
    .modal-body  { font-size: 0.83rem; color: #4a6358; line-height: 1.6; margin-bottom: 1.25rem; }
    .modal-actions { display: flex; gap: 8px; justify-content: flex-end; }
    .btn-cancel {
      background: #f0f4f2;
      border: none;
      color: #1a2e28;
      padding: 8px 20px;
      border-radius: 2rem;
      font-size: 0.83rem;
      font-weight: 600;
      cursor: pointer;
    }
    .btn-confirm-del {
      background: #e24b4a;
      border: none;
      color: white;
      padding: 8px 20px;
      border-radius: 2rem;
      font-size: 0.83rem;
      font-weight: 600;
      cursor: pointer;
    }

    /* ── TOAST NOTIFICATION ── */
    .toast {
      position: fixed;
      bottom: 1.5rem;
      right: 1.5rem;
      background: #0b3b2f;
      color: white;
      padding: 10px 20px;
      border-radius: 2rem;
      font-size: 0.82rem;
      font-weight: 600;
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.25s;
      pointer-events: none;
      z-index: 9999;
    }
    .toast.show { opacity: 1; transform: translateY(0); }

    .empty-msg { text-align: center; color: #8da396; padding: 2rem 1rem; font-size: 0.83rem; }
    .footer { margin-top: 2rem; text-align: center; color: #8da396; font-size: 0.72rem; }
  </style>
</head>
<body>
<div class="dash">

  <div class="topbar">
    <div class="topbar-title">🛡️ Super Admin Dashboard | Bantay Barangay</div>
    <div class="topbar-right">
      <span class="badge-admin">👑 SUPER ADMIN</span>
      <button class="btn-logout" onclick="handleLogout()">🚪 Logout</button>
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-label">📋 Total Reports</div>
      <div class="stat-num" id="totalReports">0</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">⏳ Pending Reports</div>
      <div class="stat-num" id="pendingReports">0</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">✅ Resolved Reports</div>
      <div class="stat-num" id="resolvedReports">0</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">👥 Verified Residents</div>
      <div class="stat-num" id="totalResidents">0</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">🕐 Pending Verification</div>
      <div class="stat-num" id="pendingResidents">0</div>
    </div>
  </div>

  <div class="content-row">
    <div class="panel">
      <div class="panel-title">📌 Recent Reports</div>
      <div class="report-list" id="recentReports">
        <div class="empty-msg">Loading reports...</div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-title">👪 Resident Directory</div>
      <div class="overflow-wrap">
        <table class="res-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Address</th>
              <th>Phone</th>
              <th>Age</th>
              <th>Gender</th>
              <th>ID</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="residentList">
            <tr><td colspan="8" class="empty-msg">Loading residents...</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <div class="panel-title">🔑 Password Reset Requests</div>
      <div class="report-list" id="resetRequestsList">
        <div class="empty-msg">Loading requests...</div>
      </div>
    </div>
  </div>

  <div class="footer">🕊️ Bantay Barangay Admin Portal | Serbisyong Tapat, Barangay Sambayan</div>
</div>

<div class="modal-backdrop" id="deleteModal">
  <div class="modal-box">
    <div class="modal-icon">🗑️</div>
    <div class="modal-title">Remove resident?</div>
    <div class="modal-body" id="modalBody"></div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeModal()">Cancel</button>
      <button class="btn-confirm-del" onclick="confirmDelete()">Remove</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
  let pendingDeleteId   = null;
  let pendingDeleteName = null;

  document.addEventListener("DOMContentLoaded", function () {
    if (typeof checkSession === "function") checkSession();
    loadReports();
    loadResidents();
    loadResetRequests();
  });

  function handleLogout() {
    window.location.href = 'admin_logout.php';
  }

  function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
  }

  const reportClassMap = {
    'Pending':      'sel-pending',
    'In Progress':  'sel-progress',
    'Under Review': 'sel-review',
    'Resolved':     'sel-resolved'
  };

  const resClassMap = {
    'Not Verified': 'sel-notver',
    'Verified':     'sel-verified',
    'Rejected':     'sel-rejected'
  };

  function applyReportColor(sel) {
    sel.className = 'status-sel ' + (reportClassMap[sel.value] || 'sel-pending');
  }

  function applyResColor(sel) {
    sel.className = 'status-sel ' + (resClassMap[sel.value] || 'sel-notver');
  }

  function updateReportStats() {
    const sels = document.querySelectorAll('#recentReports .status-sel');
    let pending = 0, resolved = 0;
    sels.forEach(s => {
      if (s.value === 'Pending')  pending++;
      if (s.value === 'Resolved') resolved++;
    });
    document.getElementById('totalReports').textContent    = sels.length;
    document.getElementById('pendingReports').textContent  = pending;
    document.getElementById('resolvedReports').textContent = resolved;
  }

  function updateResidentStats() {
    const sels = document.querySelectorAll('#residentList .status-sel');
    let verified = 0, notVerified = 0;
    sels.forEach(s => {
      if (s.value === 'Verified')     verified++;
      if (s.value === 'Not Verified') notVerified++;
    });
    document.getElementById('totalResidents').textContent   = verified;
    document.getElementById('pendingResidents').textContent = notVerified;
  }

  // ── LOAD REPORTS ──────────────────────────────────────────────────────────
  function loadReports() {
    const el = document.getElementById('recentReports');

    Promise.all([
      fetch('get_reports.php').then(r => r.ok ? r.json() : []).catch(() => []),
      fetch('get_reports_kapitbahay.php').then(r => r.ok ? r.json() : []).catch(() => []),
      fetch('get_reports_officials.php').then(r => r.ok ? r.json() : []).catch(() => [])
    ])
    .then(([originalReports, kapitbahayReports, officialsReports]) => {
      let combined = [...originalReports, ...kapitbahayReports, ...officialsReports];

      const seen = new Set();
      combined = combined.filter(r => {
        if (seen.has(r.id)) return false;
        seen.add(r.id);
        return true;
      });

      if (!combined.length) {
        el.innerHTML = '<div class="empty-msg">No reports yet.</div>';
        updateReportStats();
        return;
      }

      combined.sort((a, b) => new Date(b.created_at || b.date) - new Date(a.created_at || a.date));

      el.innerHTML = combined.map(r => {
        let type          = r.report_type || 'General Report';
        let name          = r.reported_name || r.name || '';
        let desc          = r.description || '';
        let loc           = r.location || r.address || 'Not Provided';
        let date          = r.created_at || r.date || '';
        let currentStatus = r.status || 'Pending';

        return `
          <div class="report-item">
            <div>
              <div class="report-type">${type}${name && name !== 'Anonymous' ? ' &middot; <small style="color:#64748b;">' + name + '</small>' : ''}</div>
              <div class="report-desc">${desc}</div>
              <div class="report-meta">📍 ${loc} &middot; 📅 ${date}</div>
            </div>
            <select
              class="status-sel ${reportClassMap[currentStatus] || 'sel-pending'}"
              data-id="${r.id}"
              data-prev="${currentStatus}"
              onchange="applyReportColor(this); saveReportStatus(this); updateReportStats()">
              <option${currentStatus === 'Pending'      ? ' selected' : ''}>Pending</option>
              <option${currentStatus === 'In Progress'  ? ' selected' : ''}>In Progress</option>
              <option${currentStatus === 'Under Review' ? ' selected' : ''}>Under Review</option>
              <option${currentStatus === 'Resolved'     ? ' selected' : ''}>Resolved</option>
            </select>
          </div>`;
      }).join('');

      updateReportStats();
    })
    .catch(() => {
      el.innerHTML = '<div class="empty-msg">Failed to load reports.</div>';
    });
  }

  // ── LOAD RESIDENTS ────────────────────────────────────────────────────────
  function loadResidents() {
    fetch('get_residents.php')
      .then(r => r.json())
      .then(residents => {
        const tbody = document.getElementById('residentList');
        if (!residents.length) {
          tbody.innerHTML = '<tr><td colspan="8" class="empty-msg">No residents yet.</td></tr>';
          updateResidentStats();
          return;
        }
        tbody.innerHTML = residents.map(r => {
          const idLink = r.verification_id
            ? `<a href="${r.verification_id}" target="_blank" class="view-id-btn">📄 View ID</a>`
            : `<span class="no-id">No file</span>`;

          return `
            <tr id="resident-row-${r.id}">
              <td>${r.full_name}</td>
              <td>${r.address}</td>
              <td>${r.phone}</td>
              <td>${r.age}</td>
              <td>${r.gender}</td>
              <td>${idLink}</td>
              <td>
                <select class="status-sel ${resClassMap[r.status] || 'sel-notver'}" data-id="${r.id}" onchange="applyResColor(this); saveResidentStatus(this); updateResidentStats()">
                  <option${r.status === 'Not Verified' ? ' selected' : ''}>Not Verified</option>
                  <option${r.status === 'Verified'     ? ' selected' : ''}>Verified</option>
                  <option${r.status === 'Rejected'     ? ' selected' : ''}>Rejected</option>
                </select>
              </td>
              <td><button class="del-btn" onclick="openModal('${r.id}', '${r.full_name.replace(/'/g, "\\'")}')">🗑️</button></td>
            </tr>`;
        }).join('');
        updateResidentStats();
      })
      .catch(() => {
        document.getElementById('residentList').innerHTML = '<tr><td colspan="8" class="empty-msg">Failed to load resident directory.</td></tr>';
      });
  }

  // ── SAVE REPORT STATUS ────────────────────────────────────────────────────
  function saveReportStatus(sel) {
    const newStatus  = sel.value;
    const prevStatus = sel.dataset.prev;
    const id         = sel.dataset.id;

    sel.disabled = true;

    fetch('update_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(newStatus)}`
    })
    .then(r => {
      const ct = r.headers.get('content-type') || '';
      if (!ct.includes('application/json')) {
        return r.text().then(txt => {
          throw new Error('update_status.php ay nagbalik ng non-JSON. PHP error:\n' + txt.substring(0, 300));
        });
      }
      return r.json();
    })
    .then(data => {
      sel.disabled = false;
      if (data.success) {
        sel.dataset.prev = newStatus;
        showToast('✅ Status updated');
      } else {
        sel.value = prevStatus;
        applyReportColor(sel);
        updateReportStats();
        showToast('❌ Hindi na-save: ' + (data.error || data.message || 'Unknown error'));
        console.error('update_status.php response:', data);
      }
    })
    .catch(err => {
      sel.disabled = false;
      sel.value = prevStatus;
      applyReportColor(sel);
      updateReportStats();
      showToast('❌ Server error. Buksan ang console para sa detalye.');
      console.error('saveReportStatus() error:', err.message);
    });
  }

  function saveResidentStatus(sel) {
    fetch('update_resident_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${sel.dataset.id}&status=${encodeURIComponent(sel.value)}`
    }).then(() => showToast('✅ Verification status updated'));
  }

  // ── PASSWORD RESET REQUESTS ───────────────────────────────────────────────
  function loadResetRequests() {
    const el = document.getElementById('resetRequestsList');

    fetch('get_reset_requests.php')
      .then(r => r.json())
      .then(requests => {
        if (!requests.length) {
          el.innerHTML = '<div class="empty-msg">No pending password reset requests.</div>';
          return;
        }

        el.innerHTML = requests.map(r => `
          <div class="report-item" id="reset-row-${r.id}">
            <div>
              <div class="report-type">👤 ${r.username}</div>
              <div class="report-meta">📅 ${r.requested_at}</div>
            </div>
            <div style="display:flex;gap:6px;flex-shrink:0;">
              <button class="btn-approve" onclick="handleReset(${r.id}, this)">✅ Approve</button>
            </div>
          </div>`).join('');
      })
      .catch(() => {
        el.innerHTML = '<div class="empty-msg">Failed to load reset requests.</div>';
      });
  }

  function handleReset(id, btn) {
    const row  = document.getElementById(`reset-row-${id}`);
    const btns = row.querySelectorAll('button');
    btns.forEach(b => b.disabled = true);

    fetch('approve_reset.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}`
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        row.remove();
        const el = document.getElementById('resetRequestsList');
        if (!el.querySelector('.report-item')) {
          el.innerHTML = '<div class="empty-msg">No pending password reset requests.</div>';
        }
        showToast('✅ Password na-reset na!');
      } else {
        btns.forEach(b => b.disabled = false);
        showToast('❌ Error: ' + (data.error || 'Unknown'));
      }
    })
    .catch(() => {
      btns.forEach(b => b.disabled = false);
      showToast('❌ Server error.');
    });
  }

  // ── DELETE MODAL ──────────────────────────────────────────────────────────
  function openModal(id, name) {
    pendingDeleteId   = id;
    pendingDeleteName = name;
    document.getElementById('modalBody').innerHTML =
      `Remove <strong>${name}</strong> from the Resident Directory?`;
    document.getElementById('deleteModal').classList.add('active');
  }

  function closeModal() {
    document.getElementById('deleteModal').classList.remove('active');
    pendingDeleteId   = null;
    pendingDeleteName = null;
  }

  document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
  });

  function confirmDelete() {
    if (!pendingDeleteId) return;
    const id = pendingDeleteId;
    closeModal();
    fetch('hide_resident.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}`
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        const row = document.getElementById(`resident-row-${id}`);
        if (row) row.remove();
        updateResidentStats();
        showToast('🗑️ Resident removed from list');
      } else {
        showToast('❌ Failed to remove resident');
      }
    })
    .catch(() => showToast('❌ Error connecting to server'));
  }
</script>
</body>
</html>
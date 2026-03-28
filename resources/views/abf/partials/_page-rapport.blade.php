    <div id="page-rapport" class="page">
      <div class="page-title">Rapport</div>
      <div class="page-subtitle">Génération du rapport PDF</div>
      <div class="card">
        <div class="card-body" style="text-align:center;padding:40px 20px">
          <svg width="56" height="56" viewBox="0 0 24 24" fill="none" style="margin-bottom:16px">
            <rect width="24" height="24" rx="6" fill="#f0f3fa"/>
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="#0E1030" stroke-width="1.5" fill="none"/>
            <path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="#C9A050" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <div style="font-size:15px;font-weight:700;color:var(--navy);margin-bottom:8px">Rapport ABF — <span id="rapport-client-nom">—</span></div>
          <div style="font-size:13px;color:var(--muted);margin-bottom:24px">Générez le rapport PDF complet incluant toutes les sections de l'analyse</div>
          <button class="btn btn-gold" onclick="showToast('Génération du PDF en cours…')">
            <svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            Télécharger le PDF
          </button>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- BOTTOM BAR -->
<div class="bottom-bar">
  <button class="btn btn-secondary" onclick="goPrev()">← Précédent</button>
  <button class="btn btn-primary" onclick="goNext()">Suivant →</button>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>


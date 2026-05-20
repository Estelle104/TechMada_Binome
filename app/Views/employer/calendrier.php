<!DOCTYPE html>
<html lang="fr">
<?= view('partials/template_head', ['title' => 'Calendrier - TechMada RH']) ?>
<body>
<?php
$employe = $employe ?? [];
$counts = $counts ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
$historique = $historique ?? [];
$prenom = (string) ($employe['prenom'] ?? '');
$nom = (string) ($employe['nom'] ?? '');
$departementNom = (string) ($employe['departement_nom'] ?? '');
$initials = trim(substr($prenom, 0, 1) . substr($nom, 0, 1));
$initials = $initials !== '' ? strtoupper($initials) : 'EM';
?>

<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <ul class="sidebar-nav" style="margin-top:1rem">
      <li><a href="/employer"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employer/conges/nouvelle"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/employer/conges/mes"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/employer/profil"><i class="bi bi-person"></i> Mon profil</a></li>
      <li><a href="/employer/calendrier" class="active"><i class="bi bi-calendar"></i> Calendrier</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc($initials) ?></div>
        <div>
          <div class="user-name"><?= esc(trim($prenom . ' ' . $nom)) ?></div>
          <div class="user-role">Employé · <?= esc($departementNom !== '' ? $departementNom : 'Sans département') ?></div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Calendrier de congés</div>
        <div class="topbar-breadcrumb"><a href="/employer">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Calendrier</div>
      </div>
    </div>

    <div class="content">
      <div class="data-card">
        <div class="data-card-head">
          <h3>Vue mensuelle</h3>
        </div>
        <div id="calendar" style="padding: 20px;"></div>
      </div>

      <!-- Dashboard Statistiques -->
      <div style="margin-top: 30px;">
        <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 5px; color: #333;">Historique et statistiques</h2>
        <p style="font-size: 14px; color: #666; margin-bottom: 20px;">Nombre total de demandes de congé, par statut</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
          <div class="stat-card stat-attente">
            <div class="stat-value"><?= esc((string) $counts['en_attente']) ?></div>
            <div class="stat-label">En attente</div>
          </div>
          <div class="stat-card stat-approuvee">
            <div class="stat-value"><?= esc((string) $counts['approuvee']) ?></div>
            <div class="stat-label">Approuvés</div>
          </div>
          <div class="stat-card stat-refusee">
            <div class="stat-value"><?= esc((string) $counts['refusee']) ?></div>
            <div class="stat-label">Refusés</div>
          </div>
          <div class="stat-card stat-annulee">
            <div class="stat-value"><?= esc((string) $counts['annulee']) ?></div>
            <div class="stat-label">Annulés</div>
          </div>
        </div>
      </div>

      <!-- Historique -->
      <div class="data-card" style="margin-top: 30px;">
        <div class="data-card-head">
          <h3>Historique détaillé</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Motif</th><th>Statut</th></tr>
          </thead>
          <tbody>
          <?php if (empty($historique)): ?>
            <tr><td colspan="6" class="td-muted">Aucun congé enregistré.</td></tr>
          <?php endif; ?>
          <?php foreach ($historique as $conge): ?>
            <?php
              $stat = (string) $conge['statut'];
              $statusClass = $stat === 'approuvee' ? 's-approuvee' : ($stat === 'refusee' ? 's-refusee' : ($stat === 'annulee' ? 's-annulee' : 's-attente'));
              $typeNom = (string) ($conge['type_conge_nom'] ?? '');
              $typeLower = strtolower($typeNom);
              $typeClass = str_contains($typeLower, 'annuel') ? 't-annuel' : (str_contains($typeLower, 'maladie') ? 't-maladie' : (str_contains($typeLower, 'exception') || str_contains($typeLower, 'special') ? 't-special' : 't-sans-solde'));
            ?>
            <tr>
              <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($typeNom) ?></span></td>
              <td class="td-muted"><?= esc(date('d/m/Y', strtotime((string) $conge['date_debut']))) ?></td>
              <td class="td-muted"><?= esc(date('d/m/Y', strtotime((string) $conge['date_fin']))) ?></td>
              <td class="td-mono"><?= esc((string) $conge['nb_jours']) ?> j</td>
              <td class="td-muted"><?= esc((string) ($conge['motif'] ?? '—')) ?></td>
              <td><span class="statut <?= esc($statusClass) ?>"><?= esc($stat) ?></span></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= esc((string) date('Y')) ?> <span>TechMada RH</span></div>
  </div>
</div>

    <link href="<?= base_url('util/fullcalendar.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('util/fullcalendar.min.js') ?>"></script>

    <style>
        #calendar {
            background: white;
            border-radius: 6px;
        }

        .fc {
            font-size: 14px;
        }

        .fc-daygrid-day-frame {
            min-height: 120px;
        }

        .fc-daygrid-day-cell {
            padding: 6px !important;
        }

        .fc-event {
            padding: 3px 6px !important;
            font-size: 11px !important;
            border-radius: 4px !important;
            border: none !important;
        }

        .fc-event-title {
            font-weight: bold;
            color: white !important;
            word-wrap: break-word !important;
            white-space: normal !important;
            overflow: visible !important;
        }

        .fc-event-main {
            padding: 4px !important;
        }

        .fc-daygrid-event {
            margin: 2px 0 !important;
            min-height: 20px !important;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-attente .stat-value { color: #f59e0b; }
        .stat-approuvee .stat-value { color: #2d5a3d; }
        .stat-refusee .stat-value { color: #dc2626; }
        .stat-annulee .stat-value { color: #6b7280; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('<?= base_url('/employer/events') ?>')
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(() => failureCallback());
                }

            });

            calendar.render();
        });
    </script>

</body>
</html>

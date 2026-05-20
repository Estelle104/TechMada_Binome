<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier Congee</title>
    <link href="<?= base_url('util/fullcalendar.min.css') ?>" rel="stylesheet">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        h1 {
            text-align: center;
            color: #333;
            padding: 20px;
            margin: 0;
        }

        #calendar {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .fc {
            font-size: 14px;
        }

        .fc-daygrid-day-frame {
            min-height: 100px;
        }

        .fc-event {
            padding: 2px 4px !important;
            font-size: 12px !important;
            border-radius: 4px !important;
        }

        .fc-event-title {
            font-weight: bold;
            color: white !important;
        }
    </style>
</head>

<?= view('partials/template_head', ['title' => 'Dashboard Employe - TechMada RH']) ?>
<body>
<?php
$employe = $employe ?? [];
$counts = $counts ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
$soldes = $soldes ?? [];
$annual = $annual ?? ['attribues' => 0, 'restants' => 0];
$recentDemandes = $recentDemandes ?? [];
$prenom = (string) ($employe['prenom'] ?? '');
$nom = (string) ($employe['nom'] ?? '');
$departementNom = (string) ($employe['departement_nom'] ?? '');
$initials = trim(substr($prenom, 0, 1) . substr($nom, 0, 1));
$initials = $initials !== '' ? strtoupper($initials) : 'EM';
?>
    <h1>Calendrier de congee</h1>
    <div id="calendar">
    </div>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
            <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
        </div>
        <div class="sidebar-section">Menu</div>
        <ul class="sidebar-nav">
            <li><a href="/employer" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
            <li><a href="/employer/conges/nouvelle"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
            <li>
                <a href="/employer/conges/mes">
                    <i class="bi bi-calendar3"></i> Mes demandes
                    <span class="nav-badge alert"><?= esc((string) $counts['en_attente']) ?></span>
                </a>
            </li>
            <li><a href="/employer/profil"><i class="bi bi-person"></i> Mon profil</a></li>
            <li><a href="/employer/calendrier"><i class="bi bi-calendar"></i> Calendrier</a></li>

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

    <script src="<?= base_url('util/fullcalendar.min.js') ?>"></script>

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

</html>
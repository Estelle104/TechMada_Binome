<?php
$db = new SQLite3(__DIR__ . '/../writable/database.sqlite');
$year = date('Y');
$sql = "SELECT c.id as conge_id, c.nb_jours, c.statut, c.employe_id, s.id as solde_id, s.jours_pris
FROM conges c
LEFT JOIN soldes s ON s.employe_id = c.employe_id AND s.type_conge_id = c.type_conge_id AND s.annee = $year
WHERE c.statut = 'en_attente' LIMIT 1";
$res = $db->query($sql);
$row = $res->fetchArray(SQLITE3_ASSOC);
if (!$row) {
    echo "NONE\n";
    exit(0);
}
echo json_encode($row) . "\n";

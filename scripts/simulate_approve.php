<?php
// Simulate approving a conge by id, using the same DB operations as the controller.
// Usage: php simulate_approve.php <conge_id>
$id = $argv[1] ?? null;
if (!$id) { echo "Usage: php simulate_approve.php <conge_id>\n"; exit(1); }
$db = new SQLite3(__DIR__ . '/../writable/database.sqlite');
$year = date('Y');

// fetch demande
$stmt = $db->prepare("SELECT c.id, c.nb_jours, c.statut, c.employe_id, c.type_conge_id, s.id AS solde_id, s.jours_pris, s.jours_attribues, (COALESCE(s.jours_attribues,0)-COALESCE(s.jours_pris,0)) AS jours_restants
FROM conges c
LEFT JOIN soldes s ON s.employe_id = c.employe_id AND s.type_conge_id = c.type_conge_id AND s.annee = :annee
WHERE c.id = :id");
$stmt->bindValue(':annee', $year, SQLITE3_INTEGER);
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
$res = $stmt->execute();
$row = $res->fetchArray(SQLITE3_ASSOC);
if (!$row) { echo "Demande not found\n"; exit(1); }
print_r(["before" => $row]);
if ($row['statut'] !== 'en_attente') { echo "Not in en_attente\n"; exit(1); }
$nb = (int)$row['nb_jours'];
$rest = (int)($row['jours_restants'] ?? 0);
if ($rest < $nb) { echo "Insufficient balance: $rest < $nb\n"; exit(1); }
// begin transaction
$db->exec('BEGIN');
$now = date('Y-m-d H:i:s');
$upd1 = $db->prepare('UPDATE conges SET statut = :statut, commentaire_rh = :comm, traite_par = :tp, date_traitement = :dt WHERE id = :id');
$upd1->bindValue(':statut','approuvee',SQLITE3_TEXT);
$upd1->bindValue(':comm','Simulated approval',SQLITE3_TEXT);
$upd1->bindValue(':tp',0,SQLITE3_INTEGER);
$upd1->bindValue(':dt',$now,SQLITE3_TEXT);
$upd1->bindValue(':id',$id,SQLITE3_INTEGER);
$ok1 = $upd1->execute();
if (!$row['solde_id']) { $db->exec('ROLLBACK'); echo "No solde id\n"; exit(1); }
$upd2 = $db->prepare('UPDATE soldes SET jours_pris = jours_pris + :n WHERE id = :sid');
$upd2->bindValue(':n',$nb,SQLITE3_INTEGER);
$upd2->bindValue(':sid',$row['solde_id'],SQLITE3_INTEGER);
$ok2 = $upd2->execute();
if (!$ok1 || !$ok2) { $db->exec('ROLLBACK'); echo "DB update failed\n"; exit(1); }
$db->exec('COMMIT');
// fetch after state
$stmt2 = $db->prepare('SELECT c.id,c.statut,s.jours_pris FROM conges c LEFT JOIN soldes s ON s.id = :sid WHERE c.id = :id');
$stmt2->bindValue(':sid',$row['solde_id'],SQLITE3_INTEGER);
$stmt2->bindValue(':id',$id,SQLITE3_INTEGER);
$res2 = $stmt2->execute();
$after = $res2->fetchArray(SQLITE3_ASSOC);
print_r(["after" => $after]);
echo "Simulated approval done\n";

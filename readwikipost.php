<?php
session_start();

// Include necessary files for database and utility functions
require_once 'functions/database.php';
require_once 'functions/utils.php';

// Überprüfen, ob die Post-ID in der URL übergeben wurde und ob sie gültig ist
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php'); // Falls keine gültige ID vorhanden ist, umleiten
    exit();
}

// Die Post-ID aus der URL lesen und als Integer speichern
$post_id = (int) $_GET['id'];

// Das Post aus der Datenbank holen
$post = fetch_post($pdo, $post_id);

// Wenn das Post nicht gefunden wird, eine Fehlermeldung anzeigen
if (!$post) {
    exit('Post mit der ID ' . $post_id . ' wurde nicht gefunden!');
}

// Den Autor des Posts holen, falls vorhanden
$author = isset($post['author_id']) && is_numeric($post['author_id']) ? fetch_user($pdo, $post['author_id']) : null;

// Die Benutzer-ID und Rolle aus der Session holen
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null; // Benutzerrolle (z.B. admin, moderator)

// Alle Wiki-Einreichungen (Submissions) für dieses Post holen
$sql = "SELECT ws.id, ws.moderator_id, ws.created_at, wc.name AS category_name 
        FROM wiki_submissions ws
        JOIN wiki_categories wc ON ws.category_id = wc.id
        WHERE ws.post_id = :post_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['post_id' => $post_id]);
$wiki_submissions = $stmt->fetchAll();

// Header der Seite einfügen
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Hauptinhalt-Spalte (Zentrum) -->
        <div class="col-md-12">
            <!-- Post-Header: Profilbild, Benutzername und Erstellungszeit -->
            <h1><?= htmlspecialchars($post["title"]) ?></h1>
            <p><?= nl2br(htmlspecialchars($post["content"])) ?></p>

            <!-- Abschnitt für Wiki-Einreichungen -->
            <div class="mt-4">
                <h4>Wiki Einreichungen</h4>
                <?php if ($wiki_submissions): ?>
                    <!-- Tabelle für die Anzeige der Wiki Einreichungen -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kategorie</th>
                                <th>Moderator</th>
                                <th>Erstellt am</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wiki_submissions as $submission): ?>
                                <tr>
                                    <!-- Kategorie der Einreichung -->
                                    <td><?= htmlspecialchars($submission['category_name']) ?></td>
                                    <td>
                                        <?php
                                        // Den Moderator der Einreichung holen
                                        $moderator = fetch_user($pdo, $submission['moderator_id']);
                                        echo htmlspecialchars($moderator['username'] ?? 'deleted_user');
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars(time_ago($submission['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Falls keine Wiki Einreichungen vorhanden sind -->
                    <p>Es wurden keine Wiki Einreichungen für dieses Post gefunden.</p>
                <?php endif; ?>
            </div>

            <!-- Formular für neue Wiki-Einreichung -->
            <div class="mt-5">
                <h4>Neue Wiki Einreichung</h4>
                <?php if ($user_id): ?>
                    <!-- Formular zur Einreichung von Wiki-Daten -->
                    <form action="submit_wiki.php" method="POST">
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategorie</label>
                            <select class="form-control" id="category" name="category_id" required>
                                <option value="">Wählen Sie eine Kategorie...</option>
                                <?php
                                // Alle Kategorien aus der Datenbank holen
                                $categories = fetch_all_categories($pdo);
                                if ($categories): // Falls Kategorien vorhanden sind
                                    foreach ($categories as $category) {
                                        echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                                    }
                                else:
                                    echo '<option value="">Keine Kategorien verfügbar</option>';
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Wiki-Inhalt</label>
                            <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                        </div>
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($post_id) ?>">
                        <button type="submit" class="btn btn-primary">Einreichen</button>
                    </form>

                <?php else: ?>
                    <p><a href="login.php">Login</a> um eine Wiki Einreichung zu tätigen.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php
// Footer der Seite einfügen
include 'includes/footer.php';
?>
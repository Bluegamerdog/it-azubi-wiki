<?php
session_start();
require 'db_connection.php'; // Verbindung zur Datenbank

$user_id = $_SESSION['user_id'] ?? null; // Aktuell eingeloggter Benutzer

// 1. Beitrag-ID erhalten
$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    die("Beitrag nicht gefunden.");
}

// 2. Reaktion verarbeiten (wenn das Formular abgeschickt wurde)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reaction'])) {
    $reaction = $_POST['reaction']; // 'like' oder 'dislike'

    // Überprüfen, ob der Benutzer bereits eine Reaktion abgegeben hat
    $stmt = $conn->prepare("SELECT * FROM post_reactions WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        if ($existing['reaction_type'] === $reaction) {
            // Gleiche Reaktion vorhanden → Reaktion entfernen (z. B. "Unlike")
            $del = $conn->prepare("DELETE FROM post_reactions WHERE id = ?");
            $del->execute([$existing['id']]);
        } else {
            // Andere Reaktion → Reaktion aktualisieren
            $upd = $conn->prepare("UPDATE post_reactions SET reaction_type = ? WHERE id = ?");
            $upd->execute([$reaction, $existing['id']]);
        }
    } else {
        // Noch keine Reaktion → Neue Reaktion einfügen
        $ins = $conn->prepare("INSERT INTO post_reactions (post_id, user_id, reaction_type) VALUES (?, ?, ?)");
        $ins->execute([$post_id, $user_id, $reaction]);
    }

    // Nach der Aktion Seite neu laden
    header("Location: read_post.php?id=" . $post_id);
    exit;
}

// 3. Den Beitrag abrufen
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) {
    die("Beitrag nicht gefunden.");
}

// 4. Likes und Dislikes zählen
$stmt = $conn->prepare("SELECT 
    SUM(reaction_type = 'like') AS likes,
    SUM(reaction_type = 'dislike') AS dislikes
    FROM post_reactions WHERE post_id = ?");
$stmt->execute([$post_id]);
$reactions = $stmt->fetch();
?>

<h2><?= htmlspecialchars($post['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

<p>👍 Gefällt mir: <?= $reactions['likes'] ?? 0 ?> | 👎 Gefällt mir nicht: <?= $reactions['dislikes'] ?? 0 ?></p>

<?php if ($user_id): ?>
    <!-- Formular für "Gefällt mir" -->
    <form method="POST">
        <input type="hidden" name="reaction" value="like">
        <button type="submit">👍 Gefällt mir</button>
    </form>

    <!-- Formular für "Gefällt mir nicht" -->
    <form method="POST">
        <input type="hidden" name="reaction" value="dislike">
        <button type="submit">👎 Gefällt mir nicht</button>
    </form>
<?php else: ?>
    <p><i>Bitte einloggen, um abstimmen zu können.</i></p>
<?php endif; ?>
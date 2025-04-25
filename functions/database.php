<?php

// @jonathan - Might convert this to classes later

require_once 'utils.php';

$envFile = file_exists('.env.development') ? '.env.development' : '.env';
loadEnv($envFile);

try {
    $db = getenv('DB_NAME');
    $host = getenv('DB_HOST');
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        getenv('DB_USER'),
        getenv('DB_PASS'),
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Verbindung fehlgeschlagen: " . $e->getMessage());
}

// == USERS ==

function fetch_all_users(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT id, role, username, email, password FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_user(PDO $pdo, int|string $user_id)
{
    $stmt = $pdo->prepare("SELECT id, role, username, email, profile_image_path FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetch_user_by_username(PDO $pdo, string $username)
{
    $stmt = $pdo->prepare("SELECT id, role, username, email, profile_image_path, password FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_user(PDO $pdo, string $username, string $email, string $password): bool
{
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password) 
        VALUES (:username, :email, :password)
    ");
    return $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $passwordHash
    ]);
}


function delete_user(PDO $pdo, int $user_id): bool
{
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
    return $stmt->execute(['user_id' => $user_id]);
}

// EXAMPLE:
// update_user($pdo, 3, [
//     'email' => 'newemail@example.com',
//     'password' => 'newPlaintextPassword123'
// ]);
function update_user(PDO $pdo, int|string $user_id, $data): bool
{
    $fields = [];
    $params = ['user_id' => $user_id];

    foreach ($data as $key => $value) {
        if ($key === 'password') {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }

        $fields[] = "$key = :$key";
        $params[$key] = $value;
    }

    if (empty($fields))
        return false;

    $stmt = $pdo->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = :user_id");
    return $stmt->execute($params);
}

// == POSTS ==

function fetch_all_posts(PDO $pdo, $sorted_by = 'newest', $days_old = 'all'): array
{
    $whereClause = '';
    $params = [];

    // Filter by days old
    if ($days_old != 'all') {
        $date_limit = date('Y-m-d', strtotime("-$days_old days"));
        $whereClause .= " WHERE created_at >= :date_limit";
        $params['date_limit'] = $date_limit;
    }

    // Sorting logic
    switch ($sorted_by) {
        case 'oldest':
            $orderBy = "ORDER BY created_at ASC";
            break;
        case 'most_activity':
            // You can modify this query to use actual "activity" data (e.g., comment count, vote count)
            $orderBy = "ORDER BY (SELECT COUNT(*) FROM post_comments WHERE post_id = posts.id) DESC";
            break;
        case 'trending':
            // Trending can be based on a combination of factors such as votes, comments, etc.
            $orderBy = "ORDER BY (SELECT COUNT(*) FROM post_comments WHERE post_id = posts.id) DESC, created_at DESC";
            break;
        case 'no_comments':
            $whereClause .= ($whereClause ? " AND" : " WHERE") . " (SELECT COUNT(*) FROM post_comments WHERE post_id = posts.id) = 0";
            $orderBy = "ORDER BY created_at DESC";
            break;
        case 'newest':
        default:
            $orderBy = "ORDER BY created_at DESC";
            break;
    }

    $stmt = $pdo->prepare("SELECT * FROM posts" . $whereClause . " " . $orderBy);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function fetch_posts_by_user(PDO $pdo, int|string $author_id): array
{
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE author_id = :author_id");
    $stmt->execute(["author_id" => $author_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_post(PDO $pdo, int|string $post_id)
{
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :post_id");
    $stmt->execute(['post_id' => $post_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_post(PDO $pdo, int|string $author_id, string $title, string $content): bool
{
    $stmt = $pdo->prepare("INSERT INTO posts (author_id, title, content) VALUES (:author_id, :title, :content)");
    return $stmt->execute([
        'author_id' => $author_id,
        'title' => $title,
        'content' => $content,
    ]);
}

function delete_post(PDO $pdo, int|string $post_id): bool
{
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :post_id");
    return $stmt->execute(['post_id' => $post_id]);
}

function update_post(PDO $pdo, int|string $post_id, string $title, string $content): bool
{
    $stmt = $pdo->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :post_id");
    return $stmt->execute([
        'title' => $title,
        'content' => $content,
        'post_id' => $post_id,
    ]);
}

// == COMMENTS ==
function fetch_all_comments(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT * FROM post_comments");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_comments_by_post(PDO $pdo, int|string $post_id): array
{
    $stmt = $pdo->prepare("SELECT * FROM post_comments WHERE post_id = :post_id");
    $stmt->execute(['post_id' => $post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_comments_by_user(PDO $pdo, int|string $author_id): array
{
    $stmt = $pdo->prepare("SELECT * FROM post_comments WHERE author_id = :author_id");
    $stmt->execute(['author_id' => $author_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_comment(PDO $pdo, int|string $comment_id)
{
    $stmt = $pdo->prepare("SELECT * FROM post_comments WHERE id = :comment_id");
    $stmt->execute(['comment_id' => $comment_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_comment(PDO $pdo, int|string $post_id, int|string $author_id, string $content): bool
{
    $stmt = $pdo->prepare("INSERT INTO post_comments (post_id, author_id, content) VALUES (:post_id, :author_id, :content)");
    return $stmt->execute(['post_id' => $post_id, 'author_id' => $author_id, 'content' => $content]);
}

function delete_comment(PDO $pdo, int|string $comment_id): bool
{
    $stmt = $pdo->prepare("DELETE FROM post_comments WHERE id = :comment_id");
    return $stmt->execute(['comment_id' => $comment_id]);
}

// == POST REACTIONS ==
// Get total upvotes and downvotes for a post 
// -- fetch_reaction_counts($pdo, 1); // ['upvote' => x, 'downvote' => y]

// $reactions = fetch_reaction_counts($pdo, $post_id);
// $upvote_count = $reactions['upvote'];
// $downvote_count = $reactions['downvote'];
function fetch_reaction_counts(PDO $pdo, int $post_id): array
{
    $stmt = $pdo->prepare("SELECT reaction_type, COUNT(*) as count
        FROM post_reactions
        WHERE post_id = :post_id
        GROUP BY reaction_type
    ");
    $stmt->execute(['post_id' => $post_id]);

    $results = ['upvote' => 0, 'downvote' => 0];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $results[$row['reaction_type']] = (int) $row['count'];
    }
    return $results;
}

// Get a user's reaction for a specific post
function fetch_user_reaction(PDO $pdo, int $post_id, int $user_id): ?string
{
    $stmt = $pdo->prepare("
        SELECT reaction_type
        FROM post_reactions
        WHERE post_id = :post_id AND user_id = :user_id
    ");
    $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['reaction_type'] : null;
}

// Add or update a user's reaction to a post 
// set_reaction($pdo, $post_id, $user_id, 'upvote'); // User 1 upvotes post 1
// set_reaction($pdo, $post_id, $user_id, 'downvote'); // Changes to downvote

function set_reaction(PDO $pdo, int $post_id, int $user_id, string $reaction_type): bool
{
    $current = fetch_user_reaction($pdo, $post_id, $user_id);

    if ($current === $reaction_type) {
        // Toggle off if same reaction
        return delete_reaction($pdo, $post_id, $user_id);
    }

    if ($current === null) {
        $stmt = $pdo->prepare("
            INSERT INTO post_reactions (post_id, user_id, reaction_type)
            VALUES (:post_id, :user_id, :reaction_type)
        ");
    } else {
        $stmt = $pdo->prepare("
            UPDATE post_reactions
            SET reaction_type = :reaction_type
            WHERE post_id = :post_id AND user_id = :user_id
        ");
    }

    return $stmt->execute([
        'post_id' => $post_id,
        'user_id' => $user_id,
        'reaction_type' => $reaction_type
    ]);
}

// Remove a user's reaction
// delete_reaction($pdo, 1, 1, 'downvote'); // Removes the downvote
function delete_reaction(PDO $pdo, int $post_id, int $user_id): bool
{
    $stmt = $pdo->prepare("
        DELETE FROM post_reactions
        WHERE post_id = :post_id AND user_id = :user_id
    ");
    return $stmt->execute([
        'post_id' => $post_id,
        'user_id' => $user_id
    ]);
}

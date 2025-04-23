<?php

// @jonathan - Might convert this to classes later

require_once 'utils.php';

$envFile = file_exists(__DIR__ . '/.env.local') ? '.env.local' : '.env';
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
    $stmt = $pdo->prepare("SELECT username, email FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_user(PDO $pdo, int|string $user_id)
{
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetch_user_by_username(PDO $pdo, string $username)
{
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_user(PDO $pdo, string $username, string $email, string $password): bool
{
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    return $stmt->execute(['username' => $username, 'email' => $email, 'password' => $passwordHash]);
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

function fetch_all_posts(PDO $pdo): array
{
    $stmt = $pdo->prepare("SELECT * FROM posts");
    $stmt->execute();
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
    $stmt = $pdo->prepare("SELECT * FROM comments");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_comments_by_post(PDO $pdo, int|string $post_id): array
{
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = :post_id");
    $stmt->execute(['post_id' => $post_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_comments_by_user(PDO $pdo, int|string $author_id): array
{
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE author_id = :author_id");
    $stmt->execute(['author_id' => $author_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_comment(PDO $pdo, int|string $comment_id)
{
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :comment_id");
    $stmt->execute(['comment_id' => $comment_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function create_comment(PDO $pdo, int|string $post_id, int|string $author_id, string $content): bool
{
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, author_id, content) VALUES (:post_id, :author_id, :content)");
    return $stmt->execute(['post_id' => $post_id, 'author_id' => $author_id, 'content' => $content]);
}

function delete_comment(PDO $pdo, int|string $comment_id): bool
{
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :comment_id");
    return $stmt->execute(['comment_id' => $comment_id]);
}

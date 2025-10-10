<?php
// models/Article.php
// Model responsible for database CRUD operations for articles

require_once __DIR__ . '/../config/database.php';

class Article
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Get all articles ordered by created_at DESC
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT id, title, content, author, created_at, updated_at
            FROM articles
            ORDER BY created_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Get a single article by ID
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, title, content, author, created_at, updated_at
            FROM articles
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create a new article
     * @param string $title
     * @param string $content
     * @param string $author
     * @return int Inserted article ID
     */
    public function create(string $title, string $content, string $author): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO articles (title, content, author)
            VALUES (:title, :content, :author)
        ");
        $stmt->execute([
            'title'   => $title,
            'content' => $content,
            'author'  => $author
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update an existing article
     * @param int $id
     * @param string $title
     * @param string $content
     * @param string $author
     * @return bool success
     */
    public function update(int $id, string $title, string $content, string $author): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE articles
            SET title = :title, content = :content, author = :author
            WHERE id = :id
        ");
        return $stmt->execute([
            'title'   => $title,
            'content' => $content,
            'author'  => $author,
            'id'      => $id
        ]);
    }

    /**
     * Delete an article by ID
     * @param int $id
     * @return bool success
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

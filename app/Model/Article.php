<?php

namespace App\Model;

use SQLite3;

class Article
{
    private SQLite3 $db;

    public function __construct()
    {
        $this->db = new SQLite3(__DIR__ . '/../../storage/articles.sqlite');
        $this->createTable();
    }

    private function createTable(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS articles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    }

    public function getArticles(): array
    {
        $result = $this->db->query("SELECT * FROM articles");
        $articles = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $articles[] = $row;
        }
        return $articles;
    }

    public function getArticle(int $id): ?array
    {
        $article = $this->db->prepare("SELECT * FROM articles WHERE id=:id");
        $article->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $article->execute();
        return $result->fetchArray(SQLITE3_ASSOC) ?: null;
    }

    public function create(string $title, string $content): void
    {
        $article = $this->db->prepare("INSERT INTO articles (title, content, created_at, updated_at)
VALUES (:title, :content, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        $article->bindValue(':title', $title);
        $article->bindValue(':content', $content);
        $article->execute();
    }

    public function update(int $id, string $title, string $content): void
    {
        $article = $this->db->prepare("UPDATE articles
SET title=:title, content=:content, updated_at=CURRENT_TIMESTAMP WHERE id=:id");
        $article->bindValue(':id', $id);
        $article->bindValue(':title', $title);
        $article->bindValue(':content', $content);
        $article->execute();
    }

    public function delete(int $id): void
    {
        $article = $this->db->prepare("DELETE FROM articles WHERE id=:id");
        $article->bindValue(':id', $id, SQLITE3_INTEGER);
        $article->execute();
    }
}
<?php

namespace App\Models;

use App\Core\Model;

class Admin extends Model
{
    public function getCategories(): array
    {
        $stmt = $this->db->query(
            'SELECT id, name, slug
             FROM categories
             ORDER BY id ASC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function findCategoryById(int $categoryId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, slug
             FROM categories
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute(['id' => $categoryId]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    public function findCategoryByName(string $name): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, slug
             FROM categories
             WHERE LOWER(name) = LOWER(:name)
             LIMIT 1'
        );

        $stmt->execute(['name' => trim($name)]);
        $category = $stmt->fetch();

        return $category ?: null;
    }

    public function createCategory(string $name): int
    {
        $name = trim($name);
        $slug = $this->generateUniqueSlug('categories', $name);

        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, slug, created_at)
             VALUES (:name, :slug, NOW())'
        );

        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function createChallenge(array $data): bool
    {
        $slug = $this->generateUniqueSlug('challenges', $data['title']);

        $stmt = $this->db->prepare(
            'INSERT INTO challenges (
                category_id, title, slug, description, points, difficulty,
                flag_hash, hint, file_path, is_active, created_at
             )
             VALUES (
                :category_id, :title, :slug, :description, :points, :difficulty,
                :flag_hash, :hint, :file_path, :is_active, NOW()
             )'
        );

        return $stmt->execute([
            'category_id' => (int) $data['category_id'],
            'title' => trim((string) $data['title']),
            'slug' => $slug,
            'description' => trim((string) $data['description']),
            'points' => (int) $data['points'],
            'difficulty' => trim((string) $data['difficulty']),
            'flag_hash' => (string) $data['flag_hash'],
            'hint' => $data['hint'] !== '' ? trim((string) $data['hint']) : null,
            'file_path' => $data['file_path'] !== '' ? trim((string) $data['file_path']) : null,
            'is_active' => (int) $data['is_active'],
        ]);
    }

    private function generateUniqueSlug(string $table, string $text): string
    {
        $baseSlug = $this->slugify($text);
        if ($baseSlug === '') {
            $baseSlug = 'item';
        }

        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($table, $slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $table, string $slug): bool
    {
        $query = sprintf('SELECT id FROM %s WHERE slug = :slug LIMIT 1', $table);
        $stmt = $this->db->prepare($query);
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() !== false;
    }

    private function slugify(string $text): string
    {
        $text = trim(strtolower($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text) ?? '';
        $text = preg_replace('/\s+/', '-', $text) ?? '';
        $text = preg_replace('/-+/', '-', $text) ?? '';

        return trim($text, '-');
    }
}

<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Practice extends Model
{
    public function getCategories(): array
    {
        $stmt = $this->db->query(
            'SELECT c.id, c.name, c.slug, COUNT(ch.id) AS challenge_count
             FROM categories c
             LEFT JOIN challenges ch ON ch.category_id = c.id AND ch.is_active = 1
             GROUP BY c.id, c.name, c.slug
               ORDER BY c.id ASC'
        );

        return $stmt->fetchAll() ?: [];
    }

    public function getChallengesByCategory(int $categoryId, int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ch.id, ch.category_id, ch.title, ch.slug, ch.description, ch.points, ch.difficulty, ch.hint,
                    CASE WHEN s.id IS NULL THEN 0 ELSE 1 END AS is_solved
             FROM challenges ch
             LEFT JOIN solves s ON s.challenge_id = ch.id AND s.user_id = :user_id
             WHERE ch.category_id = :category_id AND ch.is_active = 1
               ORDER BY ch.points ASC'
        );

        $stmt->execute([
            'user_id' => $userId,
            'category_id' => $categoryId,
        ]);

        return $stmt->fetchAll() ?: [];
    }

    public function findActiveChallengeById(int $challengeId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, category_id, title, points, flag_hash, is_active
             FROM challenges
             WHERE id = :id AND is_active = 1
             LIMIT 1'
        );

        $stmt->execute(['id' => $challengeId]);
        $challenge = $stmt->fetch();

        return $challenge ?: null;
    }

    public function hasSolved(int $userId, int $challengeId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id
             FROM solves
             WHERE user_id = :user_id AND challenge_id = :challenge_id
             LIMIT 1'
        );

        $stmt->execute([
            'user_id' => $userId,
            'challenge_id' => $challengeId,
        ]);

        return $stmt->fetch() !== false;
    }

    public function createSubmission(int $userId, int $challengeId, string $submittedFlag, bool $isCorrect): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO submissions (user_id, challenge_id, submitted_flag, is_correct, created_at)
             VALUES (:user_id, :challenge_id, :submitted_flag, :is_correct, NOW())'
        );

        $stmt->execute([
            'user_id' => $userId,
            'challenge_id' => $challengeId,
            'submitted_flag' => $submittedFlag,
            'is_correct' => $isCorrect ? 1 : 0,
        ]);
    }

    public function createSolve(int $userId, int $challengeId): bool
    {
        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO solves (user_id, challenge_id, solved_at)
             VALUES (:user_id, :challenge_id, NOW())'
        );

        $stmt->execute([
            'user_id' => $userId,
            'challenge_id' => $challengeId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function verifyFlag(string $submittedFlag, string $storedHash): bool
    {
        $submittedFlag = trim($submittedFlag);
        if ($submittedFlag === '') {
            return false;
        }

        $hashInfo = password_get_info($storedHash);
        if (!empty($hashInfo['algo'])) {
            return password_verify($submittedFlag, $storedHash);
        }

        // Support common legacy storage formats during migration.
        if (hash_equals($storedHash, hash('sha256', $submittedFlag))) {
            return true;
        }

        if (hash_equals($storedHash, md5($submittedFlag))) {
            return true;
        }

        return hash_equals($storedHash, $submittedFlag);
    }
}

<!DOCTYPE html>
<html lang="en">
<?php
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(dirname($scriptName), '/');
if ($basePath === '' || $basePath === '.') {
    $basePath = '';
}

$categories = $categories ?? [];
$oldInput = $oldInput ?? [];
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - nmsCTF</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/auth.css', ENT_QUOTES, 'UTF-8') ?>">
    <style>
        .admin-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
            padding: 40px 20px;
            margin: 0 auto;
        }

        .admin-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            padding: 28px;
            backdrop-filter: blur(10px);
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .title {
            margin: 0;
            color: var(--text-primary);
            font-size: 30px;
        }

        .sub {
            margin: 6px 0 0;
            color: var(--text-secondary);
        }

        .top-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .link-btn {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            text-decoration: none;
            padding: 10px 14px;
            font-weight: 600;
        }

        .notice {
            padding: 11px 13px;
            border-radius: 8px;
            border: 1px solid;
            margin-bottom: 14px;
        }

        .notice.ok {
            color: #22c55e;
            border-color: rgba(34, 197, 94, 0.45);
            background: rgba(34, 197, 94, 0.12);
        }

        .notice.err {
            color: #f87171;
            border-color: rgba(248, 113, 113, 0.45);
            background: rgba(248, 113, 113, 0.12);
        }

        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: 1fr 1fr;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 14px;
        }

        input,
        select,
        textarea {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            color: var(--text-primary);
            padding: 11px 12px;
            outline: none;
            width: 100%;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }

        .check {
            display: flex;
            gap: 8px;
            align-items: center;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .check input {
            width: auto;
        }

        .submit-row {
            margin-top: 16px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .submit-btn {
            border: none;
            border-radius: 9px;
            background: var(--primary-color);
            color: #fff;
            padding: 12px 18px;
            font-weight: 700;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: var(--primary-hover);
        }

        .helper {
            margin: 0;
            color: var(--text-secondary);
            font-size: 13px;
        }

        @media (max-width: 800px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>

    <div class="admin-container">
        <div class="admin-card">
            <div class="header-row">
                <div>
                    <h1 class="title">Admin Dashboard</h1>
                    <p class="sub">Thêm challenge mới cho hệ thống nmsCTF.</p>
                </div>
                <div class="top-actions">
                    <a class="link-btn" href="<?= htmlspecialchars($basePath . '/dashboard', ENT_QUOTES, 'UTF-8') ?>">Dashboard</a>
                    <a class="link-btn" href="<?= htmlspecialchars($basePath . '/practice', ENT_QUOTES, 'UTF-8') ?>">Practice</a>
                </div>
            </div>

            <?php if (!empty($success)): ?>
                <div class="notice ok"><?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="notice err"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= htmlspecialchars($basePath . '/admin/challenges/create', ENT_QUOTES, 'UTF-8') ?>">
                <div class="grid">
                    <div class="field">
                        <label for="category_id">Category có sẵn</label>
                        <select id="category_id" name="category_id">
                            <option value="">-- Chọn category --</option>
                            <?php foreach ($categories as $category): ?>
                                <option
                                    value="<?= (int) $category['id'] ?>"
                                    <?= (int) ($oldInput['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label for="new_category_name">Hoặc tạo category mới</label>
                        <input
                            id="new_category_name"
                            type="text"
                            name="new_category_name"
                            value="<?= htmlspecialchars((string) ($oldInput['new_category_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Ví dụ: Cryptography"
                        >
                    </div>

                    <div class="field full">
                        <label for="title">Title challenge</label>
                        <input
                            id="title"
                            type="text"
                            name="title"
                            required
                            value="<?= htmlspecialchars((string) ($oldInput['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Ví dụ: Hidden Message"
                        >
                    </div>

                    <div class="field full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?= htmlspecialchars((string) ($oldInput['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="field">
                        <label for="points">Points</label>
                        <input
                            id="points"
                            type="number"
                            min="1"
                            name="points"
                            value="<?= (int) ($oldInput['points'] ?? 100) ?>"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="difficulty">Difficulty</label>
                        <select id="difficulty" name="difficulty" required>
                            <?php $selectedDifficulty = (string) ($oldInput['difficulty'] ?? 'easy'); ?>
                            <option value="easy" <?= $selectedDifficulty === 'easy' ? 'selected' : '' ?>>Easy</option>
                            <option value="medium" <?= $selectedDifficulty === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="hard" <?= $selectedDifficulty === 'hard' ? 'selected' : '' ?>>Hard</option>
                        </select>
                    </div>

                    <div class="field full">
                        <label for="flag">Flag (plain text)</label>
                        <input
                            id="flag"
                            type="text"
                            name="flag"
                            required
                            placeholder="Ví dụ: nmsCTF{example_flag}"
                        >
                        <p class="helper">Hệ thống sẽ tự hash flag trước khi lưu.</p>
                    </div>

                    <div class="field full">
                        <label for="hint">Hint</label>
                        <textarea id="hint" name="hint" placeholder="Có thể nhập nhiều hint, phân tách bằng ||"><?= htmlspecialchars((string) ($oldInput['hint'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="field full">
                        <label for="file_path">File path (optional)</label>
                        <input
                            id="file_path"
                            type="text"
                            name="file_path"
                            value="<?= htmlspecialchars((string) ($oldInput['file_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Ví dụ: /uploads/challenges/chall1.zip"
                        >
                    </div>

                    <div class="field full">
                        <label class="check" for="is_active">
                            <input id="is_active" type="checkbox" name="is_active" value="1" <?= (int) ($oldInput['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                            Challenge đang active
                        </label>
                    </div>
                </div>

                <div class="submit-row">
                    <button class="submit-btn" type="submit">Tạo Challenge</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

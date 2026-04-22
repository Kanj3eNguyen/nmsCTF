<!DOCTYPE html>
<html lang="en">
<?php
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(dirname($scriptName), '/');
if ($basePath === '' || $basePath === '.') {
    $basePath = '';
}

$categories = $categories ?? [];
$challenges = $challenges ?? [];
$selectedCategoryId = (int) ($selectedCategoryId ?? 0);
$selectedChallengeId = (int) ($selectedChallengeId ?? 0);
$selectedChallenge = $selectedChallenge ?? null;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice - nmsCTF</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/auth.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Space Grotesk', sans-serif;
        }

        .practice-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1100px;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            justify-content: flex-start;
            gap: 16px;
        }

        .practice-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 1000px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
            animation: slideUp 0.35s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .sub {
            color: var(--text-secondary);
            margin: 4px 0 0;
        }

        .pill-row {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .pill {
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.04);
            border-radius: 999px;
            padding: 8px 14px;
            text-decoration: none;
            font-size: 14px;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .pill.active,
        .pill:hover {
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.18);
        }

        .challenge-grid {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 16px;
        }

        .challenge-item {
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px;
            background: rgba(255, 255, 255, 0.02);
        }

        .challenge-link {
            display: block;
            color: inherit;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .challenge-link.active .challenge-item,
        .challenge-link:hover .challenge-item {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.18);
        }

        .select-note {
            margin: 0 0 12px;
            color: var(--text-secondary);
        }

        .challenge-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .challenge-title {
            margin: 0;
            color: var(--text-primary);
            font-size: 20px;
        }

        .meta {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .tag {
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .tag.solved {
            color: #16a34a;
            border-color: rgba(22, 163, 74, 0.45);
            background: rgba(22, 163, 74, 0.12);
        }

        .desc {
            color: var(--text-secondary);
            margin: 0 0 12px;
            line-height: 1.55;
        }

        .hint {
            margin: 0 0 12px;
            color: #f59e0b;
            font-size: 14px;
        }

        .submit-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .flag-input {
            flex: 1 1 240px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--bg-input);
            color: var(--text-primary);
            padding: 11px 12px;
            outline: none;
        }

        .flag-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
            padding: 11px 16px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            box-shadow: var(--shadow-glow);
            transform: translateY(-2px);
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .notice {
            width: 100%;
            max-width: 1000px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
            border: 1px solid;
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

        .empty {
            margin: 4px 0 0;
            color: var(--text-secondary);
        }

        .challenge-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(10, 15, 28, 0.6);
            z-index: 30;
            backdrop-filter: blur(2px);
        }

        .challenge-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: min(92vw, 880px);
            max-height: 88vh;
            overflow: auto;
            background: #f4f6ff;
            color: #26334b;
            border-radius: 12px;
            border: 1px solid #d9def0;
            box-shadow: 0 24px 60px rgba(10, 15, 28, 0.45);
            z-index: 40;
        }

        .modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 22px 12px;
            border-bottom: 1px solid #dde3f5;
        }

        .modal-title {
            margin: 0;
            font-size: 36px;
            line-height: 1.1;
            font-weight: 700;
            color: #2a3154;
        }

        .modal-close {
            text-decoration: none;
            color: #8c97b3;
            font-size: 30px;
            line-height: 1;
        }

        .modal-close:hover {
            color: #59638a;
        }

        .modal-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .modal-tag {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            text-transform: none;
        }

        .modal-tag.diff-easy { background: #0f9d85; }
        .modal-tag.diff-medium { background: #d97706; }
        .modal-tag.diff-hard { background: #dc2626; }
        .modal-tag.cat { background: #ef4444; }
        .modal-tag.point { background: #3b82f6; }

        .modal-body {
            padding: 18px 22px 22px;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: 1fr 190px;
            gap: 20px;
        }

        .modal-label {
            margin: 0 0 8px;
            font-size: 13px;
            font-weight: 700;
            color: #6d7692;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .modal-description {
            margin: 0;
            color: #2f3b58;
            line-height: 1.6;
            white-space: pre-line;
        }

        .hint-tabs {
            display: flex;
            gap: 6px;
            margin-top: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .hint-toggle {
            border: 1px solid #cfd6ef;
            background: #ffffff;
            color: #44507a;
            border-radius: 8px;
            padding: 7px 12px;
            cursor: pointer;
            font-weight: 700;
        }

        .hint-toggle.open {
            border-color: #5865f2;
            color: #3d47a3;
            background: #e8ebff;
        }

        .hint-box {
            margin-top: 10px;
        }

        .hint-tab {
            border: none;
            border-radius: 8px;
            padding: 6px 11px;
            cursor: pointer;
            background: #dde3ff;
            color: #3d4780;
            font-weight: 700;
        }

        .hint-tab.active {
            background: #5865f2;
            color: #fff;
        }

        .hint-panel {
            background: #e9edff;
            border: 1px solid #d8defa;
            color: #36406b;
            border-radius: 8px;
            padding: 10px;
            min-height: 70px;
            white-space: pre-line;
        }

        .hint-empty {
            margin: 0;
            color: #7b86ab;
        }

        .modal-submit {
            border-top: 1px solid #dde3f5;
            margin-top: 18px;
            padding-top: 16px;
            display: grid;
            gap: 10px;
            grid-template-columns: 1fr 170px;
        }

        .modal-input {
            border: 1px solid #cfd6ef;
            background: #fff;
            color: #26334b;
            border-radius: 8px;
            padding: 11px 12px;
            outline: none;
            font-size: 15px;
        }

        .modal-input:focus {
            border-color: #5b69f5;
            box-shadow: 0 0 0 3px rgba(91, 105, 245, 0.16);
        }

        .btn-submit {
            border: none;
            border-radius: 8px;
            background: #5865f2;
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #4a57db;
        }

        @media (max-width: 640px) {
            .practice-card {
                padding: 20px;
            }

            .modal-title {
                font-size: 30px;
            }

            .modal-grid {
                grid-template-columns: 1fr;
            }

            .modal-submit {
                grid-template-columns: 1fr;
            }

            .btn-submit {
                min-height: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="background-pattern"></div>

    <div class="practice-container">
        <?php if (!empty($success)): ?>
            <div class="notice ok"><?= htmlspecialchars((string) $success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="notice err"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="practice-card">
            <div class="header-row">
                <div>
                    <h1 class="title">Practice Arena</h1>
                    <p class="sub">Xin chào <?= htmlspecialchars((string) ($_SESSION['user_name'] ?? 'User'), ENT_QUOTES, 'UTF-8') ?>, chọn category rồi submit flag để ghi solve.</p>
                </div>
                <div class="actions">
                    <a class="btn" style="border: 1px solid var(--border-color); color: var(--text-primary);" href="<?= htmlspecialchars($basePath . '/dashboard', ENT_QUOTES, 'UTF-8') ?>">Dashboard</a>
                    <a class="btn" style="border: 1px solid var(--border-color); color: var(--text-primary);" href="<?= htmlspecialchars($basePath . '/profile', ENT_QUOTES, 'UTF-8') ?>">Profile</a>
                    <a class="btn" style="border: 1px solid #ef4444; color: #ef4444;" href="<?= htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8') ?>">Logout</a>
                </div>
            </div>

            <div class="pill-row">
                <?php foreach ($categories as $category): ?>
                    <?php $isActive = (int) $category['id'] === $selectedCategoryId; ?>
                    <a
                        class="pill <?= $isActive ? 'active' : '' ?>"
                        href="<?= htmlspecialchars($basePath . '/practice?category=' . (int) $category['id'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?> (<?= (int) $category['challenge_count'] ?>)
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($categories)): ?>
                <p class="empty">Chưa có category nào.</p>
            <?php elseif (empty($challenges)): ?>
                <p class="empty">Category này chưa có challenge khả dụng.</p>
            <?php else: ?>
                <p class="select-note">Chọn challenge để xem đề bài và submit flag.</p>
                <div class="challenge-grid">
                    <?php foreach ($challenges as $challenge): ?>
                        <?php $isSelected = (int) $challenge['id'] === $selectedChallengeId; ?>
                        <a
                            class="challenge-link <?= $isSelected ? 'active' : '' ?>"
                            href="<?= htmlspecialchars($basePath . '/practice?category=' . (int) $selectedCategoryId . '&challenge=' . (int) $challenge['id'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <article class="challenge-item">
                                <div class="challenge-top">
                                    <h2 class="challenge-title"><?= htmlspecialchars((string) $challenge['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                                    <?php if ((int) $challenge['is_solved'] === 1): ?>
                                        <span class="tag solved">Solved</span>
                                    <?php endif; ?>
                                </div>
                                <div class="meta">
                                    <span class="tag"><?= htmlspecialchars((string) $challenge['difficulty'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="tag"><?= (int) $challenge['points'] ?> pts</span>
                                </div>
                            </article>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($selectedChallenge === null): ?>
                    <p class="empty">Bạn chưa chọn challenge nào.</p>
                <?php else: ?>
                    <?php
                    $selectedCategoryName = 'Category';
                    foreach ($categories as $categoryItem) {
                        if ((int) $categoryItem['id'] === $selectedCategoryId) {
                            $selectedCategoryName = (string) $categoryItem['name'];
                            break;
                        }
                    }

                    $difficultyClass = 'diff-' . strtolower((string) $selectedChallenge['difficulty']);

                    $hints = [];
                    if (!empty($selectedChallenge['hint'])) {
                        $parts = explode('||', (string) $selectedChallenge['hint']);
                        foreach ($parts as $part) {
                            $trimmed = trim($part);
                            if ($trimmed !== '') {
                                $hints[] = $trimmed;
                            }
                        }
                    }
                    ?>

                    <div class="challenge-modal-backdrop"></div>
                    <div class="challenge-modal" role="dialog" aria-modal="true" aria-label="Challenge detail">
                        <div class="modal-head">
                            <div>
                                <h2 class="modal-title"><?= htmlspecialchars((string) $selectedChallenge['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <div class="modal-tags">
                                    <span class="modal-tag <?= htmlspecialchars($difficultyClass, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ucfirst((string) $selectedChallenge['difficulty']), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="modal-tag cat"><?= htmlspecialchars($selectedCategoryName, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="modal-tag point"><?= (int) $selectedChallenge['points'] ?> pts</span>
                                    <?php if ((int) $selectedChallenge['is_solved'] === 1): ?>
                                        <span class="tag solved">Solved</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <a
                                class="modal-close"
                                href="<?= htmlspecialchars($basePath . '/practice?category=' . (int) $selectedCategoryId, ENT_QUOTES, 'UTF-8') ?>"
                                aria-label="Close challenge"
                            >
                                ×
                            </a>
                        </div>

                        <div class="modal-body">
                            <div class="modal-grid">
                                <div>
                                    <p class="modal-label">Description</p>
                                    <p class="modal-description"><?= htmlspecialchars((string) $selectedChallenge['description'], ENT_QUOTES, 'UTF-8') ?></p>
                                </div>

                                <div>
                                    <p class="modal-label">Hints</p>
                                    <?php if (!empty($hints)): ?>
                                        <button class="hint-toggle" id="hint-toggle" type="button" aria-expanded="false">Show Hints</button>
                                        <div class="hint-box" id="hint-box" hidden>
                                            <div class="hint-tabs" id="hint-tabs">
                                                <?php foreach ($hints as $index => $hintItem): ?>
                                                    <button
                                                        class="hint-tab"
                                                        type="button"
                                                        data-hint-index="<?= (int) $index ?>"
                                                    >
                                                        <?= (int) ($index + 1) ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="hint-panel" id="hint-panel">Chọn tab hint để xem nội dung.</div>
                                        </div>
                                    <?php else: ?>
                                        <p class="hint-empty">Chưa có hint cho challenge này.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <form method="POST" action="<?= htmlspecialchars($basePath . '/practice/submit', ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="challenge_id" value="<?= (int) $selectedChallenge['id'] ?>">
                                <div class="modal-submit">
                                    <input
                                        class="modal-input"
                                        type="text"
                                        name="submitted_flag"
                                        placeholder="nmsCTF{FLAG}"
                                        autocomplete="off"
                                        required
                                    >
                                    <button class="btn-submit" type="submit">Submit<br>Flag</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (!empty($hints)): ?>
                        <script>
                            (function () {
                                const hintToggle = document.getElementById('hint-toggle');
                                const hintBox = document.getElementById('hint-box');
                                const hintTabs = document.querySelectorAll('[data-hint-index]');
                                const hintPanel = document.getElementById('hint-panel');
                                const hintData = <?= json_encode(array_values($hints), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
                                let activeIndex = null;

                                if (hintToggle && hintBox) {
                                    hintToggle.addEventListener('click', function () {
                                        const isOpen = !hintBox.hidden;
                                        hintBox.hidden = isOpen;
                                        hintToggle.classList.toggle('open', !isOpen);
                                        hintToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                                        hintToggle.textContent = isOpen ? 'Show Hints' : 'Hide Hints';

                                        if (isOpen) {
                                            activeIndex = null;
                                            hintTabs.forEach((item) => item.classList.remove('active'));
                                            if (hintPanel) {
                                                hintPanel.textContent = 'Chọn tab hint để xem nội dung.';
                                            }
                                        }
                                    });
                                }

                                hintTabs.forEach((tab) => {
                                    tab.addEventListener('click', function () {
                                        const index = Number(this.getAttribute('data-hint-index'));

                                        if (activeIndex === index) {
                                            activeIndex = null;
                                            this.classList.remove('active');
                                            if (hintPanel) {
                                                hintPanel.textContent = 'Chọn tab hint để xem nội dung.';
                                            }
                                            return;
                                        }

                                        activeIndex = index;

                                        hintTabs.forEach((item) => item.classList.remove('active'));
                                        this.classList.add('active');

                                        if (hintPanel && typeof hintData[index] === 'string') {
                                            hintPanel.textContent = hintData[index];
                                        }
                                    });
                                });
                            })();
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

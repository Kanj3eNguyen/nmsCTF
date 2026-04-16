<!DOCTYPE html>
<html lang="en">
    <?php
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = rtrim(dirname($scriptName), '/');
    if ($basePath === '' || $basePath === '.') {
        $basePath = '';
    }
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - nmsCTF</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/auth.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .dashboard-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1200px;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            justify-content: center;
        }

        .dashboard-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 60px 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
            animation: slideUp 0.5s ease-out;
            text-align: center;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .greeting {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .greeting-emoji {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
            animation: wave 0.6s ease-in-out;
        }

        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(20deg); }
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 16px;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .dashboard-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
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

        .btn-secondary {
            background-color: var(--bg-input);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: var(--border-color);
            transform: translateY(-2px);
        }

        .welcome-icon {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            font-size: 40px;
        }

        @media (max-width: 640px) {
            .dashboard-card {
                padding: 40px 20px;
            }

            .greeting {
                font-size: 24px;
            }

            .greeting-emoji {
                font-size: 40px;
            }

            .dashboard-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    
    <div class="background-pattern"></div>
    
    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="welcome-icon">👋</div>
            <span class="greeting-emoji">✨</span>
            <h1 class="greeting">Xin Chào, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?>!</h1>
            <p class="subtitle">
                Chào mừng bạn đến với nmsCTF.<br>
                Sẵn sàng để chinh phục những thử thách bảo mật?
            </p>
            <div class="dashboard-actions">
                <button class="btn btn-primary" onclick="window.location.href='<?= htmlspecialchars($basePath . '/challenges', ENT_QUOTES, 'UTF-8') ?>'">
                    Xem Thử Thách
                </button>
                <button class="btn btn-secondary" onclick="window.location.href='<?= htmlspecialchars($basePath . '/profile', ENT_QUOTES, 'UTF-8') ?>'">
                    Hồ Sơ
                </button>
                <button class="btn btn-secondary" style="border-color: #ef4444; color: #ef4444;" onclick="window.location.href='<?= htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8') ?>'">
                    Đăng Xuất
                </button>
            </div>
        </div>
    </div>

</body>
</html>

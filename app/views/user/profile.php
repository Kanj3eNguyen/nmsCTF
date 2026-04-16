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
    <title>My Profile - nmsCTF</title>
    <!-- Tuỳ dùng chung CSS với auth hoặc tách file, ở đây tạm dùng auth.css nếu style chung -->
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/auth.css', ENT_QUOTES, 'UTF-8') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="background-pattern"></div>
    
    <div class="container">
        <div class="login-card">
            <div class="logo-section">
                <p class="tagline">Personal Information</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <script>
                    alert(<?= json_encode($error) ?>);
                </script>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <script>
                    alert(<?= json_encode($success) ?>);
                </script>
            <?php endif; ?>

            <form class="login-form" method="POST" action="<?= htmlspecialchars($basePath . '/profile/update', ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="form-group">
                    <label for="username">Username (Read-only)</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="username" 
                            value="<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            disabled
                            style="background-color: #2d3748; cursor: not-allowed;"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <!-- Icon email -->
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email"
                            value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-options" style="margin-top: 15px; justify-content: space-between;">
                    <a href="<?= htmlspecialchars($basePath . '/dashboard', ENT_QUOTES, 'UTF-8') ?>" class="forgot-link">← Back to Dashboard</a>
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 15px;">
                    <span class="btn-text">Update Profile</span>
                </button>
            </form>
        </div>
    </div>
    
</body>
</html>
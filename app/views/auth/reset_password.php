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
    <title>Reset Password - nmsCTF</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/auth.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="background-pattern"></div>
    
    <div class="container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo">
                    <span class="logo-icon">🚩</span>
                    <span class="logo-text">nms<span class="highlight">CTF</span></span>
                </div>
                <p class="tagline">Set A New Password</p>
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

            <form class="login-form" method="POST" action="<?= htmlspecialchars($basePath . '/reset-password', ENT_QUOTES, 'UTF-8') ?>">
                
                <p style="color: #a0aec0; font-size: 14px; margin-bottom: 20px; text-align: center;">
                    Please enter your new password to complete the reset process.
                </p>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrapper">
                        <!-- Icon pass -->
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter new password"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Confirm new password"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 15px;">
                    <span class="btn-text">Update Password</span>
                </button>
            </form>
        </div>
    </div>
    
</body>
</html>

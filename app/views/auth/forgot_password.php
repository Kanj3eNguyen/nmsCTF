
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
    <title>Forgot Password - nmsCTF</title>
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
                <p class="tagline">Reset your password</p>
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

            <form class="login-form" id="forgotPasswordForm" method="POST" action="<?= htmlspecialchars($basePath . '/forgot-password', ENT_QUOTES, 'UTF-8') ?>">
                
                <p style="color: #a0aec0; font-size: 14px; margin-bottom: 20px; text-align: center;">
                    Enter your email address and we'll send you an OTP to reset your password.
                </p>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <!-- Icon email tự thiết kế đồng bộ với form login -->
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email"
                            required
                        >
                    </div>
                </div>

                <div class="form-options">
                    <a href="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>" class="forgot-link">← Back to Login</a>
                </div>

                <button type="submit" class="submit-btn">
                    <span class="btn-text">Send OTP</span>
                    <span class="btn-loader" style="display: none;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" stroke-dasharray="60" stroke-dashoffset="60">
                                <animate attributeName="stroke-dashoffset" dur="1s" repeatCount="indefinite" from="60" to="0"/>
                            </circle>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>
    
</body>
</html>
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
    <title>2-Step Verification - nmsCTF</title>
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
                <p class="tagline">2-Step Verification</p>
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

            <form class="login-form" method="POST" action="<?= htmlspecialchars($basePath . '/login/2fa', ENT_QUOTES, 'UTF-8') ?>">
                
                <p style="color: #a0aec0; font-size: 14px; margin-bottom: 20px; text-align: center;">
                    Please enter the 6-digit OTP code sent to your email to continue.
                </p>

                <div class="form-group">
                    <label for="otp">OTP Code</label>
                    <div class="input-wrapper">
                        <!-- Icon pass/lock -->
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <input 
                            type="text" 
                            id="otp" 
                            name="otp" 
                            placeholder="Enter 6-digit OTP"
                            maxlength="6"
                            required
                        >
                    </div>
                </div>

                <div class="form-options">
                    <a href="<?= htmlspecialchars($basePath . '/login', ENT_QUOTES, 'UTF-8') ?>" class="forgot-link">← Cancel and return to Login</a>
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 15px;">
                    <span class="btn-text">Verify & Login</span>
                </button>
            </form>

            <form method="POST" action="<?= htmlspecialchars($basePath . '/login/2fa/resend', ENT_QUOTES, 'UTF-8') ?>" style="margin-top: 12px;">
                <button type="submit" class="submit-btn" style="background: #4a5568;">
                    <span class="btn-text">Resend OTP</span>
                </button>
            </form>
        </div>
    </div>
    
</body>
</html>
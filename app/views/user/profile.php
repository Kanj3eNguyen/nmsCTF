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
                    <label for="username">Username</label>
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

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            placeholder="Enter your full name"
                            value="<?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            placeholder="Enter your phone number"
                            value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        >
                    </div>
                </div>

                <div class="form-group" style="margin-top: 15px;">
                    <label style="display: flex; align-items: center; cursor: pointer; color: #cbd5e0;">
                        <input 
                            type="checkbox" 
                            name="is_2fa_enabled" 
                            value="1" 
                            <?= (isset($user['is_2fa_enabled']) && $user['is_2fa_enabled'] == 1) ? 'checked' : '' ?>
                            style="margin-right: 10px; width: 18px; height: 18px; accent-color: #4CAF50;"
                        >
                        Enable Two-Factor Authentication (2FA)
                    </label>
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
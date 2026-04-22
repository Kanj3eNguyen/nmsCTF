<!DOCTYPE html>
<html lang="en">
    <?php
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = rtrim(dirname($scriptName), '/');
    if ($basePath === '' || $basePath === '.') {
        $basePath = '';
    }
    $username = (string) ($user['username'] ?? 'User');
    $avatarInitial = strtoupper(substr($username, 0, 1));
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - nmsCTF</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath . '/assets/css/auth.css', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #0b1220;
            --bg-2: #111b2f;
            --panel: rgba(14, 24, 40, 0.86);
            --panel-2: rgba(17, 29, 48, 0.9);
            --border: rgba(129, 167, 255, 0.2);
            --text: #ecf2ff;
            --muted: #9db0d6;
            --brand: #45c4ff;
            --brand-2: #70ffb8;
            --danger: #ff8a8a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Manrope", sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 600px at -10% -10%, rgba(69, 196, 255, 0.18), transparent 60%),
                radial-gradient(1000px 500px at 100% 0%, rgba(112, 255, 184, 0.14), transparent 60%),
                linear-gradient(145deg, var(--bg-1), var(--bg-2));
            min-height: 100vh;
            padding: 36px 20px;
        }

        .profile-shell {
            max-width: 980px;
            margin: 0 auto;
            position: relative;
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(35, 58, 93, 0.65), rgba(20, 35, 56, 0.75));
            backdrop-filter: blur(8px);
            margin-bottom: 18px;
        }

        .profile-id {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 20px;
            color: #001425;
            background: linear-gradient(140deg, var(--brand), var(--brand-2));
            box-shadow: 0 10px 24px rgba(69, 196, 255, 0.35);
        }

        .title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: 0.2px;
        }

        .subtitle {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .go-back {
            text-decoration: none;
            color: #0c1d34;
            background: linear-gradient(140deg, #d2ecff, #b5ffdf);
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .profile-card {
            border: 1px solid var(--border);
            border-radius: 18px;
            background: linear-gradient(180deg, var(--panel), var(--panel-2));
            backdrop-filter: blur(10px);
            padding: 18px;
        }

        .sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .section {
            border: 1px solid rgba(157, 176, 214, 0.2);
            border-radius: 14px;
            padding: 14px;
            background: rgba(7, 14, 25, 0.35);
        }

        .section h3 {
            margin: 0 0 12px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #d8e6ff;
        }

        .form-group {
            margin-bottom: 12px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #d9e8ff;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #89a7d8;
            width: 17px;
            height: 17px;
            pointer-events: none;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1px solid rgba(137, 167, 216, 0.35);
            border-radius: 11px;
            background: rgba(6, 14, 24, 0.72);
            color: var(--text);
            padding: 11px 12px 11px 36px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(69, 196, 255, 0.18);
        }

        input[readonly],
        input[disabled] {
            background: rgba(56, 72, 97, 0.45);
            color: #c6d6f3;
            cursor: not-allowed;
        }

        .toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #d8e6ff;
            font-size: 13px;
        }

        .toggle input {
            width: 17px;
            height: 17px;
            accent-color: #58f0bd;
        }

        .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            border: 0;
            border-radius: 12px;
            padding: 11px 12px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
        }

        .btn-ghost {
            color: #d6e5ff;
            background: linear-gradient(135deg, rgba(62, 86, 120, 0.92), rgba(39, 56, 82, 0.92));
        }

        .btn-primary {
            color: #062035;
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
        }

        .hint {
            margin: 2px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.45;
        }

        @media (max-width: 860px) {
            .sections {
                grid-template-columns: 1fr;
            }

            .profile-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .go-back {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
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

    <div class="profile-shell">
        <div class="profile-header">
            <div class="profile-id">
                <div class="avatar"><?= htmlspecialchars($avatarInitial, ENT_QUOTES, 'UTF-8') ?></div>
                <div>
                    <h1 class="title">Profile Control Center</h1>
                    <p class="subtitle">@<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?> · Secure profile update with OTP verification</p>
                </div>
            </div>
            <a class="go-back" href="<?= htmlspecialchars($basePath . '/dashboard', ENT_QUOTES, 'UTF-8') ?>">Back to Dashboard</a>
        </div>

        <div class="profile-card">
            <form method="POST" action="<?= htmlspecialchars($basePath . '/profile/update', ENT_QUOTES, 'UTF-8') ?>">
                <div class="sections">
                    <div class="section">
                        <h3>Account Details</h3>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <div class="input-wrapper">
                                <input type="text" id="username" value="<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <p class="hint">Email update will be applied only after OTP verification.</p>
                        </div>

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" value="<?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="toggle">
                                <input type="checkbox" name="is_2fa_enabled" value="1" <?= (isset($user['is_2fa_enabled']) && $user['is_2fa_enabled'] == 1) ? 'checked' : '' ?>>
                                Enable Two-Factor Authentication (2FA)
                            </label>
                        </div>
                    </div>

                    <div class="section">
                        <h3>Security Verification</h3>

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <input type="password" id="current_password" name="current_password" placeholder="Current password if changing password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <input type="password" id="new_password" name="new_password" placeholder="New password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="otp">OTP Verification</label>
                            <div class="input-wrapper">
                                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <input type="text" id="otp" name="otp" placeholder="Enter OTP to confirm profile update" maxlength="6">
                            </div>
                            <p class="hint">Step 1: send OTP. Step 2: enter OTP and verify to apply changes.</p>
                        </div>

                        <div class="actions">
                            <button type="submit" name="action" value="send_otp" class="btn btn-ghost">Send OTP</button>
                            <button type="submit" name="action" value="verify_update" class="btn btn-primary">Verify OTP and Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
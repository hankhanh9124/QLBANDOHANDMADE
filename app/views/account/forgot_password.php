<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu | GÌ CŨNG MÓC</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-color: #22c55e;
            --primary-gradient: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --bg-gradient: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            --shadow-soft: 0 10px 25px -5px rgba(34, 197, 94, 0.1), 0 8px 10px -6px rgba(34, 197, 94, 0.1);
            --shadow-hover: 0 20px 25px -5px rgba(34, 197, 94, 0.2), 0 10px 10px -5px rgba(34, 197, 94, 0.1);
            --radius-lg: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            color: #1e293b;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            perspective: 1000px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid white;
        }

        .auth-card {
            background: white;
            padding: 40px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(34, 197, 94, 0.1);
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--primary-gradient);
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
            animation: slideIn 0.5s ease forwards;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
            color: #0f172a;
        }

        .subtitle {
            font-size: 15px;
            color: #64748b;
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            color: #334155;
            display: block;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: var(--transition);
        }

        .form-control {
            height: 54px;
            border-radius: 14px;
            border: 2px solid #f1f5f9;
            padding-left: 48px;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            background: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
            outline: none;
        }

        .form-control:focus + i {
            color: var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            height: 54px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 16px;
            width: 100%;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
            filter: brightness(1.1);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        /* OTP Inputs */
        .otp-wrapper {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            background: #f8fafc;
            transition: var(--transition);
        }

        .otp-input:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
            outline: none;
        }

        /* Countdown & Back Button */
        .footer-actions {
            text-align: center;
            margin-top: 25px;
        }

        .back-to-login {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
        }

        .back-to-login:hover {
            color: var(--primary-color);
        }

        .resend-timer {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .resend-link {
            color: var(--primary-color);
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .resend-link.disabled {
            color: #cbd5e1;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* Password Toggle */
        .pass-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            transition: var(--transition);
            z-index: 10;
        }

        .pass-toggle:hover {
            color: var(--primary-color);
        }

        /* Toast Notification */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-custom {
            background: white;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 5px solid var(--primary-color);
            transform: translateX(120%);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .toast-custom.show { transform: translateX(0); }
        .toast-custom.error { border-left-color: #ef4444; }
        .toast-custom.error i { color: #ef4444; }
        .toast-custom i { color: var(--primary-color); font-size: 20px; }
        .toast-content { font-weight: 600; font-size: 14px; color: #334155; }

        /* Loading Spinner */
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading .spinner { display: inline-block; }
        .loading span { display: none; }

        @media (max-width: 480px) {
            .auth-card { padding: 30px 20px; }
            .otp-input { width: 42px; height: 50px; font-size: 20px; }
        }
    </style>
</head>
<body>

    <div id="toast-container"></div>

    <div class="auth-container">
        <div class="logo-section">
            <img src="<?php echo BASE_URL; ?>public/images/logolen.png" alt="Logo">
            <h4 class="font-weight-bold" style="color: #16a34a;">GÌ CŨNG MÓC</h4>
        </div>

        <div class="auth-card">
            <!-- Step 1: Request Reset -->
            <div id="step-1" class="step active">
                <h1 class="title">Quên mật khẩu</h1>
                <p class="subtitle">Nhập email hoặc số điện thoại để nhận mã xác minh khôi phục tài khoản</p>
                
                <form id="form-request">
                    <div class="form-group">
                        <label>Email hoặc Số điện thoại</label>
                        <div class="input-icon-wrapper">
                            <input type="text" id="identifier" class="form-control" placeholder="example@gmail.com..." required>
                            <i class="fas fa-user-circle"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="btn-send-otp">
                        <div class="spinner"></div>
                        <span>Gửi mã xác minh</span>
                    </button>
                </form>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="step-2" class="step">
                <h1 class="title">Xác minh OTP</h1>
                <p class="subtitle">Mã xác minh đã được gửi đến bạn. Vui lòng kiểm tra và nhập vào bên dưới</p>
                
                <form id="form-otp">
                    <div class="otp-wrapper">
                        <input type="text" class="otp-input" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-input" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-input" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-input" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-input" maxlength="1" pattern="\d*">
                        <input type="text" class="otp-input" maxlength="1" pattern="\d*">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="btn-verify-otp">
                        <div class="spinner"></div>
                        <span>Xác minh mã</span>
                    </button>
                </form>

                <div class="footer-actions">
                    <p class="resend-timer">Không nhận được mã? <span id="timer">60s</span></p>
                    <a id="resend-link" class="resend-link disabled">Gửi lại mã ngay</a>
                </div>
            </div>

            <!-- Step 3: New Password -->
            <div id="step-3" class="step">
                <h1 class="title">Mật khẩu mới</h1>
                <p class="subtitle">Tạo mật khẩu mới mạnh mẽ hơn để bảo vệ tài khoản của bạn</p>
                
                <form id="form-reset">
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <div class="input-icon-wrapper">
                            <input type="password" id="new-password" class="form-control" placeholder="••••••••" required>
                            <i class="fas fa-lock"></i>
                            <i class="fas fa-eye pass-toggle" onclick="togglePass('new-password', this)"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Xác nhận mật khẩu</label>
                        <div class="input-icon-wrapper">
                            <input type="password" id="confirm-password" class="form-control" placeholder="••••••••" required>
                            <i class="fas fa-shield-alt"></i>
                            <i class="fas fa-eye pass-toggle" onclick="togglePass('confirm-password', this)"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="btn-reset-password">
                        <div class="spinner"></div>
                        <span>Cập nhật mật khẩu</span>
                    </button>
                </form>
            </div>

            <div class="footer-actions">
                <a href="<?php echo BASE_URL; ?>index.php?url=Page/login" class="back-to-login">
                    <i class="fas fa-arrow-left mr-2"></i> Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
        let countdown = 60;
        let timerId;

        // Toast Helper
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast-custom ${type === 'error' ? 'error' : ''}`;
            toast.innerHTML = `
                <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                <div class="toast-content">${message}</div>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        // State Management
        function goToStep(stepNum) {
            $('.step').removeClass('active');
            $(`#step-${stepNum}`).addClass('active');
            
            if(stepNum === 2) startTimer();
        }

        function startTimer() {
            countdown = 60;
            $('#resend-link').addClass('disabled');
            $('#timer').text(`60s`);
            
            if(timerId) clearInterval(timerId);
            timerId = setInterval(() => {
                countdown--;
                $('#timer').text(`${countdown}s`);
                if(countdown <= 0) {
                    clearInterval(timerId);
                    $('#resend-link').removeClass('disabled');
                    $('#timer').text('Sẵn sàng');
                }
            }, 1000);
        }

        // Toggle Password
        function togglePass(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Step 1: Send OTP
        $('#form-request').on('submit', function(e) {
            e.preventDefault();
            const identifier = $('#identifier').val();
            const btn = $('#btn-send-otp');
            
            btn.addClass('loading').prop('disabled', true);

            $.post(`${BASE_URL}index.php?url=Page/sendOTP`, { identifier }, function(res) {
                btn.removeClass('loading').prop('disabled', false);
                if(res.success) {
                    showToast(res.message);
                    goToStep(2);
                } else {
                    showToast(res.message, 'error');
                }
            });
        });

        // Step 2: OTP Inputs Logic
        $('.otp-input').on('input', function() {
            if (this.value) {
                $(this).next('.otp-input').focus();
            }
        });

        $('.otp-input').on('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value) {
                $(this).prev('.otp-input').focus();
            }
        });

        $('#form-otp').on('submit', function(e) {
            e.preventDefault();
            let otp = '';
            $('.otp-input').each(function() { otp += this.value; });
            
            if(otp.length < 6) {
                showToast('Vui lòng nhập đủ 6 chữ số.', 'error');
                return;
            }

            const btn = $('#btn-verify-otp');
            btn.addClass('loading').prop('disabled', true);

            $.post(`${BASE_URL}index.php?url=Page/verifyOTP`, { otp }, function(res) {
                btn.removeClass('loading').prop('disabled', false);
                if(res.success) {
                    showToast('Xác minh thành công!');
                    goToStep(3);
                } else {
                    showToast(res.message, 'error');
                }
            });
        });

        // Step 3: Reset Password
        $('#form-reset').on('submit', function(e) {
            e.preventDefault();
            const password = $('#new-password').val();
            const confirm = $('#confirm-password').val();

            if(password !== confirm) {
                showToast('Mật khẩu xác nhận không khớp.', 'error');
                return;
            }

            const btn = $('#btn-reset-password');
            btn.addClass('loading').prop('disabled', true);

            $.post(`${BASE_URL}index.php?url=Page/resetPassword`, { password }, function(res) {
                btn.removeClass('loading').prop('disabled', false);
                if(res.success) {
                    showToast(res.message);
                    setTimeout(() => {
                        window.location.href = `${BASE_URL}index.php?url=Page/login`;
                    }, 2000);
                } else {
                    showToast(res.message, 'error');
                }
            });
        });

        // Resend OTP
        $('#resend-link').on('click', function() {
            const identifier = $('#identifier').val();
            showToast('Đang gửi lại mã...');
            $.post(`${BASE_URL}index.php?url=Page/sendOTP`, { identifier }, function(res) {
                if(res.success) {
                    showToast(res.message);
                    startTimer();
                } else {
                    showToast(res.message, 'error');
                }
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Biodata - UPTD TEKKOM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            height: 100vh;
            overflow: hidden;
            background: url('forest 2.jpg') center center/cover no-repeat;
            position: relative;
        }

        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(2px);
        }

        .container {
            display: flex;
            height: 100vh;
            position: relative;
            z-index: 2;
        }

        .left-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 10rem;
            color: white;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(1px);
        }

        .title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 24px;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 16px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
            max-width: 400px;
        }

        .right-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(3px);
        }

        .signup-card {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(6px);
            border-radius: 16px;
            padding: 48px;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .signup-title {
            color: white;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 32px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 16px;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-bottom-color: #4CAF50;
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .password-section {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .password-group {
            flex: 1;
        }

        .or-divider {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            font-weight: 500;
        }

        .signup-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 32px;
        }

        .signup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }

        .signup-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
        }

        .social-login {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .social-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .left-section {
                padding: 40px 20px 20px;
                text-align: center;
            }
            
            .title {
                font-size: 36px;
            }
            
            .right-section {
                padding: 20px;
            }
            
            .signup-card {
                padding: 32px 24px;
            }
            
            .social-login {
                position: static;
                flex-direction: row;
                justify-content: center;
                margin-top: 24px;
                transform: none;
            }
        }
    </style>
</head>
<body>
    <div class="background-overlay"></div>

    <div class="container">
        <div class="left-section">
            <h1 class="title">Mari Mulai</h1>
            <p class="subtitle">
                Bergabunglah dengan kami dan mulai perjalanan Anda. 
                Dapatkan akses ke semua fitur eksklusif dan nikmati 
                pengalaman yang luar biasa.
            </p>
        </div>

        <div class="right-section">
            <div class="signup-card">
                <h2 class="signup-title">Daftar</h2>
                
                <form id="biodataForm">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-input" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-input" required>
                    </div>

                    <div class="password-section">
                        <div class="password-group">
                            <label class="form-label">Asal Sekolah</label>
                            <input type="text" name="asal_sekolah" class="form-input" placeholder="Masukkan asal sekolah" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Buat Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Minimal 6 karakter" required>
                    </div>

                    <button type="submit" class="signup-btn" id="submitBtn">Daftar</button>
                </form>

                <div class="login-link">
                    Sudah punya akun? <a href="#">Masuk di sini</a>
                </div>
                
                <div class="login-link" style="margin-top: 10px;">
                    <a href="data_biodata.php" style="color: #4CAF50;">Lihat Data Biodata</a>
                </div>
                
                
            </div>
        </div>

        <div class="social-login">
            <a href="#" class="social-btn">f</a>
            <a href="#" class="social-btn">t</a>
            <a href="#" class="social-btn">G</a>
        </div>
    </div>

    <script>
        // Animasi fokus yang halus
        const inputs = document.querySelectorAll('.form-input');
        
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateX(5px)';
                this.parentElement.style.transition = 'transform 0.3s ease';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateX(0)';
            });
        });

        // Handle form submission
        const form = document.getElementById('biodataForm');
        const submitBtn = document.getElementById('submitBtn');

        function showAlert(message, type) {
            // If success, redirect after 2 seconds
            if (type === 'success') {
                // Get the user's name from form data
                const formData = new FormData(form);
                const userName = formData.get('nama_lengkap');
                
                // Show simple alert for success
                alert('✓ ' + message + '\n\nAnda akan diarahkan ke halaman utama dalam 2 detik.');
                
                // Redirect to index.php with user name parameter
                setTimeout(() => {
                    window.location.href = 'index.php?welcome=' + encodeURIComponent(userName);
                }, 2000);
            } else {
                // Show error alert
                alert('❌ ' + message);
            }
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
            
            // Get form data
            const formData = new FormData(form);
            
            // Send to controller
            fetch('../config/app.php?action=add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    form.reset();
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Terjadi kesalahan. Silakan coba lagi.', 'error');
                console.error('Error:', error);
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Daftar';
            });
        });

        // Efek klik tombol
        submitBtn.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            
            this.style.position = 'relative';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        // CSS untuk animasi ripple
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Efek hover tombol sosial media
        const socialBtns = document.querySelectorAll('.social-btn');
        socialBtns.forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
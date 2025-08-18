<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบตรวจสอบคุณภาพน้ำตู้ปลา - เข้าสู่ระบบ / สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light-blue">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header bg-gradient-header p-4">
                        <h3 class="text-center font-weight-light my-4 text-white">
                            <i class="fas fa-water me-2"></i> ระบบตรวจสอบคุณภาพน้ำตู้ปลา
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <ul class="nav nav-pills nav-fill mb-4" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">เข้าสู่ระบบ</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab" aria-controls="pills-register" aria-selected="false">สมัครสมาชิก</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="pills-login-tab">
                                <form id="loginForm" autocomplete="off">
                                    <div class="mb-3">
                                        <label for="loginUsername" class="form-label">ชื่อผู้ใช้</label>
                                        <input type="text" class="form-control" id="loginUsername" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="loginPassword" class="form-label">รหัสผ่าน</label>
                                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ</button>
                                    </div>
                                    <div id="loginMessage" class="mt-3 alert d-none" role="alert"></div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="pills-register-tab">
                                <form id="registerForm" autocomplete="off">
                                    <div class="mb-3">
                                        <label for="regUsername" class="form-label">ชื่อผู้ใช้</label>
                                        <input type="text" class="form-control" id="regUsername" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="regEmail" class="form-label">อีเมล</label>
                                        <input type="email" class="form-control" id="regEmail" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="regPassword" class="form-label">รหัสผ่าน</label>
                                        <input type="password" class="form-control" id="regPassword" name="password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success btn-block">สมัครสมาชิก</button>
                                    </div>
                                    <div id="registerMessage" class="mt-3 alert d-none" role="alert"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const loginMessage = document.getElementById('loginMessage');
            const registerMessage = document.getElementById('registerMessage');

            function showMessage(messageDiv, message, isSuccess) {
                messageDiv.textContent = message;
                messageDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
                if (isSuccess) {
                    messageDiv.classList.add('alert-success');
                } else {
                    messageDiv.classList.add('alert-danger');
                }
            }

            loginForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                loginMessage.classList.add('d-none');

                try {
                    const formData = new FormData(this);
                    const response = await fetch('api/auth.php?action=login', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        window.location.href = 'dashboard.php';
                    } else {
                        showMessage(loginMessage, data.message, false);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showMessage(loginMessage, 'เกิดข้อผิดพลาดในการเชื่อมต่อหรือการประมวลผลข้อมูล', false);
                }
            });

            registerForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                registerMessage.classList.add('d-none');

                try {
                    const formData = new FormData(this);
                    const response = await fetch('api/auth.php?action=register', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        showMessage(registerMessage, data.message, true);
                        this.reset();
                        const loginTab = new bootstrap.Tab(document.getElementById('pills-login-tab'));
                        loginTab.show();
                    } else {
                        showMessage(registerMessage, data.message, false);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showMessage(registerMessage, 'เกิดข้อผิดพลาดในการเชื่อมต่อหรือการประมวลผลข้อมูล', false);
                }
            });
        });
    </script>
</body>
</html>
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
                                <button class="nav-link active" id="pills-login-tab" data-bs-toggle="pill" data-bs-target="#pills-login" type="button" role="tab" aria-controls="pills-login" aria-selected="true">
                                    <i class="fas fa-sign-in-alt me-2"></i> เข้าสู่ระบบ
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-register-tab" data-bs-toggle="pill" data-bs-target="#pills-register" type="button" role="tab" aria-controls="pills-register" aria-selected="false">
                                    <i class="fas fa-user-plus me-2"></i> สมัครสมาชิก
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="pills-login-tab">
                                <form id="loginForm">
                                    <div class="mb-3">
                                        <label for="loginUsername" class="form-label">ชื่อผู้ใช้</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="loginUsername" name="username" placeholder="กรอกชื่อผู้ใช้" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="loginPassword" class="form-label">รหัสผ่าน</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="กรอกรหัสผ่าน" required>
                                        </div>
                                    </div>
                                    <div id="loginMessage" class="alert d-none mt-3" role="alert"></div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg mt-3">เข้าสู่ระบบ</button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="pills-register-tab">
                                <form id="registerForm">
                                    <div class="mb-3">
                                        <label for="registerUsername" class="form-label">ชื่อผู้ใช้</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="registerUsername" name="username" placeholder="ตั้งชื่อผู้ใช้" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerEmail" class="form-label">อีเมล</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" class="form-control" id="registerEmail" name="email" placeholder="กรอกอีเมล" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerPassword" class="form-label">รหัสผ่าน</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="registerPassword" name="password" placeholder="ตั้งรหัสผ่าน" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label">ยืนยันรหัสผ่าน</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="ยืนยันรหัสผ่าน" required>
                                        </div>
                                    </div>
                                    <div id="registerMessage" class="alert d-none mt-3" role="alert"></div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success btn-lg mt-3">สมัครสมาชิก</button>
                                    </div>
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
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const messageDiv = document.getElementById('loginMessage');
            messageDiv.classList.add('d-none'); // ซ่อนข้อความเก่า

            try {
                const response = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('alert-danger', 'alert-warning');
                    messageDiv.classList.add('alert-success');
                    messageDiv.classList.remove('d-none');
                    window.location.href = 'dashboard.php'; // Redirect to dashboard
                } else {
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('alert-success');
                    messageDiv.classList.add('alert-danger');
                    messageDiv.classList.remove('d-none');
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
                messageDiv.classList.remove('alert-success');
                messageDiv.classList.add('alert-danger');
                messageDiv.classList.remove('d-none');
            }
        });

        document.getElementById('registerForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            const messageDiv = document.getElementById('registerMessage');
            messageDiv.classList.add('d-none'); // ซ่อนข้อความเก่า

            if (password !== confirmPassword) {
                messageDiv.textContent = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
                messageDiv.classList.remove('alert-success');
                messageDiv.classList.add('alert-warning');
                messageDiv.classList.remove('d-none');
                return;
            }

            try {
                const response = await fetch('api/auth.php?action=register', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('alert-danger', 'alert-warning');
                    messageDiv.classList.add('alert-success');
                    messageDiv.classList.remove('d-none');
                    this.reset(); // Clear the form
                    // Optionally switch to login tab
                    const loginTab = new bootstrap.Tab(document.getElementById('pills-login-tab'));
                    loginTab.show();
                } else {
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('alert-success');
                    messageDiv.classList.add('alert-danger');
                    messageDiv.classList.remove('d-none');
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
                messageDiv.classList.remove('alert-success');
                messageDiv.classList.add('alert-danger');
                messageDiv.classList.remove('d-none');
            }
        });
    </script>
</body>
</html>
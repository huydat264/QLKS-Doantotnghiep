<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

<style>
    /* Reset & Fonts */
    #registerModal { font-family: 'Montserrat', sans-serif; }
    #registerModal h2, #registerModal h3 { font-family: 'Playfair Display', serif; }

    /* Hiệu ứng làm mờ nền trang chủ */
    .modal-backdrop.show {
        backdrop-filter: blur(15px);
        background-color: rgba(0, 0, 0, 0.4);
    }

    /* Khung Modal */
    .auth-modal .modal-content {
        border: none;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    /* Chia đôi màn hình */
    .auth-split { display: flex; min-height: 600px; }

    /* BÊN TRÁI: FORM */
    .auth-form-side {
        flex: 1.2;
        padding: 60px;
        background: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* BÊN PHẢI: ẢNH NỀN */
    .auth-visual-side {
        flex: 1;
        background-image: url('https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?q=80&w=1932&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: 40px;
    }

    .auth-visual-side::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(99, 50, 95, 0.45);
    }

    .auth-visual-content { position: relative; z-index: 1; }
    .auth-visual-content h3 { font-size: 2.4rem; margin-bottom: 20px; font-style: italic; }

    /* Tinh chỉnh Input & Icon Mắt */
    .input-group-auth { position: relative; margin-bottom: 25px; }

    .auth-form-side .form-control {
        border-radius: 0;
        border: none;
        border-bottom: 1px solid #e0e0e0;
        padding: 12px 30px 12px 0; /* Chừa khoảng trống bên phải cho icon */
        font-size: 0.9rem;
        background: transparent;
        transition: 0.3s;
        width: 100%;
    }

    .auth-form-side .form-control:focus {
        box-shadow: none;
        border-bottom-color: #63325f;
    }

    .toggle-password {
        position: absolute;
        right: 0;
        top: 35px; /* Căn giữa theo chiều dọc của input */
        cursor: pointer;
        color: #999;
        transition: 0.3s;
    }

    .toggle-password:hover { color: #63325f; }

    /* Checkbox & Button */
    .btn-register-submit {
        background: #333;
        color: white;
        border-radius: 0;
        padding: 15px;
        width: 100%;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 2px;
        font-size: 0.8rem;
        border: none;
        margin-top: 25px;
        transition: 0.4s;
    }

    .btn-register-submit:hover {
        background: #63325f;
        letter-spacing: 3px;
    }

    .form-label-custom {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #999;
        margin-bottom: 0;
    }

    .custom-checkbox .form-check-input:checked {
        background-color: #63325f;
        border-color: #63325f;
    }
</style>

<div class="modal fade auth-modal" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0 position-relative">

                <button type="button" class="btn-close position-absolute top-0 end-0 m-4 z-3" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="auth-split">
                    <div class="auth-form-side">
                        <div class="mb-5">
                            <img src="https://www.sixsenses.com/Content/Images/logo-six-senses.svg" height="25" class="mb-4">
                            <h2 class="display-6">Tham gia cùng chúng tôi</h2>
                            <p class="text-muted small">Kiến tạo những khoảnh khắc đáng nhớ.</p>
                        </div>

                        <form action="#" method="POST">
                            @csrf

                            <div class="input-group-auth">
                                <label class="form-label-custom">Tên người dùng</label>
                                <input type="text" name="username" class="form-control" placeholder="Tên tài khoản của bạn" required>
                            </div>

                            <div class="input-group-auth">
                                <label class="form-label-custom">Mật khẩu</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                                <i class="bi bi-eye toggle-password" onclick="togglePass('password', this)"></i>
                            </div>

                            <div class="input-group-auth">
                                <label class="form-label-custom">Nhập lại Mật khẩu</label>
                                <input type="password" name="password_confirmation" id="password_confirm" class="form-control" placeholder="••••••••" required>
                                <i class="bi bi-eye toggle-password" onclick="togglePass('password_confirm', this)"></i>
                            </div>

                            <div class="form-check custom-checkbox mt-2">
                                <input class="form-check-input" type="checkbox" id="confirmAction" required>
                                <label class="form-check-label small text-muted" for="confirmAction" style="font-size: 0.8rem; cursor: pointer;">
                                    Xác nhận tạo tài khoản?
                                </label>
                            </div>

                            <button type="submit" class="btn btn-register-submit">Đăng ký ngay</button>
                        </form>

                        <div class="mt-5 text-center">
                            <p class="small text-muted">Đã có tài khoản?
                                <a href="#" class="text-dark fw-bold text-decoration-none ms-1 border-bottom border-dark" data-bs-dismiss="modal">ĐĂNG NHẬP</a>
                            </p>
                        </div>
                    </div>

                    <div class="auth-visual-side d-none d-lg-flex">
                        <div class="auth-visual-content">
                            <h3>The Sanctuary</h3>
                            <p>Tái tạo năng lượng • Kết nối tâm hồn</p>
                            <div class="mt-5">
                                <div style="width: 30px; height: 1px; background: white; margin: 0 auto 20px; opacity: 0.5;"></div>
                                <p style="text-transform: none; font-style: italic; font-size: 0.9rem;">"Đặc quyền bắt đầu từ sự chân thực."</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function togglePass(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>

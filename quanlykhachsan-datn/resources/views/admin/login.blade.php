<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Hệ thống Quản lý Khách sạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #0f172a; /* Nền tối mặc định chống trắng trang */
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        h2 { font-family: 'Playfair Display', serif; font-weight: 700; color: #0f172a; }

        .admin-login-container {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Khối bên trái: Chứa Form điền thông tin phẳng */
        .login-form-section {
            flex: 1;
            max-width: 550px;
            background: #ffffff;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 15px 0 30px rgba(0,0,0,0.15);
            z-index: 2;
        }

        /* Khối bên phải: Ảnh nền Resort cao cấp */
        .login-visual-section {
            flex: 1.5;
            background-image: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            padding: 100px;
        }

        .login-visual-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 27, 75, 0.8) 100%);
        }

        .visual-content {
            position: relative;
            color: #ffffff;
            z-index: 1;
            max-width: 650px;
        }

        .visual-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3.2rem;
            margin-bottom: 20px;
            line-height: 1.25;
        }

        /* Group Input đồng bộ với JS */
        .input-group-custom {
            position: relative;
            margin-bottom: 30px;
        }

        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #64748b;
            margin-bottom: 8px;
            display: block;
        }

        .form-control-custom {
            border: none;
            border-bottom: 2px solid #e2e8f0;
            border-radius: 0;
            padding: 12px 40px 12px 0;
            font-size: 1rem;
            font-weight: 500;
            background: transparent;
            color: #0f172a;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control-custom:focus {
            outline: none;
            box-shadow: none;
            border-bottom-color: #1e1b4b;
        }

        /* Định dạng lớp báo lỗi đỏ */
        .form-control-custom.is-invalid {
            border-bottom-color: #ef4444 !important;
        }

        .invalid-feedback-custom {
            font-size: 0.8rem;
            color: #ef4444;
            font-weight: 600;
            margin-top: 6px;
            display: block;
        }

        .toggle-password {
            position: absolute;
            right: 0;
            bottom: 12px;
            cursor: pointer;
            color: #94a3b8;
            font-size: 1.2rem;
        }

        /* Nút bấm */
        .btn-admin-submit {
            background: #0f172a;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            padding: 16px;
            width: 100%;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            font-size: 0.9rem;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-admin-submit:hover {
            background: #1e1b4b;
            box-shadow: 0 10px 20px rgba(30, 27, 119, 0.2);
        }

        .btn-admin-submit:disabled {
            background: #64748b;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            body { overflow: auto; height: auto; }
            .admin-login-container { flex-direction: column; height: auto; }
            .login-form-section { max-width: 100%; padding: 60px 24px; min-height: 100vh; }
            .login-visual-section { display: none !important; }
        }
    </style>
</head>
<body>

<div class="admin-login-container">

    <div class="login-form-section">
        <div class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-building-fill-gear text-dark fs-2 me-2"></i>
                <span class="fw-bold tracking-wider text-uppercase small" style="letter-spacing: 2px; color: #475569;">Hệ thống quản trị</span>
            </div>
            <h2>Đăng nhập Hệ thống</h2>
            <p class="text-muted small mb-0">Cung cấp tài khoản nội bộ dành cho Quản trị viên và Nhân viên vận hành khách sạn.</p>
        </div>

        <form id="adminLoginForm" action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="input-group-custom">
                <label class="form-label-custom">Tên tài khoản nội bộ</label>
                <input type="text" name="username" class="form-control-custom" placeholder="Nhập tên tài khoản của bạn" required autocomplete="off">
            </div>

            <div class="input-group-custom">
                <label class="form-label-custom">Mật mã truy cập</label>
                <input type="password" name="password" id="admin_password" class="form-control-custom" placeholder="••••••••" required>
                <i class="bi bi-eye toggle-password" onclick="togglePass()"></i>
            </div>

            <button type="submit" class="btn btn-admin-submit" id="btnSubmit">
                <span id="btnText">Xác thực hệ thống</span>
            </button>
        </form>

        <div class="mt-5 text-center">
            <p class="small text-muted mb-0">Nhầm khu vực?
                <a href="/" class="text-dark fw-bold text-decoration-none border-bottom border-dark pb-1">Quay lại Trang chủ User</a>
            </p>
        </div>
    </div>

    <div class="login-visual-section">
        <div class="visual-content">
            <span class="badge bg-light text-dark px-3 py-2 text-uppercase mb-3" style="letter-spacing: 2px; font-weight: 600; font-size: 0.7rem;">Hệ thống lõi v1.0.0</span>
            <h1>Hotel Manager Control Center</h1>
            <p class="lead opacity-75 mb-0" style="font-weight: 300; line-height: 1.6;">Quản trị tối ưu luồng đặt phòng, kiểm soát trang thiết bị, dịch vụ kèm theo và tổng hợp báo cáo doanh thu thông minh theo thời gian thực.</p>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function togglePass() {
        const input = document.getElementById('admin_password');
        const icon = document.querySelector('.toggle-password');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("bi-eye-slash", "bi-eye");
        }
    }

    $(document).ready(function() {
        $('#adminLoginForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let submitBtn = $('#btnSubmit');
            let btnText = $('#btnText');

            // Xóa trạng thái lỗi cũ
            $('.form-control-custom').removeClass('is-invalid');
            $('.invalid-feedback-custom').remove();

            submitBtn.prop('disabled', true);
            btnText.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Đang xác thực...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false);
                    btnText.text('Xác thực hệ thống');

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            let input = $(`[name="${key}"]`);
                            input.addClass('is-invalid');
                            // Chèn đúng cấu trúc class CSS mới để hiển thị chữ đỏ dưới input
                            input.closest('.input-group-custom').append(
                                `<div class="invalid-feedback-custom">${errors[key][0]}</div>`
                            );
                        });
                    } else {
                        alert('Không thể kết nối đến hệ thống cơ sở dữ liệu qlykhachsan.');
                    }
                }
            });
        });

        $('.form-control-custom').on('input', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('.input-group-custom').find('.invalid-feedback-custom').remove();
        });
    });
</script>
</body>
</html>

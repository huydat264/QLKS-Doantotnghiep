@extends('layouts.style')

@section('content')

<style>
    body {
        font-family: 'Montserrat', sans-serif;
        color: #555;
        background-color: #fcfbf9;
    }

    /* Hero Banner */
    .policy-hero {
        height: 50vh;
        min-height: 400px;
        position: relative;
        background: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop') center/cover fixed;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .policy-hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1;
    }
    .policy-hero-content {
        position: relative;
        z-index: 2;
        text-align: center;
        color: white;
    }
    .policy-hero-title {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }
    .policy-hero-subtitle {
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 4px;
        color: #eaddcf;
    }

    /* Layout Chính */
    .policy-container {
        padding: 80px 0;
    }

    /* Cột Nội Dung Chính sách */
    .policy-section-title {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        color: #2c3e50;
        font-weight: bold;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eaeaea;
    }
    .policy-block {
        background: #fff;
        padding: 40px;
        border-radius: 4px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        margin-bottom: 40px;
    }
    .policy-item h5 {
        font-family: 'Playfair Display', serif;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
        font-size: 1.2rem;
        margin-top: 25px;
    }
    .policy-item p, .policy-item li {
        line-height: 1.8;
        color: #666;
        font-size: 0.95rem;
    }
    .policy-item ul {
        padding-left: 20px;
        margin-bottom: 25px;
    }
    .policy-item li {
        margin-bottom: 10px;
    }

    /* Khối FAQ Accordion */
    .faq-accordion .accordion-item {
        border: none;
        border-bottom: 1px solid #eaeaea;
        background: transparent;
        margin-bottom: 10px;
    }
    .faq-accordion .accordion-button {
        background: transparent;
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        color: #2c3e50;
        padding: 20px 0;
        box-shadow: none;
        font-size: 1.05rem;
    }
    .faq-accordion .accordion-button:not(.collapsed) {
        color: #1f4e3d;
        background: transparent;
    }
    .faq-accordion .accordion-button:focus {
        border-color: none;
        box-shadow: none;
    }
    .faq-accordion .accordion-body {
        padding: 0 0 25px 0;
        color: #666;
        line-height: 1.8;
        font-size: 0.95rem;
    }

    /* Sidebar Navigation Desktop */
    .nav-pills-custom .nav-link {
        color: #666;
        font-weight: 600;
        padding: 12px 20px;
        border-radius: 0;
        border-left: 3px solid transparent;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
        margin-bottom: 10px;
        transition: 0.3s;
    }
    .nav-pills-custom .nav-link.active, .nav-pills-custom .nav-link:hover {
        background: transparent;
        color: #1f4e3d;
        border-left-color: #1f4e3d;
        background-color: rgba(31, 78, 61, 0.05);
    }
</style>

<!-- Hero Banner -->
<div class="policy-hero">
    <div class="policy-hero-overlay"></div>
    <div class="policy-hero-content" data-aos="fade-up">
        <h1 class="policy-hero-title">Điều Khoản & Chính Sách</h1>
        <div class="policy-hero-subtitle">Kim Boutique Hotel</div>
    </div>
</div>

<div class="container policy-container">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-5 mb-lg-0 d-none d-lg-block">
            <div class="sticky-top" style="top: 100px;">
                <div class="nav flex-column nav-pills nav-pills-custom" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link text-start active" id="v-pills-luutru-tab" data-bs-toggle="pill" data-bs-target="#v-pills-luutru" type="button" role="tab">Chính sách lưu trú</button>
                    <button class="nav-link text-start" id="v-pills-datphong-tab" data-bs-toggle="pill" data-bs-target="#v-pills-datphong" type="button" role="tab">Đặt phòng & Hoàn hủy</button>
                    <button class="nav-link text-start" id="v-pills-phaply-tab" data-bs-toggle="pill" data-bs-target="#v-pills-phaply" type="button" role="tab">Trách nhiệm & Pháp lý</button>
                    <button class="nav-link text-start" id="v-pills-faq-tab" data-bs-toggle="pill" data-bs-target="#v-pills-faq" type="button" role="tab">Câu hỏi thường gặp (FAQ)</button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            <div class="tab-content" id="v-pills-tabContent">

                <!-- Tab 1: Chính sách lưu trú -->
                <div class="tab-pane fade show active" id="v-pills-luutru" role="tabpanel">
                    <div class="policy-block">
                        <h2 class="policy-section-title">Quy Định Nhận / Trả Phòng</h2>
                        <div class="policy-item">
                            <h5>Thời gian tiêu chuẩn</h5>
                            <ul>
                                <li><strong>Giờ nhận phòng (Check-in):</strong> Từ 14:00 chiều.</li>
                                <li><strong>Giờ trả phòng (Check-out):</strong> Trước 12:00 trưa.</li>
                            </ul>
                            <h5>Nhận phòng sớm & Trả phòng trễ</h5>
                            <p>Yêu cầu nhận phòng sớm hoặc trả phòng trễ tùy thuộc vào tình trạng phòng trống của khách sạn và sẽ áp dụng phụ phí:</p>
                            <ul>
                                <li>Trả phòng từ 12:00 đến 18:00: Phụ thu 50% giá phòng một đêm.</li>
                                <li>Trả phòng sau 18:00: Phụ thu 100% giá phòng một đêm.</li>
                            </ul>
                            <h5>Yêu cầu giấy tờ</h5>
                            <p>Quý khách vui lòng xuất trình Căn cước công dân (đối với khách nội địa) hoặc Hộ chiếu hợp lệ (đối với khách quốc tế) khi làm thủ tục nhận phòng. Khách sạn có quyền từ chối phục vụ nếu không cung cấp đủ giấy tờ.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Đặt phòng và Hoàn hủy -->
                <div class="tab-pane fade" id="v-pills-datphong" role="tabpanel">
                    <div class="policy-block">
                        <h2 class="policy-section-title">Chính Sách Đặt Phòng & Hoàn Hủy</h2>
                        <div class="policy-item">
                            <h5>Thanh toán và Tiền cọc</h5>
                            <p>Để đảm bảo đặt phòng được xác nhận thành công, quý khách cần thanh toán trước khoản tiền cọc theo quy định của hệ thống.</p>
                            <div class="alert alert-danger mt-3 mb-4" style="background-color: #fdf3f4; color: #a94442; border: 1px solid #f2dede; border-radius: 8px;">
                                <strong>LƯU Ý QUAN TRỌNG:</strong> Tất cả các khoản tiền cọc đặt phòng tại Kim Boutique Hotel là <strong>KHÔNG HOÀN LẠI (Non-refundable)</strong> trong mọi trường hợp quý khách tự ý hủy phòng, vắng mặt (No-show) hoặc rút ngắn thời gian lưu trú.
                            </div>
                            <h5>Sử dụng Combo & Voucher</h5>
                            <p>Khách sạn chấp nhận các mã Voucher khuyến mãi và các gói Combo (phòng + dịch vụ) hợp lệ. Quý khách vui lòng nhập mã Voucher ở bước thanh toán. Các gói Combo không có giá trị quy đổi thành tiền mặt và không áp dụng đồng thời với các chương trình khuyến mãi khác trừ khi có quy định cụ thể.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Pháp lý và Tài sản -->
                <div class="tab-pane fade" id="v-pills-phaply" role="tabpanel">
                    <div class="policy-block">
                        <h2 class="policy-section-title">Trách Nhiệm Pháp Lý & Tài Sản</h2>
                        <div class="policy-item">
                            <h5>Bảo vệ tài sản cá nhân</h5>
                            <p>Khách sạn trang bị két sắt an toàn tại tất cả các phòng nghỉ. Quý khách vui lòng chủ động bảo quản tiền mặt, trang sức và các tài sản có giá trị khác trong két sắt.</p>
                            <p><strong>Miễn trừ trách nhiệm:</strong> Kim Boutique Hotel <strong>không chịu bất kỳ trách nhiệm pháp lý nào</strong> đối với việc mất mát, hư hỏng hoặc thất lạc tài sản, tư trang cá nhân của quý khách để bên ngoài két sắt, tại các khu vực công cộng hoặc do quý khách không khóa cửa phòng cẩn thận.</p>

                            <h5>Hư hỏng tài sản khách sạn</h5>
                            <p>Quý khách sẽ phải chịu trách nhiệm bồi thường tài chính 100% theo thời giá đối với bất kỳ sự cố tình hoặc vô ý nào gây hư hỏng, phá hoại cấu trúc, nội thất, trang thiết bị thuộc khuôn viên khách sạn.</p>

                            <h5>Chất cấm và An ninh</h5>
                            <p>Tuyệt đối nghiêm cấm việc mang theo, tàng trữ hoặc sử dụng chất ma túy, vũ khí, chất nổ, vật liệu dễ cháy nổ vào trong khuôn viên. Khách sạn sẽ lập tức báo cáo Cơ quan chức năng và mời quý khách rời đi không hoàn tiền nếu phát hiện vi phạm.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: FAQ -->
                <div class="tab-pane fade" id="v-pills-faq" role="tabpanel">
                    <div class="policy-block">
                        <h2 class="policy-section-title">Câu Hỏi Thường Gặp (FAQ)</h2>

                        <div class="accordion faq-accordion" id="accordionFAQ">

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true">
                                        1. Quy trình đặt phòng trực tuyến diễn ra như thế nào?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body">
                                        Hệ thống của chúng tôi yêu cầu quý khách phải có tài khoản để thực hiện đặt phòng. Quý khách chỉ cần bấm vào nút Đăng nhập/Đăng ký trên thanh menu, một cửa sổ <strong>popup tiện lợi</strong> sẽ hiện ra để quý khách thao tác mà không cần phải chuyển sang trang khác. Sau khi đăng nhập, quý khách có thể chọn phòng, thêm combo dịch vụ và tiến hành thanh toán bình thường.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false">
                                        2. Khách sạn có cung cấp dịch vụ đưa đón sân bay không?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body">
                                        Có, Kim Boutique cung cấp dịch vụ đưa đón sân bay Phú Quốc. Vui lòng liên hệ với bộ phận Lễ tân hoặc chọn thêm dịch vụ này trong phần <strong>Combo & Dịch vụ</strong> khi đặt phòng trực tuyến.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false">
                                        3. Tôi có thể mang theo thú cưng không?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body">
                                        Rất tiếc, để đảm bảo không gian nghỉ ngơi yên tĩnh và tiêu chuẩn vệ sinh khắt khe cho tất cả khách lưu trú, Kim Boutique Hotel hiện chưa hỗ trợ tiếp nhận thú cưng.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@include('user.dangky')
@include('user.dangnhap')

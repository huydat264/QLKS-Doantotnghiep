@extends('layouts.style')

@section('content')

<style>
    body { background-color: #f4f7f6; color: #4a4a4a; font-family: 'Montserrat', sans-serif; }
    .article-hero { height: 70vh; position: relative; background: url('https://vj-prod-website-cms.s3.ap-southeast-1.amazonaws.com/depositphotos54387583xl-1719191773890.jpg') center/cover fixed; }
    .article-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(10, 40, 30, 0.8)); }
    .article-hero-content { position: absolute; top: 50%; left: 0; transform: translateY(-50%); width: 100%; color: #fff; z-index: 2; padding: 0 20px; text-align: left; }
    .article-category { font-size: 0.85rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 20px; display: block; color: #a8d5ba; }
    .article-title { font-family: 'Playfair Display', serif; font-size: 3.5rem; line-height: 1.2; margin-bottom: 20px; font-weight: 700; }
    .article-body { max-width: 800px; margin: 0 auto; padding: 80px 20px; font-size: 1.1rem; line-height: 1.9; }
    .dropcap::first-letter { font-family: 'Playfair Display', serif; font-size: 4.5rem; float: left; margin: 10px 15px 0 0; line-height: 0.65; color: #1f4e3d; }
    .highlight-box { background-color: #ffffff; border-left: 5px solid #1f4e3d; padding: 30px; margin: 40px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .highlight-box h4 { font-family: 'Playfair Display', serif; color: #1f4e3d; margin-bottom: 15px; }
</style>

<div class="article-hero">
    <div class="article-hero-overlay"></div>
    <div class="article-hero-content" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <span class="article-category">Phát Triển Bền Vững</span>
                    <h1 class="article-title">Dự Án Phục Hồi Rạn San Hô & Sinh Thái Đảo Ngọc</h1>
                    <p class="lead">Gìn giữ viên ngọc bích của đại dương cho thế hệ mai sau.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="article-body">
        <p class="dropcap">Phú Quốc không chỉ quyến rũ bởi bờ cát trắng mịn, mà còn sở hữu một thế giới rực rỡ ẩn mình dưới đáy đại dương: quần thể rạn san hô Nam Đảo. Tuy nhiên, biến đổi khí hậu và du lịch chưa kiểm soát đã để lại những tổn thương sâu sắc cho hệ sinh thái nơi đây. Thấu hiểu điều đó, Kim Boutique Hotel đã khởi xướng chương trình "Hồi sinh San hô" dài hạn.</p>

        <p>Chúng tôi tự hào là khu nghỉ dưỡng tiên phong phối hợp cùng Viện Hải Dương Học và chính quyền địa phương triển khai vườn ươm san hô ngay tại vùng biển riêng của khách sạn. Hàng ngàn giá thể san hô đã được cấy ghép thành công, tạo nên ngôi nhà mới cho hàng trăm loài cá nhiệt đới và sinh vật biển quý hiếm.</p>

        <div class="highlight-box">
            <h4>Hành Động Của Chúng Tôi</h4>
            <ul class="mb-0 text-muted" style="line-height: 2;">
                <li><strong>Trồng rạn san hô nhân tạo:</strong> Cấy ghép hơn 5,000 nhành san hô mỗi năm.</li>
                <li><strong>Cam kết Không Rác Thải Nhựa:</strong> 100% không sử dụng nhựa dùng một lần trong mọi hoạt động của khách sạn.</li>
                <li><strong>Làm sạch bờ biển:</strong> Tổ chức chiến dịch "Green Beach" mỗi sáng Chủ Nhật hàng tuần với sự tham gia của nhân viên và du khách.</li>
            </ul>
        </div>

        <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80" alt="Coral Reef Phu Quoc" class="img-fluid rounded my-5 shadow-sm">

        <h3 class="mb-4" style="font-family: 'Playfair Display', serif; font-weight: bold; color: #1f4e3d;">Dấu ấn của bạn trong hành trình này</h3>
        <p>Mỗi vị khách lưu trú tại Kim Boutique đều đang đóng góp trực tiếp vào quỹ bảo vệ môi trường biển. Hơn thế nữa, quý khách có thể đăng ký tham gia lặn biển cùng chuyên gia sinh học của chúng tôi để tự tay gắn những nhành san hô non xuống đáy biển, để lại một di sản sống động thực sự sau kỳ nghỉ của mình.</p>

        <div class="text-center mt-5">
            <a href="/" class="btn btn-dark rounded-pill px-5 py-2 fw-bold" style="background-color: #1f4e3d; border:none; letter-spacing: 1px;">QUAY LẠI TRANG CHỦ</a>
        </div>
    </div>
</div>

@endsection

@include('user.dangky')
@include('user.dangnhap')

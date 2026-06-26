@extends('layouts.style')

@section('content')

<style>
    body { background-color: #fcfbf9; color: #4a4a4a; font-family: 'Montserrat', sans-serif; }
    .article-hero { height: 70vh; position: relative; background: url('https://images.trvl-media.com/lodging/94000000/93240000/93235700/93235601/4961892d.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill') center/cover fixed; }
    .article-hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.7)); }
    .article-hero-content { position: absolute; top: 50%; left: 0; transform: translateY(-50%); width: 100%; color: #fff; z-index: 2; padding: 0 20px; text-align: left; }
    .article-category { font-size: 0.85rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 20px; display: block; color: #e6d5b8; }
    .article-title { font-family: 'Playfair Display', serif; font-size: 3.5rem; line-height: 1.2; margin-bottom: 20px; font-weight: 700; }
    .article-body { max-width: 800px; margin: 0 auto; padding: 80px 20px; font-size: 1.1rem; line-height: 1.9; }
    .dropcap::first-letter { font-family: 'Playfair Display', serif; font-size: 4.5rem; float: left; margin: 10px 15px 0 0; line-height: 0.65; color: #2c3e50; }
    .article-quote { font-family: 'Playfair Display', serif; font-size: 1.8rem; line-height: 1.5; color: #2c3e50; text-align: center; padding: 40px; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; margin: 50px 0; }
    .article-image-full { width: 100vw; position: relative; left: 50%; transform: translateX(-50%); height: 60vh; object-fit: cover; margin: 60px 0; }
</style>

<div class="article-hero">
    <div class="article-hero-overlay"></div>
    <div class="article-hero-content" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <span class="article-category">Kiến trúc & Di sản</span>
                    <h1 class="article-title">Tôn Vinh Nét Mộc Mạc Của Làng Chài Bản Địa</h1>
                    <p class="lead">Hành trình mang hồn cốt Hàm Ninh vào không gian nghỉ dưỡng xa xỉ.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="article-body">
        <p class="dropcap">Nằm ẩn mình dưới những tán dừa cổ thụ của bãi biển nguyên sơ, Kim Boutique Hotel không chọn phô trương bằng những khối bê tông chọc trời. Chúng tôi chọn cách cúi mình trước thiên nhiên, mượn lấy hình bóng của những nếp nhà ngư dân tại Làng chài Hàm Ninh lịch sử để tạo nên một kiệt tác kiến trúc giao hòa giữa quá khứ và hiện tại.</p>

        <p>Từng vách tường, mái ngói tại khách sạn đều được thổi hồn bởi những vật liệu bản địa. Gỗ lim tái chế từ những con thuyền đánh cá cũ nát, mái lá dừa nước lợp thủ công bởi các nghệ nhân địa phương, và nền gạch bông mang đậm dấu ấn Đông Dương... tất cả hòa quyện tạo nên một không gian đượm mùi biển cả.</p>

        <img src="https://meingarten.vn/wp-content/uploads/2025/04/khach-san-4.jpg" alt="Resort Architecture" class="img-fluid rounded my-5 shadow-sm">

        <h3 class="mb-4" style="font-family: 'Playfair Display', serif; font-weight: bold; color: #2c3e50;">Triết lý "Không Gian Mở"</h3>
        <p>Thiết kế của Kim Boutique ưu tiên tối đa không gian mở, xóa nhòa ranh giới giữa bên trong và bên ngoài. Các khung cửa kính kịch trần cho phép ánh sáng tự nhiên ngập tràn, đồng thời mang luồng gió biển mang vị mặn mòi len lỏi vào từng góc phòng. Khách lưu trú có thể nghe rõ tiếng sóng vỗ rì rào ngay cả khi đang say giấc.</p>

        <div class="article-quote">
            "Sự xa xỉ thực sự không nằm ở sự hào nhoáng, mà nằm ở cảm giác an yên tuyệt đối khi bạn được trở về với những gì nguyên bản nhất của thiên nhiên."
        </div>

        <p>Trải nghiệm tại Kim Boutique không chỉ là một kỳ nghỉ, mà là một cuộc hành trình văn hóa. Hãy đến và cảm nhận nhịp đập của một Phú Quốc xa xưa, yên bình và trọn vẹn trong từng hơi thở.</p>

        <div class="text-center mt-5">
            <a href="{{ route('phong.user') }}" class="btn btn-outline-dark rounded-pill px-5 py-2 fw-bold" style="letter-spacing: 1px;">TRẢI NGHIỆM KHÔNG GIAN CỦA CHÚNG TÔI</a>
        </div>
    </div>
</div>

@endsection

@include('user.dangky')
@include('user.dangnhap')

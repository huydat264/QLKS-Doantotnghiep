@extends('layouts.style')

@section('content')

<style>
    body { background-color: #fff; color: #333; font-family: 'Montserrat', sans-serif; }
    .journal-header { padding: 120px 0 60px 0; background-color: #fcfbf9; text-align: center; display:flex; flex-direction:column; align-items:center; justify-content:center; }
    .journal-header h1 { font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 700; color: #2c3e50; margin-bottom: 20px; text-align:center; }
    .journal-header p { max-width: 600px; margin: 0 auto; color: #777; line-height: 1.6; text-align:center; }
    .journal-card { border: none; border-radius: 0; transition: 0.4s ease; cursor: pointer; margin-bottom: 40px; }
    .journal-card:hover .journal-img { transform: scale(1.05); }
    .journal-img-container { overflow: hidden; height: 300px; }
    .journal-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
    .journal-card-body { padding: 25px 0; }
    .journal-meta { font-size: 0.8rem; color: #999; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; margin-bottom: 10px; display: block; }
    .journal-card-title { font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: bold; color: #222; margin-bottom: 15px; line-height: 1.3; }
    .journal-card-text { color: #666; font-size: 0.95rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .read-more-link { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #2c3e50; text-decoration: none; border-bottom: 1px solid #2c3e50; padding-bottom: 2px; }
</style>

<div class="journal-header">
    <div class="container" data-aos="fade-up">
        <h1>Nhật Ký Kim Boutique</h1>
        <p>Tuyển tập những câu chuyện, khoảnh khắc đáng giá và kinh nghiệm độc bản từ những vị khách đã dừng chân tại chốn thiên đường Đảo Ngọc.</p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <!-- Bài viết 1 -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card journal-card">
                <div class="journal-img-container">
                    <img src="https://cdn3.ivivu.com/2018/08/ngam-hoang-hon-dep-nhat-thai-lan-tren-mui-da-cua-chua-ivivu-1.jpg" class="journal-img" alt="Sunset">
                </div>
                <div class="card-body journal-card-body">
                    <span class="journal-meta">Lãng Mạn • 15 Tháng 5, 2026</span>
                    <h5 class="journal-card-title">Săn Hoàng Hôn Tuyệt Mỹ Nhất Vịnh Thái Lan</h5>
                    <p class="journal-card-text">Hoàng hôn Phú Quốc không chỉ là sự thay đổi ánh sáng, nó là một bản giao hưởng của màu sắc. Cùng thưởng thức ly cocktail đặc trưng tại Bar ven biển và ngắm nhìn mặt trời đỏ rực chìm dần xuống lòng đại dương.</p>
                    <a href="#" class="read-more-link">Khám phá thêm</a>
                </div>
            </div>
        </div>

        <!-- Bài viết 2 -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card journal-card">
                <div class="journal-img-container">
                    <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/2d/4a/4e/ba/caption.jpg?w=900&h=500&s=1" class="journal-img" alt="Local Food">
                </div>
                <div class="card-body journal-card-body">
                    <span class="journal-meta">Ẩm Thực • 10 Tháng 5, 2026</span>
                    <h5 class="journal-card-title">Hương Vị Đại Dương: Từ Chợ Đêm Đến Bàn Tiệc Đẳng Cấp</h5>
                    <p class="journal-card-text">Theo chân Bếp trưởng của chúng tôi dạo quanh chợ hải sản Hàm Ninh lúc bình minh, tự tay chọn những nguyên liệu tươi ngon nhất và tìm hiểu triết lý ẩm thực 'Farm to Table' của Kim Boutique.</p>
                    <a href="#" class="read-more-link">Khám phá thêm</a>
                </div>
            </div>
        </div>

        <!-- Bài viết 3 -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card journal-card">
                <div class="journal-img-container">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=800&q=80" class="journal-img" alt="Spa and Wellness">
                </div>
                <div class="card-body journal-card-body">
                    <span class="journal-meta">Chữa Lành • 02 Tháng 5, 2026</span>
                    <h5 class="journal-card-title">Đánh Thức Giác Quan Với Liệu Trình Trị Liệu Thảo Mộc</h5>
                    <p class="journal-card-text">Sự kết hợp hoàn hảo giữa kỹ thuật massage ấn huyệt y học cổ truyền và nguồn thảo mộc bản địa Phú Quốc như tiêu sả, mang lại sự thư giãn sâu sắc cho cả cơ thể lẫn tâm trí.</p>
                    <a href="#" class="read-more-link">Khám phá thêm</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <button class="btn btn-outline-secondary rounded-pill px-4" style="letter-spacing: 1px; font-weight: bold; font-size: 0.85rem;">TẢI THÊM BÀI VIẾT</button>
    </div>
</div>

@endsection

@include('user.dangky')
@include('user.dangnhap')

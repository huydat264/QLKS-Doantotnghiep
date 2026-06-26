@extends('layouts.style')

@section('content')

<style>
    body {
        font-family: 'Playfair Display', serif;
        color: #555;
        background-color: #fdfbf9;
    }

    /* Hero Banner */
    .hero-activities {
        height: 75vh;
        position: relative;
        overflow: hidden;
    }
    .hero-activities img {
        width: 100%;
        height: 75vh;
        object-fit: cover;
        animation: subtleZoom 5s ease-in-out forwards;
    }
    @keyframes subtleZoom {
        0% { transform: scale(1); }
        100% { transform: scale(1.06); }
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 1;
    }
    .hero-content {
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 2;
    }
    .hero-title {
        font-size: 52px;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .hero-subtitle {
        font-size: 15px;
        font-family: sans-serif;
        text-transform: uppercase;
        letter-spacing: 3px;
        opacity: 0.9;
        margin-bottom: 10px;
        display: block;
    }

    /* Description Intro */
    .intro-section {
        padding: 70px 0 40px 0;
        text-center;
    }
    .intro-text {
        font-family: sans-serif;
        font-size: 16px;
        line-height: 1.8;
        color: #666;
        max-width: 850px;
        margin: 0 auto;
    }

    /* Luxury Horizontal Scroll Tabs Menu */
    .tabs-container {
        background: #fff;
        border-top: 1px solid #f1eeea;
        border-bottom: 1px solid #f1eeea;
        position: sticky;
        top: 80px; /* Sửa lại cho vừa khít với chiều cao header của mày */
        z-index: 99;
    }
    .nav-activity {
        display: flex;
        justify-content: center;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    .nav-activity::-webkit-scrollbar {
        display: none;
    }
    .nav-activity .nav-link {
        color: #888;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 1.5px;
        padding: 22px 25px;
        border: none;
        background: transparent;
        transition: 0.3s;
        text-transform: uppercase;
    }
    .nav-activity .nav-link:hover {
        color: #673065;
    }
    .nav-activity .nav-link.active {
        color: #673065;
        position: relative;
    }
    .nav-activity .nav-link.active::after {
        content: "";
        position: absolute;
        left: 25px;
        right: 25px;
        bottom: 0;
        height: 2px;
        background-color: #673065;
    }

    /* Activity Card Grid Layout */
    .activity-grid {
        padding: 60px 0 90px 0;
    }
    .activity-card {
        background: white;
        border: 1px solid #f1eeea;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .activity-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(103,48,101,0.05);
        border-color: #673065;
    }
    .activity-img-box {
        height: 250px;
        overflow: hidden;
        position: relative;
    }
    .activity-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .activity-card:hover .activity-img-box img {
        transform: scale(1.05);
    }
    .activity-body {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .activity-card-title {
        font-size: 20px;
        font-weight: 600;
        color: #222;
        margin-bottom: 12px;
        line-height: 1.4;
    }
    .activity-card-desc {
        font-family: sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #666;
        text-align: justify;
        margin-bottom: 0;
    }
</style>

<div class="hero-activities">
    <img src="https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?q=80&w=2070" alt="Phu Quoc Experiences">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-subtitle">Khám Phá Kimboutique Resort</span>
        <h1 class="hero-title">Trải Nghiệm Đảo Ngọc</h1>
    </div>
</div>

<div class="intro-section text-center">
    <div class="container">
        <p class="intro-text">
            Chào mừng quý khách đến với hành trình khám phá Phú Quốc đầy cảm hứng. Từ những ngôi làng cổ kính đậm đà bản sắc văn hóa địa phương, những rạn san hô rực rỡ sắc màu dưới lòng đại dương đến những hoạt động chăm sóc sức khỏe độc quyền đầy thư thái, Kimboutique đã thiết kế sẵn các trải nghiệm hoàn hảo để ghi dấu từng khoảnh khắc đáng nhớ của bạn.
        </p>
    </div>
</div>

<div class="tabs-container shadow-sm">
    <div class="container">
        <ul class="nav nav-activity" id="activityTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-vanhoa" data-bs-toggle="tab" data-bs-target="#pane-vanhoa" type="button" role="tab">Văn hóa</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-khampha" data-bs-toggle="tab" data-bs-target="#pane-khampha" type="button" role="tab">Khám phá</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-bien" data-bs-toggle="tab" data-bs-target="#pane-bien" type="button" role="tab">Biển</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-dactrung" data-bs-toggle="tab" data-bs-target="#pane-dactrung" type="button" role="tab">Đặc trưng</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-amthuc" data-bs-toggle="tab" data-bs-target="#pane-amthuc" type="button" role="tab">Ẩm thực</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-thethao" data-bs-toggle="tab" data-bs-target="#pane-thethao" type="button" role="tab">Thể thao</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-spa" data-bs-toggle="tab" data-bs-target="#pane-spa" type="button" role="tab">Chăm sóc sức khỏe</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-benvung" data-bs-toggle="tab" data-bs-target="#pane-benvung" type="button" role="tab">Phát triển bền vững</button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content" id="activityTabContent">

    <div class="tab-pane fade show active" id="pane-vanhoa" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://statics.vinpearl.com/nha-tu-phu-quoc-2_1627870564.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Di Tích Lịch Sử Nhà Tù Phú Quốc</h4>
                            <p class="activity-card-desc">Tìm hiểu và tri ân lòng quả cảm của các chiến sĩ cách mạng thông qua mô hình phục dựng sống động tại "Địa ngục trần gian" nổi tiếng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://statics.vinpearl.com/Chua-Ho-Quoc-Phu-Quoc_1747904779.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Chiêm Bái Chùa Hộ Quốc linh thiêng</h4>
                            <p class="activity-card-desc">Thiền viện trúc lâm lớn nhất Đảo Ngọc, tọa lạc tại vị trí tựa sơn hướng thủy tuyệt mỹ đón trọn ánh nắng bình minh của vịnh Thái Lan.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://onetour.vn/Media/Images/OneTour/tin-tuc/2018/11/ve-dep-yen-binh-cua-lang-chai-ham-ninh.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Thăm Làng Chài Cổ Hàm Ninh</h4>
                            <p class="activity-card-desc">Khám phá nếp sống yên ả bình dị mộc mạc nguyên sơ của ngư dân vùng biển dưới những nếp nhà tranh và những bến thuyền tấp nập.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://buulong.com.vn/wp-content/uploads/2026/04/dinh-cau-dinh-ba-phu-quoc-eed642.webp"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Ghé Thăm Dinh Cậu & Dinh Bà</h4>
                            <p class="activity-card-desc">Biểu tượng tín ngưỡng đặc trưng của Đảo Ngọc, nơi các ngư dân đến thắp hương cầu bình an biển lặng trước khi vươn khơi bám biển.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhhBdqSGcUsCXu97jO1mHdktB4TzMkHE7y00HQBDEM-EJ6IBtXs2fxjjg9Ow4qAmhKG77RHGUm3yivz7Kq8MzpU_DmML1_CAe5L8I05Qpve7-NNH1jsj2MeO4HQqWdgvjLzo7_Fbg3pwNE/s1296/thung+go+nuoc+mam+phu+quoc.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Nhà Thùng Nước Mắm Truyền Thống</h4>
                            <p class="activity-card-desc">Tận mắt chứng kiến quy trình ủ chượp cá cơm trong các thùng gỗ khổng lồ để tạo nên giọt nước mắm cốt đậm vị di sản độc đáo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://file.hstatic.net/1000350212/article/ang-suc-sang-trong-kya-jewel__14__1fcc1a5c88f348b0bb60f20ead3e4ff6_ecddd38af17c4cba883cee95e1ae93af.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Nghệ Thuật Nuôi Trai Lấy Ngọc</h4>
                            <p class="activity-card-desc">Khám phá quy trình cấy ghép tế bào tinh xảo vào vỏ trai và chiêm ngưỡng những viên ngọc biển trân quý lấp lánh đủ sắc màu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-khampha" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1448375240586-882707db888b?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Trekking Rừng Quốc Gia Phú Quốc</h4>
                            <p class="activity-card-desc">Băng qua những cung đường xanh ngút ngàn, khám phá thảm thực vật nhiệt đới nguyên sinh quý hiếm cùng các con suối mát lành.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://dulichphuquocsense.wordpress.com/wp-content/uploads/2015/08/mui-ganh-dau-phu-quoc-3.jpg?w=800"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Khai Phá Mũi Gành Dầu Hoang Sơ</h4>
                            <p class="activity-card-desc">Đứng từ mỏm đá ngắm trọn vẹn hải giới tự nhiên tuyệt đẹp tiếp giáp với Campuchia giữa làn nước trong xanh như pha lê.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://booking-static.vinpearl.com/tours/c5ec3b51fee04b3a8e714a47c1435883_d3f05396ff3243e0b2651d0de39648d1_1_vinpearl-safari-phu-quoc-ve-vao-cua.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Thám Hiểm Bán Hoang Dã Vinpearl Safari</h4>
                            <p class="activity-card-desc">Trải nghiệm ngồi xe chuyên dụng ngắm các loài động vật quý hiếm như hổ Bengal, tê giác châu Phi tự do đi lại giữa thiên nhiên.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://tgroup.vn/uploads/images/phu-quoc/phu_quoc_tgroup_travel%20(4)(1).jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Chèo Thuyền Kayak Trên Sông Cửa Cạn</h4>
                            <p class="activity-card-desc">Dọc theo dòng sông hiền hòa uốn lượn xuyên qua những cánh rừng ngập mặn tĩnh mịch, hít thở bầu không khí thuần khiết.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://statics.vinpearl.com/venice-phu-quoc_1773636948.JPG"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Khám Phá Sắc Màu Grand World</h4>
                            <p class="activity-card-desc">Mãn nhãn với những kiến trúc lấy cảm hứng từ Venice cổ kính lung linh sắc màu tại siêu quần thể thành phố không ngủ náo nhiệt.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://phuquocxanh.com/vi/wp-content/uploads/2016/09/hang-doi.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Chinh Phục Hang Dơi Phú Quốc</h4>
                            <p class="activity-card-desc">Hành trình thám hiểm mạo hiểm kỳ thú dành riêng cho những vị khách yêu thích cảm giác khám phá hang động tự nhiên huyền bí.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-bien" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://go2joy.s3.ap-southeast-1.amazonaws.com/blog/wp-content/uploads/2022/08/15154749/lan-bien-ngam-san-ho-hon-gam-ghi.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Lặn Ngắm San Hô Tại Hòn Gầm Ghì</h4>
                            <p class="activity-card-desc">Đắm mình trong làn nước ấm trong vắt, chiêm ngưỡng hệ sinh thái rạn san hô tự nhiên đa sắc màu lộng lẫy bậc nhất đảo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Thư Giãn Tại Bãi Sao Cát Trắng</h4>
                            <p class="activity-card-desc">Thả mình trọn vẹn trên bờ cát mịn màng như kem, ngắm nhìn hàng dừa xanh nghiêng bóng bên làn nước ngọc bích êm đềm.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://phuquoctravel.vn/wp-content/uploads/2023/09/tou-di-bo-duoi-bien-c1.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Đi Bộ Dưới Đáy Biển Nam Đảo</h4>
                            <p class="activity-card-desc">Trải nghiệm đội mũ dưỡng khí cao cấp dạo bước nhẹ nhàng dưới đáy biển sâu, tận tay chạm vào những rạn san hô và đàn cá rực rỡ.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/18/96/91/42/sunset-sanato-beach.jpg?w=1200&h=-1&s=1"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Ngắm Hoàng Hôn Sunset Sanato</h4>
                            <p class="activity-card-desc">Lưu giữ khoảnh khắc hoàng hôn huy hoàng lãng mạn bên những công trình nghệ thuật sắp đặt hình chú voi độc đáo ngay trên bãi biển.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://dulichviet.com.vn/images/bandidau/du-lich-cap-treo-hon-thom-phu-quoc-co-gi-choi.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Cáp Treo Hòn Thơm Vượt Biển</h4>
                            <p class="activity-card-desc">Thu vào tầm mắt toàn cảnh biển đảo An Thới bao la hùng vĩ từ cabin hệ thống cáp treo ba dây vượt biển dài nhất thế giới.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://puolotrip.com/images/pro/package-media-tour-phu-quoc-005-1624.png"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Tour Cano Khám Phá Quần Đảo An Thới</h4>
                            <p class="activity-card-desc">Lướt nhanh trên sóng nước ghé thăm những hòn đảo nhỏ biệt lập đẹp như thiên đường như Hòn Móng Tay, Hòn Mây Rút.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-dactrung" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://product.hstatic.net/200000637161/product/tour_sunset_cruise_1_44fc1a35f6174f6495c4660b3f771108.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Tiệc Tối Lãng Mạn Trên Du Thuyền 5 Sao</h4>
                            <p class="activity-card-desc">Thưởng thức ly champagne thượng hạng và thực đơn fine-dining giữa đại dương mênh mông rực hồng ánh hoàng hôn tuyệt mỹ.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://visitphuquoc.com.vn/google_docs_media/post/post-8288/AD_4nXepqO6N2Kl_wwpj0Is-XhNGduUTglFzzdDSpFmH8Pw9RxPgVzRw_2v1ar_JxJeswCmsiVS11cReqWXUcq2ok2yvHX2l3pwWBQAsmvWSBXiEKzWyPV19iUQs6e9RGN3gP-OViuyIb0mCZRsSH84xfs0h0i9Xxba3TlKomyOccJOZE1Cw4p4%3Ds2048.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Phiêu Lưu Bằng Xe Jeep Mui Trần</h4>
                            <p class="activity-card-desc">Hành trình mạo hiểm khác biệt đầy phóng khoáng xuyên qua các cung đường đất đỏ hoang dại của mảng rừng già Bắc Đảo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://avalo.vn/wp-content/uploads/2024/06/thiet-ke-rap-chieu-phim-ngoai-troi-5-70673-680x453.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Rạp Chiếu Phim Bãi Biển Dưới Ánh Sao</h4>
                            <p class="activity-card-desc">Đặc quyền thư giãn trên ghế lười êm ái sát bờ cát, thưởng thức bộ phim kinh điển hòa cùng tiếng sóng vỗ rì rào ban đêm.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://www.kkday.com/vi/blog/wp-content/uploads/KHAH7991.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Show Diễn Đa Phương Tiện "Kiss The Stars"</h4>
                            <p class="activity-card-desc">Chiêm ngưỡng kiệt tác nghệ thuật đỉnh cao kết hợp hiệu ứng lửa, nước, ánh sáng laser hiện đại hàng đầu thế giới tại Thị trấn Hoàng Hôn.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://duan-sungroup.com/wp-content/uploads/2022/10/tan-huong-hoang-hon-tuyet-dep-tren-cau-hon.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Tản Bộ Đón Hoàng Hôn Tại Cầu Hôn</h4>
                            <p class="activity-card-desc">Sải bước trên kiến trúc độc bản Kiss Bridge vươn dài ra khơi, đón trọn khoảnh khắc mặt trời rớt xuống chính giữa khe hở cây cầu.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://media-cdn-v2.laodong.vn/storage/newsportal/2024/12/29/1442365/Khinh-Khi-Cau-Vung-T.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Ngắm Trọn Đảo Ngọc Từ Khinh Khí Cầu</h4>
                            <p class="activity-card-desc">Bay bổng giữa không trung bao la và tận hưởng đặc quyền ngắm trọn vẹn dải đất thiên đường Phú Quốc từ độ cao lý tưởng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-amthuc" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://daivietourist.vn/wp-content/uploads/2025/08/cau-muc-dem-phu-quoc-2.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Trải Nghiệm Đêm Câu Mực Cùng Ngư Dân</h4>
                            <p class="activity-card-desc">Lên tàu vươn khơi khi màn đêm buông xuống, tự tay thả mồi câu mực và thưởng thức thành quả tươi rói ngay trên boong tàu.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://gonatour.vn/vnt_upload/news/08_2021/cho_dem_phu_quoc.png"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Food Tour Chợ Đêm Phú Quốc</h4>
                            <p class="activity-card-desc">Hòa mình vào không khí sầm uất, khám phá thiên đường ăn vặt với kẹo chỉ, bánh khọt và vô vàn món nướng thơm lừng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://phuquocxanh.com/vi/wp-content/uploads/2016/04/a3.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Thu Hoạch Tiêu Tại Vườn Tiêu Suối Đá</h4>
                            <p class="activity-card-desc">Tản bộ ngắm những hàng tiêu xanh mướt, tự tay hái những chùm tiêu chín đỏ và học cách làm muối tiêu dưỡng sinh nổi tiếng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Thử Rượu Sim & Lớp Học Pha Chế</h4>
                            <p class="activity-card-desc">Khám phá quy trình lên men thủ công quả sim rừng và tự tay pha chế cocktail mang hương vị sim đặc trưng độc bản.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://phuquocxanh.com/vi/wp-content/uploads/2017/02/thuong-thuc-goi-ca-trich-1.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Thưởng Thức Gỏi Cá Trích Hàm Ninh</h4>
                            <p class="activity-card-desc">Trải nghiệm món ăn quốc hồn quốc túy của Phú Quốc, kết hợp thịt cá tươi rói cùng dừa nạo và nước chấm đậu phộng béo bùi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://oms.hotdeal.vn/images/editors/sources/000367054019/367054-367054-body-bo-sung(3).jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Tiệc BBQ Hải Sản Bên Hồ Bơi Vô Cực</h4>
                            <p class="activity-card-desc">Thưởng thức tôm hùm, nhum biển, mực trứng được nướng trực tiếp bởi bếp trưởng trong không gian lộng gió đẳng cấp của resort.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-thethao" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1587174486073-ae5e5cff23aa?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Chơi Golf Tại Sân Vinpearl Golf Phú Quốc</h4>
                            <p class="activity-card-desc">Trải nghiệm những cú đánh gậy đỉnh cao tại sân golf 18 hố đẳng cấp quốc tế nằm ẩn mình giữa cánh rừng nguyên sinh thơ mộng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://cdn2.tuoitre.vn/471584752817336320/2023/11/28/2-17011572456541913506728.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Lướt Ván Phản Lực Jet Ski Mạo Hiểm</h4>
                            <p class="activity-card-desc">Chinh phục những con sóng bạc đầu với dòng môtô nước tốc độ cao đầy phấn khích trên làn nước trong xanh ngọc bích.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://image.vietgoing.com/article/large/trai-nghiem-cheo-sup-ngam-binh-minh-tren-vinh-bien-dep-nhat-nhi-viet-nam.gif"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Chèo Ván SUP Đón Bình Minh Bãi Trường</h4>
                            <p class="activity-card-desc">Tận hưởng cảm giác bình yên thư thái khi chèo SUP thả trôi nhẹ nhàng đón nhận những tia nắng sớm đầu tiên thắp sáng biển trời.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://irace.vn/wp-content/uploads/2021/05/jogging-in-the-sand.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Chạy Bộ Đón Gió Biển Bãi Dài</h4>
                            <p class="activity-card-desc">Rèn luyện sức khỏe bền bỉ trên đường chạy mịn màng dọc theo đường bờ biển bãi Dài nguyên sơ thoảng hương muối mặn mòi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://cdn.tgdd.vn/Files/2021/10/23/1392903/10-dia-diem-tuyet-voi-giup-trai-nghiem-vi-vu-bang-xe-dap-cung-nguoi-yeu-202110231133071287.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Đạp Xe Địa Hình Ven Biển</h4>
                            <p class="activity-card-desc">Sử dụng xe đạp địa hình cao cấp của resort để tự do khám phá các cung đường ven biển lộng gió hay len lỏi vào làng chài cổ.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://zapata.com.vn/wp-content/uploads/2024/12/du-keo-cano-1.webp"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Bay Dù Lượn Cano Kéo Trên Biển</h4>
                            <p class="activity-card-desc">Trải nghiệm cảm giác bay vút lên không trung bao la lộng gió bằng dù kéo cao tốc, ngắm nhìn đại dương lùi xa dưới chân.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-spa" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Yoga Bình Minh Trên Bãi Biển</h4>
                            <p class="activity-card-desc">Đón nhận nguồn năng lượng thuần khiết từ biển khơi qua các bài tập kéo giãn cơ thể tĩnh lặng dưới sự hướng dẫn của chuyên gia.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://herbalspa.vn/data/news/gallery/600/herbal-spa-signature-5-1777367654.webp"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Trị Liệu Ấn Huyệt Tinh Dầu Thảo Mộc</h4>
                            <p class="activity-card-desc">Thả lỏng mọi giác quan với liệu trình massage chuyên sâu kết hợp các loại tinh dầu tự nhiên chiết xuất từ thảo mộc quý của đảo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://shanhealth.vn/wp-content/uploads/2022/08/chuOng-xoay-himalaya-02-hinh-800x600_optimized.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Trị Liệu Âm Thanh Chuông Xoay Tây Tạng</h4>
                            <p class="activity-card-desc">Chữa lành tâm thức sâu sắc, xua tan căng thẳng mệt mỏi bằng những tần số rung động nguyên bản từ chuông xoay huyền bí.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://hometechvietnam.vn/wp-content/uploads/2023/11/tai-sao-nen-ngam-chan-thao-duoc.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Ngâm Chân Thảo Dược Ngoài Trời</h4>
                            <p class="activity-card-desc">Thư giãn đôi chân mệt mỏi trong bồn nước ấm ngập tràn lát gừng, sả nồng nàn thảo dược thiên nhiên giữa khu vườn xanh mát.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Lớp Học Detox & Sinh Tố Thanh Lọc</h4>
                            <p class="activity-card-desc">Học công thức độc quyền cân bằng dinh dưỡng, tự tay pha chế những ly sinh tố hữu cơ tươi lành tăng cường sức đề kháng.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://queenspa.com.vn/wp-content/uploads/2026/06/gia-massage-da-nong-1024x683.webp"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Massage Đá Nóng Giải Tỏa Thần Kinh</h4>
                            <p class="activity-card-desc">Sử dụng những viên đá núi lửa hấp nóng truyền nguồn năng lượng sâu vào hệ cơ, giải phóng hoàn toàn sự mỏi mệt tích tụ.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-benvung" role="tabpanel">
        <div class="container activity-grid">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://media-int.vnecdn.net/3784382/data/images/2018/07/28/VJ-cover_1532759205_VnEx_660x0.png"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Chiến Dịch Làm Sạch Biển "Green Phu Quoc"</h4>
                            <p class="activity-card-desc">Chung tay lan tỏa thông điệp xanh ý nghĩa, cùng nhân viên resort dọn dẹp và thu gom rác thải nhựa làm sạch bờ biển hoang sơ.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Tour Tham Quan Vườn Rau Hữu Cơ Resort</h4>
                            <p class="activity-card-desc">Tìm hiểu mô hình canh tác tuần hoàn không hóa chất, tự tay ủ phân hữu cơ từ rác thải nhà bếp thân thiện với môi trường.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://kobi.vn/wp-content/uploads/2025/05/workshop-nen-thom-du-an-handmade-3.jpg"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Lớp Học Tái Chế Nến Thơm Tự Nhiên</h4>
                            <p class="activity-card-desc">Tái sử dụng các nguyên liệu dầu ăn thừa hoặc sáp ong bỏ đi để chế tác thành những hũ nến thơm bãi biển xinh xắn lưu niệm.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Gieo Mầm Trồng Cây Xanh Lưu Niệm</h4>
                            <p class="activity-card-desc">Tự tay gieo trồng một cây xanh nhỏ tại khu vực bảo tồn của resort để lưu lại dấu ấn bảo vệ hệ sinh thái rừng ngập mặn Phú Quốc.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://images.unsplash.com/photo-1546026423-cc4642628d2b?q=80&w=1000"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Hội Thảo Bảo Vệ Rạn San Hô Đảo Ngọc</h4>
                            <p class="activity-card-desc">Lắng nghe chia sẻ chuyên sâu từ các nhà khoa học môi trường về tầm quan trọng và cách thức giữ gìn rạn san hô quý báu.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="activity-card">
                        <div class="activity-img-box"><img src="https://icdn.dantri.com.vn/mZ4CmU0ghSWg7wdoD7ro/Image/2014/03/DSC_0140-71bea.JPG"></div>
                        <div class="activity-body">
                            <h4 class="activity-card-title">Giờ Trái Đất Nhỏ Thắp Nến Tại Biệt Thự</h4>
                            <p class="activity-card-desc">Trải nghiệm 1 giờ tắt bớt thiết bị điện không cần thiết, sưởi ấm không gian biệt thự bằng ánh nến lãng mạn tiết kiệm năng lượng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

{{-- GẮN ĐẦY ĐỦ CÁC FILE HEADER & FOOTER ĐI KÈM POPUP MODAL ĐỂ CHẠY KHÔNG BỊ LỖI CLICK --}}
@include('user.dangky')
@include('user.dangnhap')

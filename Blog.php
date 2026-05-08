<?php
    $currentPage = 'Blog';
    include 'header.php';
    // Giả sử bạn đã có biến kết nối $conn từ header hoặc file connect
    // Lưu ý: Đừng đóng kết nối ($conn->close()) ở header nhé!
?>

<style>
    /* CSS Tùy chỉnh cho trang Blog P-Guitar */
    .blog-hero {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1510915361894-db8b60106cb1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        margin-bottom: 40px;
    }
    
    .pguitar-text {
        color: rgb(173, 157, 78);
        font-weight: bold;
    }

    .pguitar-bg {
        background-color: rgb(173, 157, 78);
        color: white;
    }

    .card {
        transition: transform 0.3s;
        border: none;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    .card-img-top {
        height: 200px;
        object-fit: cover;
    }
    
    .sidebar-widget {
        background: #f8f9fa;
        padding: 20px;
        margin-bottom: 30px;
        border-radius: 5px;
    }
    
    .btn-read-more {
        border: 1px solid rgb(173, 157, 78);
        color: rgb(173, 157, 78);
        background: transparent;
    }
    
    .btn-read-more:hover {
        background: rgb(173, 157, 78);
        color: white;
    }
</style>

<div class="blog-hero text-center">
    <div class="container">
        <h1 class="display-3 font-weight-bold">P-Guitar Blog</h1>
        <p class="lead">Chia sẻ đam mê, kiến thức và cảm hứng âm nhạc</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            
            <div class="card mb-4">
                <img src="https://images.unsplash.com/photo-1525201548942-d8732f6617a0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" class="card-img-top" style="height: 350px;" alt="Featured Guitar">
                <div class="card-body">
                    <h2 class="card-title">Top 5 Cây Guitar Acoustic Tốt Nhất Cho Người Mới Bắt Đầu 2024</h2>
                    <p class="text-muted"><small>Đăng bởi: Admin | Ngày: 19/12/2024</small></p>
                    <p class="card-text">Việc chọn mua cây đàn guitar đầu tiên rất quan trọng. Nó ảnh hưởng trực tiếp đến cảm hứng tập luyện của bạn. Dưới đây là danh sách 5 cây đàn "ngon - bổ - rẻ" tại P-Guitar mà bạn không thể bỏ qua...</p>
                    <a href="/html/top-5-guitar-cho-nguoi-moi.html" class="btn pguitar-bg">Đọc tiếp &rarr;</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1550291652-6ea9114a47b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="card-img-top" alt="Guitar strings">
                        <div class="card-body">
                            <h5 class="card-title">Hướng dẫn thay dây đàn đúng cách</h5>
                            <p class="card-text">Dây đàn bị rỉ sét sẽ làm hỏng âm thanh. Hãy xem hướng dẫn chi tiết...</p>
                            <a href="/html/huong-dan-thay-day.html" class="btn btn-read-more btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1460039230329-eb070fc6c77c?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="card-img-top" alt="Electric Guitar">
                        <div class="card-body">
                            <h5 class="card-title">Phân biệt Electric vs Acoustic</h5>
                            <p class="card-text">Bạn phù hợp với dòng nhạc nào? Rock bùng nổ hay Ballad nhẹ nhàng?</p>
                            <a href="/html/phan-biet-electric-acoustic.html" class="btn btn-read-more btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1564186763535-ebb21ef5277f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="card-img-top" alt="Music Theory">
                        <div class="card-body">
                            <h5 class="card-title">Nhạc lý cơ bản trong 10 phút</h5>
                            <p class="card-text">Nắm vững các hợp âm cơ bản để đệm hát bất kỳ bài nào bạn thích.</p>
                            <a href="/html/nhac-ly-co-ban.html" class="btn btn-read-more btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                 <div class="col-md-6">
                    <div class="card">
                        <img src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" class="card-img-top" alt="Maintenance">
                        <div class="card-body">
                            <h5 class="card-title">Bảo quản đàn trong mùa ẩm</h5>
                            <p class="card-text">Độ ẩm là kẻ thù số 1 của gỗ làm đàn. Mẹo bảo quản tại nhà.</p>
                            <a href="/html/bao-quan-dan.html" class="btn btn-read-more btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Trước</a></li>
                    <li class="page-item active"><a class="page-link pguitar-bg border-0" href="#">1</a></li>
                    
                    <li class="page-item"><a class="page-link text-dark" href="#">Sau</a></li>
                </ul>
            </nav>
        </div>

        <div class="col-lg-4">
            <div class="sidebar-widget text-center">
                <h5 class="pguitar-text">Về P-Guitar</h5>
                <p class="small">Chúng tôi cung cấp các loại nhạc cụ chất lượng cao và dịch vụ chăm sóc khách hàng tận tâm nhất.</p>
            </div>

           
        </div>
    </div>
</div>

<?php
    include 'footer.php';
?>
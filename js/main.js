// ==========================================
// QUẢN LÝ MODAL
// ==========================================
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "flex";
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "none";
}

// Đóng modal khi click ra ngoài vùng xám
window.onclick = function(e) {
    if (e.target.classList.contains('overlay')) {
        e.target.style.display = "none";
    }
}

// ==========================================
// QUẢN LÝ ĐĂNG XUẤT
// ==========================================
function confirmLogout() {
    // Xóa session nếu cần
    window.location.href = "index.php"; // Trở về trang chủ
}

// ==========================================
// QUẢN LÝ TABS (Dùng cho User & Login)
// ==========================================
function switchTab(tabClass, activeId, btnClass, clickedBtn) {
    // Ẩn tất cả nội dung tab
    document.querySelectorAll('.' + tabClass).forEach(tab => {
        tab.style.display = "none";
    });
    // Bỏ active tất cả các nút
    if(btnClass && clickedBtn) {
        document.querySelectorAll('.' + btnClass).forEach(btn => {
            btn.classList.remove("active");
        });
        clickedBtn.classList.add("active");
    }
    // Hiện tab được chọn
    document.getElementById(activeId).style.display = "block";
}

// ==========================================
// HOMEPAGE SLIDER
// ==========================================
let currentSlide = 0;
const slides = [
    "https://picsum.photos/1200/350?1",
    "https://picsum.photos/1200/350?2",
    "https://picsum.photos/1200/350?3",
    "https://picsum.photos/1200/350?4"
];

function showSlide(index) {
    const slideImg = document.getElementById("slideImg");
    const tabs = document.querySelectorAll(".hero-tab");
    
    if(!slideImg) return; // Bỏ qua nếu không ở trang chủ

    currentSlide = index;
    slideImg.src = slides[index];

    tabs.forEach(t => t.classList.remove("active"));
    if(tabs[index]) tabs[index].classList.add("active");
}

function nextSlide(step) {
    currentSlide += step;
    if (currentSlide < 0) currentSlide = slides.length - 1;
    if (currentSlide >= slides.length) currentSlide = 0;
    showSlide(currentSlide);
}

// Tự động chạy slider nếu có element #slideImg
if(document.getElementById("slideImg")) {
    setInterval(() => nextSlide(1), 5000);
}
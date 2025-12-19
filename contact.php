<?php
/**
 * Contact Page
 */

define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';

$currentPage = 'contact';
$pageTitle = 'Kontak Kami - ' . APP_NAME;

include 'includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="mb-2">Hubungi Kami</h1>
            <p class="text-muted">Kami siap membantu Anda</p>
        </div>

        <div class="row g-4">
            <!-- Contact Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Informasi Kontak</h5>
                        
                        <!-- WhatsApp -->
                        <div class="d-flex align-items-start mb-4">
                            <div class="bg-success bg-opacity-10 p-3 rounded me-3">
                                <i class="bi bi-whatsapp text-success fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">WhatsApp</h6>
                                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" 
                                   class="text-decoration-none text-success"
                                   target="_blank">
                                    +<?php echo WHATSAPP_NUMBER; ?>
                                </a>
                                <p class="text-muted small mb-0">Chat langsung dengan kami</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="d-flex align-items-start mb-4">
                            <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                <i class="bi bi-envelope text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Email</h6>
                                <a href="mailto:info@tokoonline.com" 
                                   class="text-decoration-none text-primary">
                                    info@tokoonline.com
                                </a>
                                <p class="text-muted small mb-0">Kirim pesan via email</p>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="d-flex align-items-start mb-4">
                            <div class="bg-warning bg-opacity-10 p-3 rounded me-3">
                                <i class="bi bi-geo-alt text-warning fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Alamat</h6>
                                <p class="text-muted mb-0">
                                    Jl. Contoh No. 123<br>
                                    Jakarta, Indonesia 12345
                                </p>
                            </div>
                        </div>

                        <!-- Jam Operasional -->
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 p-3 rounded me-3">
                                <i class="bi bi-clock text-info fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Jam Operasional</h6>
                                <p class="text-muted mb-0">
                                    Senin - Jumat: 08:00 - 17:00<br>
                                    Sabtu: 08:00 - 14:00<br>
                                    Minggu: Tutup
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Kirim Pesan</h5>
                        
                        <p class="text-muted mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Untuk komunikasi yang lebih cepat, silakan hubungi kami melalui WhatsApp dengan klik tombol di bawah:
                        </p>

                        <div class="alert alert-info border-0 mb-4">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Tips:</strong> Untuk pertanyaan seputar produk atau pemesanan, 
                            gunakan tombol WhatsApp di setiap halaman produk untuk langsung chat dengan kami.
                        </div>

                        <!-- Quick Actions -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode('Halo, saya ingin bertanya tentang produk'); ?>" 
                                   class="btn btn-gradient w-100 btn-lg"
                                   target="_blank">
                                    <i class="bi bi-whatsapp me-2"></i>
                                    Chat via WhatsApp
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="mailto:info@tokoonline.com" 
                                   class="btn btn-outline-primary w-100 btn-lg">
                                    <i class="bi bi-envelope me-2"></i>
                                    Kirim Email
                                </a>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- FAQ Section -->
                        <h6 class="mb-3">Pertanyaan yang Sering Diajukan</h6>
                        
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Bagaimana cara memesan produk?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Pilih produk yang Anda inginkan, klik tombol "Order via WhatsApp", 
                                        dan lakukan pemesanan langsung melalui chat WhatsApp dengan tim kami.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Apa saja metode pembayaran yang tersedia?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Kami menerima pembayaran melalui transfer bank, e-wallet, dan COD 
                                        untuk area tertentu. Detail pembayaran akan diinformasikan saat pemesanan.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Berapa lama waktu pengiriman?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Waktu pengiriman tergantung lokasi tujuan, biasanya 2-5 hari kerja 
                                        untuk Jabodetabek dan 5-10 hari kerja untuk luar kota.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Apakah bisa retur/tukar produk?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Ya, kami menerima retur/tukar produk dalam waktu 7 hari setelah 
                                        penerimaan dengan syarat dan ketentuan yang berlaku.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- WhatsApp Float Button -->
<a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>?text=<?php echo urlencode('Halo Toko Online Hijau, saya ingin bertanya.'); ?>" 
   class="whatsapp-float" 
   target="_blank"
   title="Chat via WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>

<?php include 'includes/footer.php'; ?>

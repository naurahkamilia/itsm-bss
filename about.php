<?php
/**
 * About Page
 */

define('APP_ACCESS', true);
require_once 'config/config.php';
require_once 'config/database.php';

$currentPage = 'about';
$pageTitle = 'Tentang ITSM - ' . APP_NAME;

include 'includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="mb-2">About Us</h1>
            <p class="text-muted">Kenali lebih dekat ITSM</p>
        </div>

        <div class="row g-4">
            <!-- About Content -->
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3"
                                 style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-blue) 100%);">
                                <i class="bi bi-envelope-paper fs-1 text-white"></i>
                            </div>
                        </div>

                        <h3 class="text-center mb-4">Welcome <?php echo APP_NAME; ?></h3>
                        
                        <p class="text-muted mb-3" style="text-align: justify;">
                            <strong><?php echo APP_NAME; ?></strong> adalah aplikasi berbasis web yang telah diimplementasikan di <strong>BMJ</strong> untuk memberikan layanan teknologi informasi kepada para pengguna. Seiring dengan 
                            perkembangan bisnis perusahaan, keberhasilan operasional sangat bergantung pada infrastruktur teknologi informasi (TI) serta sistem aplikasi yang berjalan dengan baik. Oleh karena itu, kemampuan perusahaan 
                            dalam mengelola layanan TI (IT Service) harus menjadi prioritas utama.
                        </p>

                        <p class="text-muted mb-3" style="text-align: justify;">
                            Di tengah persaingan bisnis yang semakin kompetitif dan pergeseran aktivitas bisnis ke layanan berbasis online, <strong>BMJ</strong> menyediakan sebuah alat bantu (tool) bernama <strong>ITSM</strong>. Implementasi 
                            ITSM di <strong>PT Bukit Muria Jaya </strong> menjadi solusi manajemen layanan TI yang bertujuan untuk meningkatkan kualitas layanan teknologi informasi dan sistem aplikasi agar lebih efektif dan efisien.
                        </p>

                        <p class="text-muted mb-3" style="text-align: justify;">
                           Dengan slogan <i>“IT or System Problem? Keep Calm, Use ITSM to Report It”</i>, ITSM digunakan sebagai media untuk mengajukan permintaan layanan, seperti dukungan atau helpdesk terkait permasalahan perangkat keras (hardware), 
                           perangkat lunak (software), maupun aplikasi SAP, serta pelaporan insiden yang terjadi selama penggunaan sistem di lingkungan BMJ.
                        </p>

                        <p class="text-muted mb-3" style="text-align: justify;">
                            Akses ke aplikasi ITSM sangat mudah, yaitu melalui browser yang tersedia pada PC atau notebook, seperti Internet Explorer, Mozilla Firefox, dan Google Chrome. Selain itu, pengguna juga dapat mengakses ITSM melalui shortcut 
                            yang telah disediakan pada desktop. Melalui aplikasi ITSM, pengguna dapat membuat request untuk mengajukan permintaan layanan. Request yang dibuat akan diproses oleh tim support admin, yang 
                            kemudian akan memberikan solusi atas permasalahan atau permintaan layanan yang dilaporkan.
                        </p>

                        <p class="text-muted mb-3" style="text-align: justify;">
                           Dalam periode tertentu, ITSM juga menyediakan laporan terkait kualitas layanan TI yang telah diberikan, sehingga dapat digunakan sebagai bahan evaluasi dan peningkatan layanan di masa mendatang.
                        </p>

                        <hr class="my-4">

                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

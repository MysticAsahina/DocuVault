<?php
session_start();
if (!isset($_SESSION['user_ID']) && !isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}
// Get student information directly from session
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../drawables/logo1.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../../css/about.css">
    <title>ABOUT</title>
    <style>
        .main-content {
            margin-top: 80px;
        }
    </style>
</head>

<body>
    <<?php 
    include '../includes/sidebar.php';
    include '../includes/topbar.php'; 
    ?>

    <div class="main-content">
        <section class="bg">
            <div class="content">
                <h1>ABOUT US</h1>
                <h2>PHINMA Araullo University</h2>
                <p>PHINMA Araullo University is a tertiary educational institution with campuses located in Cabanatuan,
                    Nueva Ecija and San Jose that offers quality and affordable education. What started as a simple law
                    school in 1950 is now a major educational institution that provides basic, tertiary education and
                    professional studies to Novo Ecijanos.</p>
                <p>PHINMA AU is one of the nine-member schools under the PHINMA Education system. It was acquired in
                    2004 as part of PHINMA’s thrust to provide better education to improve the lives of more Filipinos.
                    Through PHINMA Education’s management, we have been strengthening our academic programs and employing
                    non-traditional approaches to learning.</p>

                <h2>Digital Document Management and Archiving System</h2>
                <p>The Digital Document Management and Archiving System is designed to streamline how the Registrar’s
                    Office manages student records at Araullo University. This comprehensive system enables both students
                    and staff to request and manage documents, process enrollment and course registration, and maintain
                    compliance with institutional policies. Key features include:</p>
                <ul>
                    <li>Online Document Retrieval: Access records anytime, anywhere.</li>
                    <li>Secure Storage and Backup: Protect data with robust security measures and reliable recovery
                        options.</li>
                    <li>Access Control: Ensure that only authorized users can view or modify records.</li>
                    <li>Digital Submission: Allow students to upload and submit documents electronically for a smoother
                        review process.</li>
                </ul>
                <p>By automating these processes, the system not only improves efficiency but also significantly reduces
                    paperwork and administrative delays, offering a more organized approach to record management.</p>
                <hr class="guhit">
                <h2>Contact Information</h2>
                <div class="contct">
                    <i class="fa-solid fa-location-dot"></i>
                    <div class="firstrow">
                        <p>Maharlika Highway, Barangay H. Concepcion Sr.,</p>
                        <p>Cabanatuan City, 3100, Nueva Ecija</p>
                    </div>
                    <i class="fa-solid fa-mobile-screen-button"></i>
                    <div class="secondrow">
                        <p>+63 905-859-5921</p>
                        <p>+63 999-565-3180</p>
                    </div>
                    <i class="fa-solid fa-phone"></i>
                    <div class="thirdrow">
                        <p>(044) 940 3900</p>
                    </div>
                    <i class="fa-solid fa-envelope"></i>
                    <div class="fourthrow">
                        <p>info.au@phinmaed.com</p>
                    </div>
                </div>

                <hr class="guhit">
                <div class="socmed">
                    <a href="https://www.facebook.com/phinmaau/" target="_blank">
                        <i class="fa-brands fa-square-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/phinmaau/" target="_blank">
                        <i class="fa-brands fa-square-instagram"></i>
                    </a>
                    <a href="https://x.com/phinmaau" target="_blank">
                        <i class="fa-brands fa-square-x-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/user/phinmaed" target="_blank">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                    <a href="https://au.phinma.edu.ph/?fbclid=IwY2xjawIX5IdleHRuA2FlbQIxMAABHd03s9RuqOUNPTafT0WuAVmkCPsW-Sta7GLCSqYys9up464gGsneRtKFxQ_aem_UaQOWzIvUPDI0_R6If-yaw" target="_blank">
                        <i class="fa-solid fa-globe"></i>
                    </a>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
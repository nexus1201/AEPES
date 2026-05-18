<?php
// footer.php – common footer for all pages
?>

<style>
html, body {
    height: 100%;
    margin: 0;
    padding-bottom: 60px; /* reserve space for fixed footer */
}

.page-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.page-content {
    flex: 1;
}

.aepes-footer {
    left: 0;
    bottom: 0;
    width: 100%;
    padding: 16px 12px;
    text-align: center;
    background: #0b4dbb;
    color: #ffffff;
    font-size: 13px;
    line-height: 1.6;
}

.aepes-footer a {
    color: #ffd500;
    text-decoration: none;
    font-weight: bold;
    margin: 0 6px;
}

.aepes-footer a:hover {
    text-decoration: underline;
}

.aepes-footer .contact {
    margin-top: 6px;
    font-size: 12px;
    opacity: 0.95;
}

.aepes-social {
    margin-top: 8px;
}
</style>

<footer class="aepes-footer">
    © <?= date('Y') ?> Mandaluyong Manpower & Technical-Vocational Training Center (MMTVTC)<br>
    Automated Employee Performance Evaluation System (AEPES)

    <div class="aepes-social">
        <a href="/aepes/about.php">About MMTVTC</a> |
        <a href="https://mandaluyong.gov.ph" target="_blank">Official Website</a> |
        <a href="https://www.facebook.com/manpowertechvoctc?rdid=BSkEjQQLmAxGrXYP&share_url=https%3A%2F%2Fwww.facebook.com%2Fshare%2F1ALmKwbRed%2F#" target="_blank">Facebook</a>
    </div>

    <div class="contact">
        📞 Contact: +63 919 095 5443
    </div>
</footer>

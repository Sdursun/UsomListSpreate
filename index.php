<?php

function isIPAddress($value) {
    return filter_var($value, FILTER_VALIDATE_IP) !== false;
}

// USOM URL
$url = "https://www.usom.gov.tr/url-list.txt";

// USOM'dan veriyi oku
$data = file_get_contents($url);
if ($data === false) {
    die("Hata: USOM listesini okuyamadım.");
}

// Satır satır veriyi işle
$lines = explode("\n", $data);
$domains = [];
$ips = [];

foreach ($lines as $line) {
    $line = trim($line);
    
    if (isIPAddress($line)) {
        $ips[] = $line;
    } else {
        $domains[] = $line;
    }
}

// Domainleri ve IP'leri ayrı dosyalara kaydet
file_put_contents("domains.txt", implode("\n", $domains));
file_put_contents("ips.txt", implode("\n", $ips));

echo "İşlem tamamlandı. 'domains.txt' ve 'ips.txt' dosyaları oluşturuldu.";

?>

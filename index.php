<?php

function isIPAddress($value) {
    return filter_var($value, FILTER_VALIDATE_IP) !== false;
}

function extractDomain($url) {
    $parsedUrl = parse_url($url, PHP_URL_HOST);
    if (!$parsedUrl) {
        $parsedUrl = $url; // Eğer URL parse edilemiyorsa, olduğu gibi kullan
    }
    
    $parts = explode('.', $parsedUrl);
    $count = count($parts);

    if ($count <= 2) {
        return $parsedUrl; // Örneğin: google.com veya sa.com gibi kısa domainler
    }

    // Özel durum: *.co.uk, *.com.tr gibi iki parçalı alan adlarını doğru almak için
    $tlds = ['com.tr', 'co.uk', 'org.tr', 'gov.tr', 'net.tr', 'edu.tr'];
    $lastTwoParts = $parts[$count - 2] . '.' . $parts[$count - 1];

    if (in_array($lastTwoParts, $tlds)) {
        return $parts[$count - 3] . '.' . $lastTwoParts;
    }

    return $parts[$count - 2] . '.' . $parts[$count - 1]; // example.com, google.com gibi ana domaini alır
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
$urls = [];
$domains = [];
$ips = [];

foreach ($lines as $line) {
    $line = trim($line);

    if (isIPAddress($line)) {
        $ips[$line] = true; // IP Adreslerini kaydet
    } elseif (strpos($line, '.') !== false) {
        $parsedDomain = extractDomain($line);

        if ($parsedDomain === $line) {
            // Eğer doğrudan bir domain ise
            $domains[$parsedDomain] = true;
        } else {
            // URL ise
            $urls[$line] = true;
            $domains[$parsedDomain] = true; // URL içindeki ana domaini kaydet
        }
    }
}

// TXT dosyalarına yaz (tekrarlardan arındırılmış haliyle)
file_put_contents("urls.txt", implode("\n", array_keys($urls)));
file_put_contents("domains.txt", implode("\n", array_keys($domains)));
file_put_contents("ips.txt", implode("\n", array_keys($ips)));

echo "İşlem tamamlandı. 'urls.txt', 'domains.txt' ve 'ips.txt' dosyaları oluşturuldu.";

?>

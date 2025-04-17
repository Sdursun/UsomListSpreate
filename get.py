import requests
import re
import ipaddress

def is_ip(text):
    """IP adresini kontrol eder (IPv4 veya IPv6)"""
    try:
        ipaddress.ip_address(text.strip())
        return True
    except ValueError:
        return False

def is_domain_with_three_parts(text):
    """abc.site.com gibi 3 nokta ile ayrılan domain adreslerini kontrol eder"""
    pattern = r'^[a-zA-Z0-9-]+\.[a-zA-Z0-9-]+\.[a-zA-Z0-9-]+$'
    return bool(re.match(pattern, text.strip()))

def is_domain_with_single_dot(text):
    """abc.com gibi tek nokta ile ayrılan domain adreslerini kontrol eder"""
    pattern = r'^[a-zA-Z0-9-]+\.[a-zA-Z0-9-]+$'
    return bool(re.match(pattern, text.strip()))

def main():
    # URL'den içeriği al
    url = "https://usom.gov.tr/url-list.txt"
    try:
        response = requests.get(url)
        response.raise_for_status()  # HTTP hatalarını kontrol et
        content = response.text
    except requests.exceptions.RequestException as e:
        print(f"Hata oluştu: {e}")
        return

    # Satırları ayır
    lines = content.splitlines()
    
    # IP adreslerini ve domain adreslerini bul
    ip_addresses = []
    three_part_domains = []
    single_dot_domains = []
    
    for line in lines:
        line = line.strip()
        if not line:
            continue
            
        if is_ip(line):
            ip_addresses.append(line)
        elif is_domain_with_three_parts(line):
            three_part_domains.append(line)
        elif is_domain_with_single_dot(line):
            single_dot_domains.append(line)
    
    # IP adreslerini dosyaya kaydet
    with open("ip_list.txt", "w") as f:
        for ip in ip_addresses:
            f.write(f"{ip}\n")
    
    # 3 noktalı domain adreslerini dosyaya kaydet
    with open("url_list.txt", "w") as f:
        for domain in three_part_domains:
            f.write(f"{domain}\n")
    
    # Tek noktalı domain adreslerini dosyaya kaydet
    with open("domain_list.txt", "w") as f:
        for domain in single_dot_domains:
            f.write(f"{domain}\n")
    
    print(f"Toplam {len(ip_addresses)} IP adresi ip_list.txt dosyasına kaydedildi.")
    print(f"Toplam {len(three_part_domains)} 3 noktalı domain adresi url_list.txt dosyasına kaydedildi.")
    print(f"Toplam {len(single_dot_domains)} tek noktalı domain adresi domain_list.txt dosyasına kaydedildi.")

if __name__ == "__main__":
    main()
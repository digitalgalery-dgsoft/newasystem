#!/bin/bash
set -e

echo "=== MENGONFIGURASI PROTEKSI BERKAS LAMPIRAN DI NGINX ==="

REWRITE_FILE="/www/server/panel/vhost/rewrite/new.asystem.co.id.conf"

if [ -f "$REWRITE_FILE" ]; then
    # Backup file konfigurasi lama
    cp "$REWRITE_FILE" "${REWRITE_FILE}.bak_$(date +%Y%m%d_%H%M%S)"

    # Tulis aturan rewrite baru
    cat << 'EOF' > "$REWRITE_FILE"
# ==============================================================
# PROTEKSI AKSES BERKAS LAMPIRAN PRIVAT (WAJIB LEWAT LARAVEL AUTH)
# ==============================================================
location ~* ^/(lampiran|refcekfile|approval|prinsiple/ttdfileprinsiple) {
    rewrite ^ /index.php$is_args$query_string last;
}

location / {  
	try_files $uri $uri/ /index.php$is_args$query_string;  
}
EOF
    echo "✔ Aturan rewrite Nginx berhasil diperbarui."
    nginx -t
    nginx -s reload
    echo "✔ Nginx berhasil di-reload dengan proteksi lampiran aktif!"
else
    echo "⚠️ File rewrite tidak ditemukan di: $REWRITE_FILE"
fi

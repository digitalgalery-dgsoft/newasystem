# Panduan Pengembangan & Prosedur Update Codebase ASYSTEM

## 1. Aturan Wajib Setiap Selesai Melakukan Update / Pengerjaan Fitur:
1. **Catat Pembaruan di `UPDATE_PROGRESS.md`**:
   - Selalu tambahkan nomor poin update baru secara kronologis dan detail teknis perubahan yang telah dilakukan.
2. **Jalankan Graphify (Memory Knowledge Graph)**:
   - Wajib menjalankan `graphify .` (atau `python -m graphify .`) di root direktori project setiap kali update selesai.
   - Hal ini memastikan `graph.json`, `GRAPH_REPORT.md`, dan `graph.html` selalu merefleksikan struktur dan relasi kode terkini.
3. **Deploy ke Server Produksi (Jika diminta/perlu)**:
   - Gunakan `php scripts/deploy_production.php` untuk memutakhirkan server live (Server 3 `new.asystem.co.id`).

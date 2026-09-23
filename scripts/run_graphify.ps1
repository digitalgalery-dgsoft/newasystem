# Script otomatis untuk memperbarui Knowledge Graph Memory (Graphify)
Write-Host "=================================================================" -ForegroundColor Cyan
Write-Host "🚀 ASYSTEM - MEMPERBARUI KNOWLEDGE GRAPH MEMORY (GRAPHIFY)" -ForegroundColor Cyan
Write-Host "=================================================================" -ForegroundColor Cyan

# 1. Ekstraksi kode AST
Write-Host "[1/3] Mengekstraksi AST kode ke graph.json..." -ForegroundColor Yellow
python -m graphify . --code-only

# 2. Ekspor visualisasi HTML interaktif
Write-Host "[2/3] Mengekspor graph.html visual interaktif..." -ForegroundColor Yellow
python -m graphify export html --graph graphify-out/graph.json

# 3. Ekspor D3 Collapsible Tree HTML
Write-Host "[3/3] Mengekspor GRAPH_TREE.html..." -ForegroundColor Yellow
python -m graphify tree

Write-Host ""
Write-Host "✔ Knowledge Graph Memory berhasil dimutakhirkan!" -ForegroundColor Green
Write-Host "   - Data Graph: graphify-out/graph.json" -ForegroundColor Gray
Write-Host "   - Visual 2D/3D Graph: graphify-out/graph.html" -ForegroundColor Gray
Write-Host "   - Visual Tree: graphify-out/GRAPH_TREE.html" -ForegroundColor Gray

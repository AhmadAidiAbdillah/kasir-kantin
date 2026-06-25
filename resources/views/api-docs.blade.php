<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dokumentasi API Resource
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto space-y-8">
        <!-- Overview Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold text-xl">🔌</span>
                <h3 class="text-lg font-bold text-slate-800">Resource API Produk</h3>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">
                Platform menyediakan API Resource lengkap untuk data Produk. Endpoint ini mengembalikan data bertipe JSON dan dapat dikonsumsi oleh aplikasi eksternal maupun skrip AJAX.
            </p>
            <div class="bg-slate-50 p-4 rounded-xl text-xs space-y-2">
                <div class="flex items-center gap-2"><strong class="text-slate-600">Base URL:</strong> <code class="bg-slate-200/60 px-2 py-0.5 rounded text-indigo-600 font-mono">http://127.0.0.1:8000/api</code></div>
                <div class="flex items-center gap-2"><strong class="text-slate-600">Format Respon:</strong> <code class="bg-slate-200/60 px-2 py-0.5 rounded text-emerald-600 font-mono">application/json</code></div>
            </div>
        </div>

        <!-- Endpoints List -->
        <div class="space-y-6">
            <h4 class="text-base font-bold text-slate-700 uppercase tracking-wider">Daftar Endpoint API</h4>

            <!-- GET ALL -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between bg-slate-50/60 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 bg-emerald-500 text-white rounded-lg text-xs font-bold font-mono">GET</span>
                        <code class="text-sm font-bold text-slate-800 font-mono">/products</code>
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">Mengambil semua produk</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold uppercase text-slate-400">Parameter Kueri (Opsional)</h5>
                        <ul class="list-disc pl-5 text-xs text-slate-600 space-y-1">
                            <li><code class="font-mono">search</code>: Menyaring produk berdasarkan nama (pencarian teks).</li>
                            <li><code class="font-mono">store_id</code>: Menyaring produk yang terikat pada ID toko tertentu.</li>
                        </ul>
                    </div>
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold uppercase text-slate-400">Contoh Respon Sukses (200 OK)</h5>
                        <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs overflow-x-auto font-mono">
{
  "success": true,
  "message": "Daftar produk berhasil diambil.",
  "data": [
    {
      "id": 1,
      "store_id": 1,
      "category_id": 2,
      "name": "Nasi Goreng Spesial",
      "price": 15000,
      "stock": 49,
      "image": null,
      "is_active": 1,
      "created_at": "2026-06-14T12:37:23.000000Z",
      "updated_at": "2026-06-14T13:37:39.000000Z"
    }
  ]
}</pre>
                    </div>
                </div>
            </div>

            <!-- GET BY ID -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between bg-slate-50/60 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 bg-emerald-500 text-white rounded-lg text-xs font-bold font-mono">GET</span>
                        <code class="text-sm font-bold text-slate-800 font-mono">/products/{id}</code>
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">Mengambil detail produk berdasarkan ID</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold uppercase text-slate-400">Contoh Respon Sukses (200 OK)</h5>
                        <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs overflow-x-auto font-mono">
{
  "success": true,
  "message": "Detail produk berhasil diambil.",
  "data": {
    "id": 1,
    "store_id": 1,
    "category_id": 2,
    "name": "Nasi Goreng Spesial",
    "price": 15000,
    "stock": 49,
    "image": null,
    "is_active": 1
  }
}</pre>
                    </div>
                </div>
            </div>

            <!-- POST -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between bg-slate-50/60 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 bg-indigo-500 text-white rounded-lg text-xs font-bold font-mono">POST</span>
                        <code class="text-sm font-bold text-slate-800 font-mono">/products</code>
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">Menambah produk baru</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold uppercase text-slate-400">Payload Request (JSON / Form Data)</h5>
                        <pre class="bg-slate-50 p-4 rounded-xl text-xs font-mono text-slate-700">
{
  "store_id": 1,
  "category_id": 2,
  "name": "Es Jeruk Peras",
  "price": 8000,
  "stock": 50,
  "is_active": 1
}</pre>
                    </div>
                </div>
            </div>

            <!-- PUT -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between bg-slate-50/60 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 bg-yellow-500 text-white rounded-lg text-xs font-bold font-mono">PUT</span>
                        <code class="text-sm font-bold text-slate-800 font-mono">/products/{id}</code>
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">Memperbarui data produk</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold uppercase text-slate-400">Payload Request (Sebagian / Seluruhnya)</h5>
                        <pre class="bg-slate-50 p-4 rounded-xl text-xs font-mono text-slate-700">
{
  "price": 9500,
  "stock": 35
}</pre>
                    </div>
                </div>
            </div>

            <!-- DELETE -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between bg-slate-50/60 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 bg-red-500 text-white rounded-lg text-xs font-bold font-mono">DELETE</span>
                        <code class="text-sm font-bold text-slate-800 font-mono">/products/{id}</code>
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">Menghapus produk dari sistem</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="space-y-1">
                        <h5 class="text-xs font-bold uppercase text-slate-400">Contoh Respon Sukses (200 OK)</h5>
                        <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs font-mono">
{
  "success": true,
  "message": "Produk berhasil dihapus."
}</pre>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

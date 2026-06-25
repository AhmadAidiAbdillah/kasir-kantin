<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kasir POS - {{ $store->name ?? 'Toko' }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Operator: {{ auth()->user()->name }}</p>
            </div>
            
            <!-- Store Selector for Owner role -->
            @if(auth()->user()->role === 'owner' && $stores->count() > 1)
                <form action="{{ route('kasir') }}" method="GET" id="store-selector-form">
                    <select name="store_id" id="store_id_selector" onchange="document.getElementById('store-selector-form').submit()" class="rounded-xl border-slate-200 text-xs font-semibold text-slate-700">
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ $st->id == $storeId ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        @if(isset($error))
            <div class="bg-amber-50 border border-amber-200 text-amber-800 p-5 rounded-2xl">
                <h4 class="font-bold text-base mb-1">Pemberitahuan</h4>
                <p class="text-sm">{{ $error }}</p>
            </div>
        @else
            <!-- Error Alert Container for AJAX failures -->
            <div id="ajax-error-alert" class="hidden mb-6 rounded-2xl bg-red-50 border border-red-100 p-4 text-xs font-bold text-red-800 items-center justify-between shadow-sm animate-pulse">
                <span id="ajax-error-msg">⚠️ Terjadi kesalahan.</span>
                <button type="button" onclick="const el = document.getElementById('ajax-error-alert'); el.classList.add('hidden'); el.classList.remove('flex');" class="text-red-500 hover:text-red-800">&times;</button>
            </div>

            <!-- POS Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Catalog & Search (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Search Bar & Filters -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center gap-4">
                        <!-- AJAX Live Search Input -->
                        <div class="relative w-full sm:flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                🔍
                            </span>
                            <!-- Input ini memicu pencarian produk secara real-time via AJAX (Fetch API) tanpa reload halaman -->
                            <input type="text" id="live-search-input" placeholder="Ketik nama produk untuk mencari..." class="w-full pl-10 pr-4 py-3 border-slate-200 rounded-xl text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        </div>
                        
                        <!-- Search Loading Indicator -->
                        <div id="search-loading" class="hidden text-xs font-semibold text-slate-400 items-center gap-1.5">
                            <span class="animate-spin">🌀</span> Memuat...
                        </div>
                    </div>

                    <!-- Category Filter Buttons -->
                    @if($categories->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-sm category-filter active" data-category="all">
                                Semua Menu
                            </button>
                            @foreach($categories as $cat)
                                <button type="button" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 border border-slate-150 hover:bg-slate-50 transition-colors category-filter" data-category="{{ $cat->id }}">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Products Grid Container -->
                    <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($products as $prod)
                            <div class="product-card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow" 
                                 data-category-id="{{ $prod->category_id }}"
                                 data-id="{{ $prod->id }}"
                                 data-name="{{ $prod->name }}"
                                 data-price="{{ $prod->price }}"
                                 data-stock="{{ $prod->stock }}">
                                
                                <div class="h-32 bg-slate-100/70 flex items-center justify-center text-slate-400 text-xs font-semibold relative overflow-hidden">
                                    @if($prod->image)
                                        <img src="{{ asset('storage/' . $prod->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-3xl">🍔</span>
                                    @endif
                                    <span class="absolute top-3 right-3 px-2 py-0.5 bg-slate-800/80 text-white rounded text-[10px] font-bold">Stok: {{ $prod->stock }}</span>
                                </div>
                                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">{{ $prod->category->name ?? '-' }}</span>
                                        <h4 class="font-bold text-slate-800 text-base leading-tight">{{ $prod->name }}</h4>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <span class="font-extrabold text-slate-900">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                        <button type="button" class="add-to-cart-btn px-4 py-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-xl text-xs font-bold transition-all">
                                            + Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-slate-400">Tidak ada produk terdaftar di toko ini.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Right Column: Cart Panel (1/3 width) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between min-h-[640px] h-fit sticky top-24">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                <span>🛒</span> Keranjang POS
                            </h3>
                            <button type="button" id="clear-cart-btn" class="text-xs text-red-500 font-bold hover:underline">Clear All</button>
                        </div>

                        <!-- Reactive Cart Items List -->
                        <div id="cart-items-list" class="space-y-3 max-h-[260px] overflow-y-auto pr-1">
                            <p class="text-slate-400 text-xs text-center py-12">Belum ada item ditambahkan.</p>
                        </div>
                    </div>

                    <!-- Checkout Form (Intercepted by Javascript Fetch API) -->
                    <form id="pos-checkout-form" class="space-y-4 mt-6 pt-4 border-t border-slate-150/80">
                        <!-- Select Customer -->
                        <div class="space-y-1">
                            <label for="customer_id" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Member / Pelanggan</label>
                            <select name="customer_id" id="customer_id" class="w-full rounded-xl border-slate-200 text-xs text-slate-700">
                                <option value="">Pelanggan Walk-In (Umum)</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Select Payment Method -->
                        <div class="space-y-1">
                            <label for="payment_method" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="w-full rounded-xl border-slate-200 text-xs text-slate-700">
                                <option value="Cash">Cash / Tunai</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <!-- Discount and Tax Fields -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="discount_percent" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Diskon (%)</label>
                                <input type="number" id="discount_percent" min="0" max="100" value="0" class="w-full rounded-xl border-slate-200 text-xs text-slate-750">
                            </div>
                            <div class="space-y-1">
                                <label for="tax_percent" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pajak PPN (%)</label>
                                <input type="number" id="tax_percent" min="0" max="100" value="11" class="w-full rounded-xl border-slate-200 text-xs text-slate-750">
                            </div>
                        </div>

                        <!-- Calculations Box -->
                        <div class="bg-slate-50 p-4 rounded-xl space-y-2 text-xs text-slate-500 font-semibold border border-slate-100">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span id="summary-subtotal" class="font-mono text-slate-800">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-red-500">
                                <span>Potongan Diskon</span>
                                <span id="summary-discount" class="font-mono">- Rp 0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Pajak (PPN)</span>
                                <span id="summary-tax" class="font-mono text-slate-800">+ Rp 0</span>
                            </div>
                            <div class="flex justify-between text-sm font-extrabold text-slate-800 border-t border-slate-200 pt-2">
                                <span>Total Belanja</span>
                                <span id="summary-total" class="font-mono text-indigo-600 text-base">Rp 0</span>
                            </div>
                        </div>

                        <!-- Paid Cash Amount -->
                        <div class="space-y-1">
                            <label for="paid_cash" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Uang Bayar (Rp)</label>
                            <input type="number" id="paid_cash" min="0" placeholder="Ketik nominal bayar" class="w-full rounded-xl border-slate-200 text-sm text-slate-800" required>
                        </div>

                        <!-- Calculated Change -->
                        <div class="flex justify-between text-xs font-bold text-slate-500">
                            <span>Uang Kembali:</span>
                            <span id="summary-change" class="text-slate-800 font-mono text-sm">Rp 0</span>
                        </div>

                        <!-- Submit Buttons -->
                        <button type="submit" id="pos-submit-btn" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-600/10 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5">
                            <span>✓</span> Bayar &amp; Simpan
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- AJAX & Cart Reactive Interaction Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Cek elemen aktif
            const storeId = "{{ $storeId }}";
            const liveSearchInput = document.getElementById('live-search-input');
            const searchLoading = document.getElementById('search-loading');
            const productsGrid = document.getElementById('products-grid');
            const cartItemsList = document.getElementById('cart-items-list');
            const clearCartBtn = document.getElementById('clear-cart-btn');
            const discountInput = document.getElementById('discount_percent');
            const taxInput = document.getElementById('tax_percent');
            const paidCashInput = document.getElementById('paid_cash');
            const paymentMethodSelect = document.getElementById('payment_method');
            const checkoutForm = document.getElementById('pos-checkout-form');
            const categoryFilters = document.querySelectorAll('.category-filter');

            // Uang kembalian, total, subtotal spans
            const subtotalSpan = document.getElementById('summary-subtotal');
            const discountSpan = document.getElementById('summary-discount');
            const taxSpan = document.getElementById('summary-tax');
            const totalSpan = document.getElementById('summary-total');
            const changeSpan = document.getElementById('summary-change');

            let cart = [];

            // Helper format mata uang Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + Math.max(0, Math.floor(angka)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // 1. AJAX LIVE SEARCH PRODUK
            let debounceTimer;
            liveSearchInput?.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    performSearch(liveSearchInput.value);
                }, 300); // Debounce untuk menghemat beban query server
            });

            function performSearch(query) {
                searchLoading.classList.remove('hidden');
                searchLoading.classList.add('flex');
                
                // Memanggil endpoint API produk buatan kita secara AJAX
                fetch(`/api/products?store_id=${storeId}&search=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(json => {
                        searchLoading.classList.add('hidden');
                        searchLoading.classList.remove('flex');
                        if (json.success) {
                            renderProductsGrid(json.data);
                        }
                    })
                    .catch(err => {
                        searchLoading.classList.add('hidden');
                        searchLoading.classList.remove('flex');
                        console.error('Gagal memuat produk via AJAX:', err);
                    });
            }

            function renderProductsGrid(products) {
                if (products.length === 0) {
                    productsGrid.innerHTML = '<div class="col-span-full py-12 text-center text-slate-400">Tidak ada produk yang cocok.</div>';
                    return;
                }

                productsGrid.innerHTML = '';
                products.forEach(prod => {
                    const card = document.createElement('div');
                    card.className = 'product-card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow';
                    card.dataset.categoryId = prod.category_id;
                    card.dataset.id = prod.id;
                    card.dataset.name = prod.name;
                    card.dataset.price = prod.price;
                    card.dataset.stock = prod.stock;

                    const imageHtml = prod.image 
                        ? `<img src="/storage/${prod.image}" class="w-full h-full object-cover">` 
                        : `<span class="text-3xl">🍔</span>`;

                    card.innerHTML = `
                        <div class="h-32 bg-slate-100/70 flex items-center justify-center text-slate-400 text-xs font-semibold relative overflow-hidden">
                            ${imageHtml}
                            <span class="absolute top-3 right-3 px-2 py-0.5 bg-slate-800/80 text-white rounded text-[10px] font-bold">Stok: ${prod.stock}</span>
                        </div>
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">${prod.category ? prod.category.name : '-'}</span>
                                <h4 class="font-bold text-slate-800 text-base leading-tight">${prod.name}</h4>
                            </div>
                            <div class="flex items-center justify-between pt-2">
                                <span class="font-extrabold text-slate-900">${formatRupiah(prod.price)}</span>
                                <button type="button" class="add-to-cart-btn px-4 py-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-xl text-xs font-bold transition-all">
                                    + Tambah
                                </button>
                            </div>
                        </div>
                    `;
                    productsGrid.appendChild(card);
                });

                // Pasang ulang event listener untuk tombol tambah keranjang baru
                bindAddToCartButtons();
            }

            // 2. ADD TO CART AJAX / JS (Reaktif)
            function bindAddToCartButtons() {
                const addButtons = document.querySelectorAll('.add-to-cart-btn');
                addButtons.forEach(btn => {
                    // Mencegah duplikasi event listener
                    btn.replaceWith(btn.cloneNode(true));
                });

                // Pasang event listener baru
                document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const card = e.target.closest('.product-card');
                        const id = parseInt(card.dataset.id);
                        const name = card.dataset.name;
                        const price = parseInt(card.dataset.price);
                        const stock = parseInt(card.dataset.stock);

                        const existing = cart.find(item => item.id === id);

                        if (existing) {
                            if (existing.qty < stock) {
                                existing.qty++;
                            } else {
                                alert(`Batas stok tercapai. Stok produk "${name}" tersisa: ${stock}`);
                                return;
                            }
                        } else {
                            if (stock > 0) {
                                cart.push({ id, name, price, stock, qty: 1 });
                            } else {
                                alert(`Stok produk "${name}" kosong.`);
                                return;
                            }
                        }

                        renderCart();
                    });
                });
            }

            // Bind pencarian awal
            bindAddToCartButtons();

            // 3. UPDATE & REMOVE QUANTITY AJAX / JS (Reaktif)
            cartItemsList?.addEventListener('click', (e) => {
                const target = e.target;
                const id = parseInt(target.dataset.id);
                const item = cart.find(i => i.id === id);

                if (target.classList.contains('qty-minus')) {
                    if (item) {
                        item.qty--;
                        if (item.qty <= 0) {
                            cart = cart.filter(i => i.id !== id);
                        }
                        renderCart();
                    }
                } else if (target.classList.contains('qty-plus')) {
                    if (item) {
                        if (item.qty < item.stock) {
                            item.qty++;
                        } else {
                            alert('Batas stok tercapai.');
                        }
                        renderCart();
                    }
                } else if (target.classList.contains('remove-item')) {
                    cart = cart.filter(i => i.id !== id);
                    renderCart();
                }
            });

            clearCartBtn?.addEventListener('click', () => {
                cart = [];
                renderCart();
            });

            // Re-kalkulasi saat diskon, pajak, atau uang bayar dirubah
            [discountInput, taxInput, paidCashInput].forEach(input => {
                input?.addEventListener('input', updateCalculations);
            });

            paymentMethodSelect?.addEventListener('change', () => {
                const total = calculateTotal();
                if (paymentMethodSelect.value !== 'Cash') {
                    paidCashInput.value = total;
                } else {
                    paidCashInput.value = '';
                }
                updateCalculations();
            });

            function renderCart() {
                if (cart.length === 0) {
                    cartItemsList.innerHTML = '<p class="text-slate-400 text-xs text-center py-12">Belum ada item ditambahkan.</p>';
                    updateCalculations();
                    return;
                }

                cartItemsList.innerHTML = '';
                cart.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100';
                    row.innerHTML = `
                        <div class="space-y-0.5">
                            <h5 class="font-bold text-slate-800 text-xs leading-tight">${item.name}</h5>
                            <span class="text-[10px] text-slate-400 font-semibold">${formatRupiah(item.price)}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white">
                                <button type="button" class="qty-minus px-2 py-0.5 text-slate-500 font-bold hover:bg-slate-50" data-id="${item.id}">-</button>
                                <span class="px-2 text-xs font-bold text-slate-800">${item.qty}</span>
                                <button type="button" class="qty-plus px-2 py-0.5 text-slate-500 font-bold hover:bg-slate-50" data-id="${item.id}">+</button>
                            </div>
                            <button type="button" class="remove-item text-xs text-red-500 font-bold hover:underline" data-id="${item.id}">Hapus</button>
                        </div>
                    `;
                    cartItemsList.appendChild(row);
                });

                updateCalculations();
            }

            function calculateTotal() {
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                const discountPercent = parseInt(discountInput.value || 0);
                const taxPercent = parseInt(taxInput.value || 0);

                const discountAmount = subtotal * (discountPercent / 100);
                const taxAmount = (subtotal - discountAmount) * (taxPercent / 100);
                
                return subtotal - discountAmount + taxAmount;
            }

            function updateCalculations() {
                const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                const discountPercent = parseInt(discountInput.value || 0);
                const taxPercent = parseInt(taxInput.value || 0);

                const discountAmount = subtotal * (discountPercent / 100);
                const taxAmount = (subtotal - discountAmount) * (taxPercent / 100);
                const total = subtotal - discountAmount + taxAmount;

                subtotalSpan.textContent = formatRupiah(subtotal);
                discountSpan.textContent = '- ' + formatRupiah(discountAmount);
                taxSpan.textContent = '+ ' + formatRupiah(taxAmount);
                totalSpan.textContent = formatRupiah(total);

                const paid = parseInt(paidCashInput.value || 0);
                const change = paid - total;

                if (change >= 0) {
                    changeSpan.textContent = formatRupiah(change);
                    changeSpan.className = 'text-emerald-600 font-extrabold font-mono text-sm';
                } else {
                    changeSpan.textContent = 'Kurang ' + formatRupiah(Math.abs(change));
                    changeSpan.className = 'text-red-500 font-extrabold font-mono text-sm';
                }
            }

            // 4. CHECKOUT VIA FETCH API / AJAX
            checkoutForm?.addEventListener('submit', (e) => {
                e.preventDefault();

                if (cart.length === 0) {
                    alert('Keranjang belanja masih kosong!');
                    return;
                }

                const total = calculateTotal();
                const paid = parseInt(paidCashInput.value || 0);

                if (paid < total) {
                    alert('Jumlah uang pembayaran kurang dari total tagihan!');
                    return;
                }

                const submitBtn = document.getElementById('pos-submit-btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⚡ Memproses Transaksi...';

                // Menyusun body URLencoded secara dinamis
                const urlEncodedData = new URLSearchParams();
                urlEncodedData.append('store_id', storeId);
                urlEncodedData.append('customer_id', document.getElementById('customer_id').value);
                urlEncodedData.append('paid', paid);
                urlEncodedData.append('payment_method', paymentMethodSelect.value);
                urlEncodedData.append('discount_percent', discountInput.value || 0);
                urlEncodedData.append('tax_percent', taxInput.value || 0);

                // Tambahkan array keranjang produk
                cart.forEach((item, index) => {
                    urlEncodedData.append(`items[${index}][product_id]`, item.id);
                    urlEncodedData.append(`items[${index}][qty]`, item.qty);
                });

                // CSRF Token Laravel
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Mengirim transaksi secara AJAX via Fetch API ke route kasir.store
                fetch("{{ route('kasir.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: urlEncodedData.toString()
                })
                .then(async response => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>✓</span> Bayar &amp; Simpan';

                    // Jika Laravel memproses dengan sukses (diarahkan ke detail/invoice)
                    if (response.redirected) {
                        // Arahkan browser langsung ke resi cetak invoice yang dituju
                        window.location.href = response.url;
                    } else {
                        // Jika gagal, baca html respon untuk menemukan flash alert error
                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Menemukan element error dari flash banner halaman kasir
                        const errorMsgElement = doc.querySelector('.rounded-xl.bg-red-50');
                        const errorMsg = errorMsgElement ? errorMsgElement.textContent.trim() : 'Transaksi gagal diproses. Periksa saldo bayar.';
                        
                        showAJAXError(errorMsg);
                    }
                })
                .catch(err => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>✓</span> Bayar &amp; Simpan';
                    console.error('Error AJAX Checkout:', err);
                    showAJAXError('Terjadi kegagalan jaringan saat mengirim transaksi.');
                });
            });

            function showAJAXError(msg) {
                const errorAlert = document.getElementById('ajax-error-alert');
                const errorMsgSpan = document.getElementById('ajax-error-msg');
                errorMsgSpan.textContent = '⚠️ ' + msg;
                errorAlert.classList.remove('hidden');
                errorAlert.classList.add('flex');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            // Category buttons filter support
            categoryFilters.forEach(btn => {
                btn.addEventListener('click', () => {
                    const catId = btn.dataset.category;
                    categoryFilters.forEach(b => b.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm'));
                    categoryFilters.forEach(b => b.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-150'));

                    btn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                    btn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-150');

                    document.querySelectorAll('.product-card').forEach(card => {
                        if (catId === 'all' || card.dataset.categoryId === catId) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>

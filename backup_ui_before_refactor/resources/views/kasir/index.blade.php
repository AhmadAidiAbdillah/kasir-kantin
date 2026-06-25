<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kasir / POS - {{ $store->name ?? 'Toko' }}
                </h2>
                <p class="text-xs text-slate-400 mt-1">Kasir: {{ auth()->user()->name }}</p>
            </div>
            @if(auth()->user()->role === 'owner' && $stores->count() > 1)
                <form action="{{ route('kasir') }}" method="GET" id="store-selector-form">
                    <select name="store_id" onchange="document.getElementById('store-selector-form').submit()" class="rounded-xl border-slate-200 text-xs font-semibold text-slate-700">
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
            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Catalog (2 Columns) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Categories filter -->
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-800">Katalog Menu</h3>
                        <!-- simple search/filter bar -->
                    </div>
                    
                    @if($categories->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 text-white shadow-sm category-filter active" data-category="all">
                                Semua Menu
                            </button>
                            @foreach($categories as $cat)
                                <button type="button" class="px-4 py-2 rounded-xl text-xs font-semibold bg-white text-slate-600 border border-slate-150 hover:bg-slate-50 transition-colors category-filter" data-category="{{ $cat->id }}">
                                    {{ $cat->name }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($products as $prod)
                            <div class="product-card bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow" 
                                 data-category-id="{{ $prod->category_id }}"
                                 data-id="{{ $prod->id }}"
                                 data-name="{{ $prod->name }}"
                                 data-price="{{ $prod->price }}"
                                 data-stock="{{ $prod->stock }}">
                                
                                <div class="h-32 bg-slate-100/70 flex items-center justify-center text-slate-400 text-xs font-semibold relative">
                                    🛒 Menu Foto
                                    <span class="absolute top-3 right-3 px-2 py-0.5 bg-slate-800/80 text-white rounded text-[10px] font-bold">Stok: {{ $prod->stock }}</span>
                                </div>
                                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">{{ $prod->category->name ?? '-' }}</span>
                                        <h4 class="font-bold text-slate-800 text-base leading-tight">{{ $prod->name }}</h4>
                                    </div>
                                    <div class="flex items-center justify-between pt-2">
                                        <span class="font-extrabold text-slate-900">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                        <button type="button" class="add-to-cart-btn px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-bold transition-all">
                                            + Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-sm py-12 text-center col-span-full">Tidak ada menu/stok tersedia saat ini.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Right: Cart & Checkout (1 Column) -->
                <div class="bg-white rounded-2xl border border-slate-150 shadow-sm p-6 flex flex-col justify-between min-h-[600px] h-fit sticky top-24">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <span>🧾</span> Keranjang Belanja
                            </h3>
                            <button type="button" id="clear-cart-btn" class="text-xs text-red-500 font-semibold hover:underline">Kosongkan</button>
                        </div>

                        <!-- Cart Items Container -->
                        <div id="cart-container" class="space-y-4 max-h-[280px] overflow-y-auto pr-1">
                            <p class="text-slate-400 text-sm text-center py-12">Belum ada item dipilih.</p>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form action="{{ route('kasir.store') }}" method="POST" id="checkout-form" class="space-y-4 mt-6 pt-4 border-t border-slate-100">
                        @csrf
                        <input type="hidden" name="store_id" value="{{ $storeId }}">
                        
                        <!-- Hidden Inputs for Cart Items (Generated Dynamically via JS) -->
                        <div id="hidden-cart-inputs"></div>

                        <!-- Customer Selector -->
                        <div class="space-y-1">
                            <label for="customer_id" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pelanggan</label>
                            <select name="customer_id" id="customer_id" class="w-full rounded-xl border-slate-200 text-sm text-slate-700">
                                <option value="">Umum (Tanpa Member)</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone ?? '-' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Method -->
                        <div class="space-y-1">
                            <label for="payment_method" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Metode Pembayaran</label>
                            <select name="payment_method" id="payment_method" class="w-full rounded-xl border-slate-200 text-sm text-slate-700">
                                <option value="Cash">Cash / Tunai</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <!-- Total Tagihan -->
                        <div class="space-y-2.5 bg-slate-50 p-4 rounded-xl">
                            <div class="flex justify-between text-sm text-slate-500 font-medium">
                                <span>Subtotal</span>
                                <span id="cart-subtotal">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-base font-extrabold text-slate-900 border-t border-slate-200/60 pt-2">
                                <span>Total Tagihan</span>
                                <span id="cart-total" class="text-indigo-600">Rp 0</span>
                            </div>
                        </div>

                        <!-- Paid Field -->
                        <div class="space-y-1">
                            <label for="paid" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Uang Bayar (Rp)</label>
                            <input type="number" name="paid" id="paid" min="0" placeholder="Masukkan jumlah bayar" class="w-full rounded-xl border-slate-200 text-sm text-slate-800" required>
                        </div>

                        <!-- Perkiraan Kembalian -->
                        <div class="flex justify-between text-sm font-semibold text-slate-700">
                            <span>Kembalian</span>
                            <span id="cart-change" class="text-slate-900">Rp 0</span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="checkout-submit-btn" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-base font-bold shadow-lg shadow-indigo-600/10 hover:shadow-indigo-600/20 transition-all flex items-center justify-center gap-2">
                            <span>🛒</span> Proses Transaksi
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Scripts for POS Page Cart Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const categoryButtons = document.querySelectorAll('.category-filter');
            const productCards = document.querySelectorAll('.product-card');
            const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
            const cartContainer = document.getElementById('cart-container');
            const clearCartBtn = document.getElementById('clear-cart-btn');
            const cartSubtotal = document.getElementById('cart-subtotal');
            const cartTotal = document.getElementById('cart-total');
            const paidInput = document.getElementById('paid');
            const cartChange = document.getElementById('cart-change');
            const hiddenInputsContainer = document.getElementById('hidden-cart-inputs');
            const checkoutForm = document.getElementById('checkout-form');
            const paymentMethodSelect = document.getElementById('payment_method');

            let cart = [];

            function formatRupiah(angka) {
                return 'Rp ' + Math.max(0, Math.floor(angka)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Category Filtering
            categoryButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const catId = btn.dataset.category;
                    categoryButtons.forEach(b => b.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm'));
                    categoryButtons.forEach(b => b.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-150'));
                    
                    btn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                    btn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-150');

                    productCards.forEach(card => {
                        if (catId === 'all' || card.dataset.categoryId === catId) {
                            card.classList.remove('hidden');
                        } else {
                            card.classList.add('hidden');
                        }
                    });
                });
            });

            // Add to Cart
            addToCartButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const card = e.target.closest('.product-card');
                    const id = parseInt(card.dataset.id);
                    const name = card.dataset.name;
                    const price = parseInt(card.dataset.price);
                    const stock = parseInt(card.dataset.stock);

                    const existingItem = cart.find(item => item.id === id);

                    if (existingItem) {
                        if (existingItem.qty < stock) {
                            existingItem.qty++;
                        } else {
                            alert(`Stok produk "${name}" tidak mencukupi.`);
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

            // Remove from Cart
            cartContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-item-btn')) {
                    const id = parseInt(e.target.dataset.id);
                    cart = cart.filter(item => item.id !== id);
                    renderCart();
                } else if (e.target.classList.contains('qty-minus-btn')) {
                    const id = parseInt(e.target.dataset.id);
                    const item = cart.find(i => i.id === id);
                    if (item) {
                        item.qty--;
                        if (item.qty <= 0) {
                            cart = cart.filter(i => i.id !== id);
                        }
                        renderCart();
                    }
                } else if (e.target.classList.contains('qty-plus-btn')) {
                    const id = parseInt(e.target.dataset.id);
                    const item = cart.find(i => i.id === id);
                    if (item) {
                        if (item.qty < item.stock) {
                            item.qty++;
                        } else {
                            alert('Batas stok tercapai.');
                        }
                        renderCart();
                    }
                }
            });

            // Clear Cart
            clearCartBtn.addEventListener('click', () => {
                cart = [];
                renderCart();
            });

            // Calculate Change on Paid Input
            paidInput.addEventListener('input', updateSummary);

            // Automatically autofill exact amount on change of payment method if non-cash (QRIS/Transfer usually exact)
            paymentMethodSelect.addEventListener('change', () => {
                const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                if (paymentMethodSelect.value !== 'Cash') {
                    paidInput.value = total;
                } else {
                    paidInput.value = '';
                }
                updateSummary();
            });

            function renderCart() {
                if (cart.length === 0) {
                    cartContainer.innerHTML = '<p class="text-slate-400 text-sm text-center py-12">Belum ada item dipilih.</p>';
                    hiddenInputsContainer.innerHTML = '';
                    updateSummary();
                    return;
                }

                cartContainer.innerHTML = '';
                hiddenInputsContainer.innerHTML = '';

                cart.forEach((item, index) => {
                    // Render UI List item
                    const itemEl = document.createElement('div');
                    itemEl.className = 'flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100';
                    itemEl.innerHTML = `
                        <div class="space-y-1">
                            <h5 class="font-bold text-slate-800 text-sm leading-tight">${item.name}</h5>
                            <span class="text-xs text-slate-400 font-semibold">${formatRupiah(item.price)}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white">
                                <button type="button" class="qty-minus-btn px-2.5 py-1 hover:bg-slate-50 text-slate-600 font-bold text-sm" data-id="${item.id}">-</button>
                                <span class="px-2 text-xs font-bold text-slate-800">${item.qty}</span>
                                <button type="button" class="qty-plus-btn px-2.5 py-1 hover:bg-slate-50 text-slate-600 font-bold text-sm" data-id="${item.id}">+</button>
                            </div>
                            <button type="button" class="remove-item-btn text-xs text-red-500 font-semibold hover:underline" data-id="${item.id}">Hapus</button>
                        </div>
                    `;
                    cartContainer.appendChild(itemEl);

                    // Render Hidden Inputs for POST Request Form
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = `items[${index}][product_id]`;
                    idInput.value = item.id;
                    hiddenInputsContainer.appendChild(idInput);

                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `items[${index}][qty]`;
                    qtyInput.value = item.qty;
                    hiddenInputsContainer.appendChild(qtyInput);
                });

                updateSummary();
            }

            function updateSummary() {
                const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                cartSubtotal.textContent = formatRupiah(total);
                cartTotal.textContent = formatRupiah(total);

                if (paymentMethodSelect.value !== 'Cash' && paidInput.value == '') {
                    // Auto-fill paid amount for QRIS/Transfer if empty
                    paidInput.value = total;
                }

                const paid = parseInt(paidInput.value || 0);
                const change = paid - total;

                if (change >= 0) {
                    cartChange.textContent = formatRupiah(change);
                    cartChange.className = 'text-slate-900 font-extrabold';
                } else {
                    cartChange.textContent = 'Kurang ' + formatRupiah(Math.abs(change));
                    cartChange.className = 'text-red-500 font-extrabold';
                }
            }

            // Form Submit validation
            checkoutForm.addEventListener('submit', (e) => {
                if (cart.length === 0) {
                    e.preventDefault();
                    alert('Keranjang belanja masih kosong!');
                    return;
                }

                const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                const paid = parseInt(paidInput.value || 0);

                if (paid < total) {
                    e.preventDefault();
                    alert('Jumlah uang bayar tidak mencukupi total tagihan!');
                }
            });
        });
    </script>
</x-app-layout>

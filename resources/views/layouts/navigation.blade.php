<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    
    <!-- Logo + Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Left -->
            <div class="flex space-x-8 items-center">

                <a href="{{ route('dashboard') }}" class="font-semibold text-gray-700">
                    Dashboard
                </a>

                {{-- Menu Khusus ROLE --}}
                @if(auth()->user()->role === 'owner')
                    <a href="{{ route('owner.users') }}">Kelola User</a>
                    <a href="{{ route('categories.index') }}">Kategori</a>
                    <a href="{{ route('products.index') }}">Produk</a>
                    <a href="{{ route('toppings.index') }}">Topping</a>
                    <a href="{{ route('kasir') }}">Kasir</a>
                @elseif(auth()->user()->role === 'admin')
                    <a href="{{ route('categories.index') }}">Kategori</a>
                    <a href="{{ route('products.index') }}">Produk</a>
                    <a href="{{ route('toppings.index') }}">Topping</a>
                    <a href="{{ route('kasir') }}">Kasir</a>
                @elseif(auth()->user()->role === 'kasir')
                    <a href="{{ route('kasir') }}">Kasir</a>
                @endif

            </div>

            <!-- Right (User Menu) -->
            <div class="flex items-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600 hover:text-red-800">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

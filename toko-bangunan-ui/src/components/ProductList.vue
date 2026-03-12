<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const products = ref([]);

// Ambil data dari API Laravel
const fetchProducts = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/products');
    products.ref = response.data;
  } catch (error) {
    console.error("Gagal memuat produk", error);
  }
};

const formatPrice = (value) => {
  return new Intl.NumberFormat('id-ID').format(value);
};

const addToCart = (product) => {
  // Logika tambah ke Pinia Store / LocalStorage
  alert(`${product.name} ditambahkan ke keranjang!`);
};

onMounted(fetchProducts);
</script>


<template>
  <div class="bg-gray-50 min-h-screen p-4 md:p-8">
    <div class="max-w-7xl mx-auto">
      <header class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-slate-800">Katalog Material</h1>
        <div class="relative">
          <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
          <input type="text" placeholder="Cari semen, cat, pipa..." 
                 class="pl-10 pr-4 py-2 border rounded-full w-64 focus:outline-none focus:ring-2 focus:ring-orange-500" />
        </div>
      </header>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="product in products" :key="product.id" 
             class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-100">
          
          <div class="h-48 bg-gray-200 flex items-center justify-center relative">
            <img v-if="product.image" :src="product.image" class="object-cover w-full h-full" />
            <span v-else class="text-gray-400 text-xs">Tanpa Gambar</span>
            
            <span :class="product.stock > 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                  class="absolute top-2 right-2 px-2 py-1 rounded-md text-[10px] font-bold uppercase">
              Stok: {{ product.stock }} {{ product.unit }}
            </span>
          </div>

          <div class="p-4">
            <p class="text-xs text-orange-600 font-semibold uppercase tracking-wider">{{ product.category.name }}</p>
            <h2 class="text-lg font-bold text-gray-800 truncate">{{ product.name }}</h2>
            <p class="text-xl font-black text-gray-900 mt-2">Rp {{ formatPrice(product.price) }}</p>
            
            <button @click="addToCart(product)"
                    class="w-full mt-4 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
              <span>🛒</span> Tambah
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
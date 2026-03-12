<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'; // Tambahkan computed
import axios from 'axios';
import { Product } from '@/types/product';
import { router } from '@inertiajs/vue3';

const products = ref<Product[]>([]);
const loading = ref(true);
const searchQuery = ref(''); // State untuk teks pencarian

// 1. Fungsi Mengambil Data
const fetchProducts = async () => {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/products');
        products.value = response.data.data;
    } catch (error) {
        console.error("Gagal mengambil data:", error);
    } finally {
        loading.value = false;
    }
};

// 2. Fungsi Format Harga ke Rupiah
const formatCurrency = (value: string | number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(Number(value));
};

// 3. Logika Pencarian (Computed)
// Ini akan memfilter produk secara otomatis saat searchQuery berubah
const filteredProducts = computed(() => {
    return products.value.filter(product => {
        return product.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
               product.description?.toLowerCase().includes(searchQuery.value.toLowerCase());
    });
});

// 4. Fungsi Hapus
const deleteProduct = async (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        try {
            await axios.delete(`/api/products/${id}`);
            products.value = products.value.filter(p => p.id !== id);
            alert('Produk berhasil dihapus');
        } catch (error) {
            alert('Gagal menghapus produk');
        }
    }
};

onMounted(() => {
    fetchProducts();
});
</script>

<template>
    <div class="p-8 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Inventory Produk</h1>
                <button @click="router.visit('/products/create')" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    + Tambah Produk
                </button>
            </div>

            <div v-if="loading" class="text-center py-10 text-gray-500">
                Memuat data produk...
            </div>

            <table v-else-if="products.length > 0" class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-gray-100">
                        <th class="p-3">Nama Produk</th>
                        <th class="p-3">Harga</th>
                        <th class="p-3 text-center">Stok</th>
                        <th class="p-3 text-center">Aksi</th>
                        <th class="p-3 text-center">Gambar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products" :key="product.id" class="border-b hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ product.name }}</td>
                        <td class="p-3 text-green-600 font-semibold">Rp{{ product.price.toLocaleString() }}</td>
                        <td class="p-3 text-center">{{ product.stock }}</td>
                        <td class="p-3">
                            <img 
                                v-if="product.image" 
                                :src="product.image" 
                                alt="product" 
                                class="w-12 h-12 object-cover rounded shadow-sm"
                            >
                            <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">
                                No Img
                            </div>
                        </td>
                        <td class="p-3 text-center">
                            <button @click="router.visit(`/products/${product.id}/edit`)" class="text-blue-500 hover:underline mr-2">Edit</button>
                            <button @click="deleteProduct(product.id)" class="text-red-500 hover:underline">Hapus</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-else class="text-center py-10 text-gray-500">
                Belum ada produk yang tersedia.
            </div>
        </div>
    </div>
</template>
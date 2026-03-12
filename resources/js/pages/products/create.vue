<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, ref } from 'vue';



const form = reactive({
    name: '',
    price: 0,
    stock: 0,
    description: ''
});

// State untuk menyimpan file gambar
const imageFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);

const errors = ref<any>({});
const processing = ref(false);

// Fungsi menangani pilih file
const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        imageFile.value = file;
        
        // Membuat preview gambar sederhana
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = async () => {
    processing.value = true;
    errors.value = {}; // Reset error

    // Gunakan FormData karena ada upload file
    const data = new FormData();
    data.append('name', form.name);
    data.append('price', form.price.toString());
    data.append('stock', form.stock.toString());
    data.append('description', form.description);
    
    if (imageFile.value) {
        data.append('image', imageFile.value);
    }

    try {
        await axios.post('/api/products', data, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        // Jika berhasil, balik ke index
        router.visit('/products');
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            alert('Terjadi kesalahan sistem.');
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-xl mt-10 border-t-4 border-indigo-500">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Tambah Produk Baru</h2>
        
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                <input v-model="form.name" type="text" class="w-full border p-2 rounded-md focus:ring-2 focus:ring-blue-500 outline-none" :class="{'border-red-500': errors.name}">
                <span v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input v-model.number="form.price" type="number" class="w-full border p-2 rounded-md focus:ring-2 focus:ring-blue-500 outline-none">
                    <span v-if="errors.price" class="text-red-500 text-xs mt-1">{{ errors.price[0] }}</span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                    <input v-model.number="form.stock" type="number" class="w-full border p-2 rounded-md focus:ring-2 focus:ring-blue-500 outline-none">
                    <span v-if="errors.stock" class="text-red-500 text-xs mt-1">{{ errors.stock[0] }}</span>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea v-model="form.description" rows="3" class="w-full border p-2 rounded-md focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
                <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                
                <div v-if="imagePreview" class="mt-3">
                    <img :src="imagePreview" class="h-32 w-32 object-cover rounded-lg border shadow-sm">
                </div>
                <span v-if="errors.image" class="text-red-500 text-xs mt-1">{{ errors.image[0] }}</span>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" @click="router.visit('/products')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                <button type="submit" :disabled="processing" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow-md disabled:bg-blue-300 transition-all">
                    {{ processing ? 'Menyimpan...' : 'Simpan Produk' }}
                </button>
            </div>
        </form>
    </div>
</template>
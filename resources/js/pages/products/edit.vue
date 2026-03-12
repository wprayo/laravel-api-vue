<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, ref, } from 'vue';


// Menerima data produk dari Laravel sebagai props
const props = defineProps<{
    product: any
}>();

const form = reactive({
    name: props.product.name,
    price: props.product.price,
    stock: props.product.stock,
    description: props.product.description || '',
    // image: props.product.image
});

const imageFile = ref<File | null>(null);
const imagePreview = ref<string | null>(props.product.image); // Gunakan foto lama sebagai default preview
const errors = ref<any>({});
const processing = ref(false);

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        imageFile.value = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = async () => {
    processing.value = true;
    errors.value = {};

    const data = new FormData();
    data.append('name', form.name);
    data.append('price', form.price.toString());
    data.append('stock', form.stock.toString());
    data.append('description', form.description);
    
    // Kirim gambar hanya jika user memilih file baru
    if (imageFile.value) {
        data.append('image', imageFile.value);
    }

    // METHOD SPOOFING: Sangat penting untuk Update dengan File di Laravel
    data.append('_method', 'PUT');

    try {
        // Kita kirim via POST karena FormData + PUT murni sering bermasalah di PHP
        await axios.post(`/api/products/${props.product.id}`, data, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        router.visit('/products');
    } catch (error: any) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        processing.value = false;
    }
};
</script>

<template>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-xl mt-10 border-t-4 border-indigo-500">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Produk: {{ props.product.name }}</h2>
        
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Produk</label>
                <input v-model="form.name" type="text" class="w-full border p-2 rounded-md" :class="{'border-red-500': errors.name}">
                <span v-if="errors.name" class="text-red-500 text-xs">{{ errors.name[0] }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Harga</label>
                    <input v-model.number="form.price" type="number" class="w-full border p-2 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Stok</label>
                    <input v-model.number="form.stock" type="number" class="w-full border p-2 rounded-md">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Foto Produk (Kosongkan jika tidak diubah)</label>
                <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-sm">
                
                <div v-if="imagePreview" class="mt-3 relative w-32 h-32">
                    <img :src="imagePreview" class="h-full w-full object-cover rounded-lg border shadow-sm">
                    <span class="absolute -top-2 -right-2 bg-indigo-500 text-white text-[10px] px-2 py-1 rounded">Preview</span>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" @click="router.visit('/products')" class="px-4 py-2 text-gray-500">Batal</button>
                <button type="submit" :disabled="processing" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold">
                    {{ processing ? 'Menyimpan...' : 'Update Produk' }}
                </button>
            </div>
        </form>
    </div>
</template>
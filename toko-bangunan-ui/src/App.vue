<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const products = ref([])
const loading = ref(true)

const loadData = async () => {
  try {
    // Memanggil API dari terminal sebelah (Laravel)
    const response = await axios.get('http://127.0.0.1:8000/api/products')
    products.value = response.data
  } catch (e) {
    console.error("Gagal konek ke Laravel. Pastikan 'php artisan serve' jalan.")
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>

<template>
  <div style="padding: 20px; font-family: sans-serif;">
    <h1 style="color: #e67e22;">🏗️ Katalog Toko Bangunan</h1>
    <hr>
    
    <div v-if="loading">Sedang mengambil data material...</div>
    
    <div v-else-if="products.length === 0">
      <p>Data kosong. Coba isi database Laravel kamu dulu.</p>
    </div>

    <div v-else style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 20px;">
      <div v-for="item in products" :key="item.id" style="border: 1px solid #ddd; padding: 15px; border-radius: 8px;">
        <h3 style="margin: 0;">{{ item.name }}</h3>
        <p style="font-weight: bold; color: green;">Rp {{ item.price.toLocaleString() }}</p>
        <small>Stok: {{ item.stock }} {{ item.unit }}</small>
      </div>
    </div>
  </div>
</template>
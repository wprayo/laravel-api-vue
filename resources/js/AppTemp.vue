<template>
  <div>
    <h1>Laravel + Vue Web Server</h1>

    <button @click="getProducts">
      Load Products
    </button>

    <ul>
      <li v-for="p in products" :key="p.id">
        {{ p.name }} - Rp {{ p.price }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

interface Product {
  id: number
  name: string
  price: number
}

const products = ref<Product[]>([])

const getProducts = async () => {
  const res = await axios.get('/api/products')
  products.value = res.data
}
</script>

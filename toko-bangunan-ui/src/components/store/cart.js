// store/cart.js (Pinia) atau di Component
import axios from 'axios';

const checkout = async () => {
  const payload = {
    shipping_address: "Jl. Raya Semen No. 10, Jakarta",
    payment_method: "Transfer Bank",
    items: cart.items.map(item => ({
      id: item.id,
      quantity: item.qty
    }))
  };

  try {
    const response = await axios.post('/api/orders', payload);
    alert('Sukses! Nomor Invoice: ' + response.data.order.order_number);
    // Redirect ke halaman sukses atau bayar
  } catch (error) {
    alert(error.response.data.message);
  }
};
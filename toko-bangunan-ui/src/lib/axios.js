import axios from 'axios';

const instance = axios.create({
    baseURL: 'http://localhost:8000/api', // URL Laravel Anda
});

export default instance;
export interface Product {
    id: number;
    name: string;
    description: string | null;
    price: string | number; // API Anda mengirim price sebagai string "222.00"
    stock: number;
    image: string | null;
    created_at: string;
    updated_at: string;
}
export interface User {
    id: number;
    name: string;
    permissions: string[]; // Contoh: 'create_user', 'view_any_post'
    roles: string[];
}

export interface AuthResponse {
    token: string;
    user: User;
}
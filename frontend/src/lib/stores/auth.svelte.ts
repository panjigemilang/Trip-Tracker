import { api } from '$lib/services/api/client';
import type { LoginCredentials, RegisterCredentials, User } from '$lib/types/auth';

export class AuthStore {
	user = $state<User | null>(null);
	isAuthenticated = $derived(this.user !== null);
	loading = $state(true);

	constructor() {
		this.init();
	}

	async init() {
		if (typeof window === 'undefined') return;
		
		const token = localStorage.getItem('auth_token');
		if (token) {
			try {
				const response = await api.request<{ data: User }>('/auth/user');
				this.user = response.data;
			} catch (error) {
				localStorage.removeItem('auth_token');
			}
		}
		this.loading = false;
	}

	async login(credentials: LoginCredentials): Promise<void> {
		const response = await api.request<{ data: User; token: string }>('/auth/login', {
			method: 'POST',
			body: JSON.stringify(credentials)
		});
		
		localStorage.setItem('auth_token', response.token);
		this.user = response.data;
	}

	async register(credentials: RegisterCredentials): Promise<void> {
		const response = await api.request<{ data: User; token: string }>('/auth/register', {
			method: 'POST',
			body: JSON.stringify(credentials)
		});
		
		localStorage.setItem('auth_token', response.token);
		this.user = response.data;
	}

	async logout(): Promise<void> {
		try {
			await api.request('/auth/logout', { method: 'POST' });
		} catch (e) {
			// Ignore if token is already invalid
		} finally {
			localStorage.removeItem('auth_token');
			this.user = null;
		}
	}
}

export const authStore = new AuthStore();

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
				this.loading = false;
				return;
			} catch (error) {
				localStorage.removeItem('auth_token');
			}
		}

		// Fallback for Guest session: silent auto-login if token expired or backend database was reset
		const guestCredsStr = localStorage.getItem('guest_credentials');
		if (guestCredsStr) {
			try {
				const creds = JSON.parse(guestCredsStr);
				await this.login(creds);
			} catch (error) {
				localStorage.removeItem('guest_credentials');
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

	async loginAsGuest(): Promise<void> {
		const rand = Math.random().toString(36).substring(2, 10);
		const guestCredentials = {
			name: `Guest Agent ${rand}`,
			email: `guest_${rand}@sector7.local`,
			password: `guest_${rand}_pass`,
			password_confirmation: `guest_${rand}_pass`
		};
		await this.register(guestCredentials);
		localStorage.setItem('guest_credentials', JSON.stringify({
			email: guestCredentials.email,
			password: guestCredentials.password
		}));
	}

	async logout(): Promise<void> {
		try {
			await api.request('/auth/logout', { method: 'POST' });
		} catch (e) {
			// Ignore if token is already invalid
		} finally {
			localStorage.removeItem('auth_token');
			localStorage.removeItem('guest_credentials');
			this.user = null;
		}
	}
}

export const authStore = new AuthStore();

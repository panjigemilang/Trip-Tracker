export class ApiClient {
	private baseUrl = 'http://localhost:8000/api/v1';

	async request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
		const token = typeof window !== 'undefined' ? localStorage.getItem('auth_token') : null;

		const headers: HeadersInit = {
			'Content-Type': 'application/json',
			'Accept': 'application/json',
			...options.headers,
		};

		if (token) {
			headers['Authorization'] = `Bearer ${token}`;
		}

		const response = await fetch(`${this.baseUrl}${endpoint}`, {
			...options,
			headers,
		});

		if (response.status === 401) {
			if (typeof window !== 'undefined') {
				localStorage.removeItem('auth_token');
				window.location.href = '/auth/login';
			}
			throw new Error('Unauthorized');
		}

		const data = await response.json();

		if (!response.ok) {
			throw new Error(data.message || `API Error: ${response.status}`);
		}

		return data;
	}
}

export const api = new ApiClient();

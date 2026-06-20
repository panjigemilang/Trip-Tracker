export class ApiClient {
	private baseUrl = 'http://localhost:8000/api/v1';

	async request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
		const token = typeof window !== 'undefined' ? localStorage.getItem('auth_token') : null;

		const headers: Record<string, string> = {
			'Content-Type': 'application/json',
			'Accept': 'application/json',
		};

		if (options.headers) {
			const extraHeaders = options.headers as Record<string, string>;
			for (const key in extraHeaders) {
				headers[key] = extraHeaders[key];
			}
		}

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
				window.location.href = '/login';
			}
			throw new Error('Unauthorized');
		}

		const data = await response.json();

		if (!response.ok) {
			throw new Error(data.message || `API Error: ${response.status}`);
		}

		return data;
	}

	async get<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
		return this.request<T>(endpoint, { ...options, method: 'GET' });
	}

	async post<T>(endpoint: string, body?: unknown, options: RequestInit = {}): Promise<T> {
		return this.request<T>(endpoint, {
			...options,
			method: 'POST',
			body: body ? JSON.stringify(body) : undefined,
		});
	}

	async put<T>(endpoint: string, body?: unknown, options: RequestInit = {}): Promise<T> {
		return this.request<T>(endpoint, {
			...options,
			method: 'PUT',
			body: body ? JSON.stringify(body) : undefined,
		});
	}

	async patch<T>(endpoint: string, body?: unknown, options: RequestInit = {}): Promise<T> {
		return this.request<T>(endpoint, {
			...options,
			method: 'PATCH',
			body: body ? JSON.stringify(body) : undefined,
		});
	}

	async delete<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
		return this.request<T>(endpoint, { ...options, method: 'DELETE' });
	}
}

export const api = new ApiClient();
export const apiClient = api;



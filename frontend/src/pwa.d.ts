declare module 'virtual:pwa-register/svelte' {
	export interface RegisterSWOptions {
		immediate?: boolean;
		onRegisterError?: (error: any) => void;
		onRegistered?: (registration: any) => void;
		onRegisteredSW?: (swScriptUrl: string, registration: any) => void;
		onNeedRefresh?: () => void;
		onOfflineReady?: () => void;
	}

	export function useRegisterSW(options?: RegisterSWOptions): {
		needRefresh: import('svelte/store').Writable<boolean>;
		offlineReady: import('svelte/store').Writable<boolean>;
		updateServiceWorker: (reloadPage?: boolean) => Promise<void>;
	};
}

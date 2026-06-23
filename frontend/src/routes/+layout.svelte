<script lang="ts">
	import './layout.css';
	import favicon from '$lib/assets/favicon.svg';
	import { Toaster } from 'svelte-sonner';
	import OfflineIndicator from '$lib/components/atoms/OfflineIndicator.svelte';
	import ReloadPrompt from '$lib/components/molecules/ReloadPrompt.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { page } from '$app/stores';
	import { goto, onNavigate } from '$app/navigation';

	onNavigate((navigation) => {
		if (!document.startViewTransition) return;

		return new Promise((resolve) => {
			document.startViewTransition(async () => {
				resolve();
				await navigation.complete;
			});
		});
	});

	let { children } = $props();

	$effect(() => {
		if (!authStore.loading) {
			const path = $page.url.pathname;
			const isAuth = !!authStore.user;
			const isAuthRoute = path.startsWith('/login') || path.startsWith('/register');
			
			if (isAuth && isAuthRoute) {
				goto('/trips');
			} else if (!isAuth && !isAuthRoute && path !== '/') {
				goto('/login');
			}
		}
	});
</script>

<svelte:head><link rel="icon" href={favicon} /></svelte:head>
<Toaster />
<OfflineIndicator />
<ReloadPrompt />
{#if !authStore.loading}
  {@render children()}
{:else}
  <div class="flex h-screen w-screen items-center justify-center bg-[#0B0C10]">
    <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary"></div>
  </div>
{/if}

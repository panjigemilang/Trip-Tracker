<script lang="ts">
	import { page } from '$app/stores';
	import { authStore } from '$lib/stores/auth.svelte';
	import { goto } from '$app/navigation';

	let { children } = $props();

	$effect(() => {
		if (!authStore.loading && authStore.isAuthenticated) {
			goto('/trips');
		}
	});
</script>

{#if authStore.loading}
	<div class="min-h-screen bg-background flex items-center justify-center">
		<div class="text-secondary tracking-widest text-xs uppercase animate-pulse">Checking credentials...</div>
	</div>
{:else if !authStore.isAuthenticated}
	{#if $page.url.pathname === '/login'}
		{@render children()}
	{:else}
		<div class="flex min-h-screen items-center justify-center bg-zinc-50 dark:bg-zinc-950 p-4">
			<div class="w-full max-w-md">
				{@render children()}
			</div>
		</div>
	{/if}
{/if}

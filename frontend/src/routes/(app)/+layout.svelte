<script lang="ts">
	import TopNav from '$lib/components/layout/desktop/TopNav.svelte';
	import SideNav from '$lib/components/layout/desktop/SideNav.svelte';
	import BottomNav from '$lib/components/layout/mobile/BottomNav.svelte';
	import FAB from '$lib/components/layout/mobile/FAB.svelte';
	import MobileNotificationOverlay from '$lib/components/layout/mobile/MobileNotificationOverlay.svelte';
	import { authStore } from '$lib/stores/auth.svelte';
	import { notificationsStore } from '$lib/stores/notifications.svelte';
	import { goto } from '$app/navigation';
	import { onMount } from 'svelte';

	let { children } = $props();

	$effect(() => {
		if (!authStore.loading && !authStore.isAuthenticated) {
			goto('/login');
		}
	});

	onMount(() => {
		notificationsStore.init();
	});
</script>

{#if authStore.isAuthenticated}
	<div class="relative min-h-screen bg-background text-foreground flex flex-col overflow-x-hidden">
		<TopNav />
		
		<div class="flex flex-1">
			<SideNav />
			
			<!-- Main content area -->
			<main class="flex-1 pb-24 md:pb-0 md:ml-64 px-4 py-6 md:px-8 w-full max-w-[100vw]">
				{@render children()}
			</main>
		</div>

		<BottomNav />
		<FAB />
		<MobileNotificationOverlay />
	</div>
{:else}
	<div class="min-h-screen bg-background flex items-center justify-center">
		<div class="text-secondary tracking-widest text-xs uppercase animate-pulse">Initializing Secure Protocol...</div>
	</div>
{/if}

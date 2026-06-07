<script lang="ts">
	import './layout.css';
	import favicon from '$lib/assets/favicon.svg';
	import { Toaster } from 'svelte-sonner';
	import OfflineIndicator from '$lib/components/atoms/OfflineIndicator.svelte';
	import ReloadPrompt from '$lib/components/molecules/ReloadPrompt.svelte';

	let { children } = $props();
</script>

<svelte:head><link rel="icon" href={favicon} /></svelte:head>
<Toaster />
<OfflineIndicator />
<ReloadPrompt />
{@render children()}

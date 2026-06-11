<script lang="ts">
	import TopNav from '$lib/components/layout/desktop/TopNav.svelte';
	import SideNav from '$lib/components/layout/desktop/SideNav.svelte';
	import BottomNav from '$lib/components/layout/mobile/BottomNav.svelte';
	import FAB from '$lib/components/layout/mobile/FAB.svelte';

	let { children } = $props();
</script>

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
</div>

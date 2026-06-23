<script lang="ts">
  import { Plus } from 'lucide-svelte';
  import { page } from '$app/stores';

  // Hide FAB on trip details page to prevent confusion
  let isTripDetailPage = $derived($page.url.pathname.match(/^\/trips\/[^\/]+\/[^\/]+$/));
</script>

{#if !isTripDetailPage}
<a href="/trips/create" class="md:hidden fixed bottom-20 right-4 z-50 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-[0_0_15px_rgba(255,42,122,0.6)] transition-all duration-200 hover:scale-105 active:scale-90">
  <Plus class="h-6 w-6" />
</a>
{/if}

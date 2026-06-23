<script lang="ts">
  import { ArrowLeft, AlertCircle } from 'lucide-svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { fade, fly } from 'svelte/transition';
  import { cubicOut } from 'svelte/easing';
  import type { Snippet } from 'svelte';

  let { 
    isFormOpen = $bindable(), 
    error,
    children
  } = $props<{
    isFormOpen: boolean,
    error: string,
    children: Snippet
  }>();
</script>

{#if isFormOpen}
  <div class="md:hidden fixed inset-0 z-100 w-full h-dvh">
    <!-- Backdrop -->
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div 
      class="absolute inset-0 bg-[#0B0C10]/80 backdrop-blur-sm" 
      transition:fade={{ duration: 200 }} 
      onclick={() => { isFormOpen = false; }}
    ></div>
    
    <!-- Drawer Content -->
    <div 
      class="absolute inset-x-0 bottom-0 top-12 bg-[#06080E] flex flex-col overflow-y-auto rounded-t-2xl shadow-[0_-10px_40px_rgba(255,42,122,0.15)] border-t border-border/50"
      transition:fly={{ y: '100%', duration: 300, easing: cubicOut }}
    >
      <header class="flex items-center justify-between p-4 border-b border-border/20 sticky top-0 bg-[#06080E]/90 backdrop-blur z-10 rounded-t-2xl">
        <button onclick={() => { isFormOpen = false; }} class="text-muted-foreground hover:text-white transition-colors p-2 -ml-2">
          <ArrowLeft class="h-5 w-5" />
        </button>
        <NeonText class="text-sm font-bold tracking-widest uppercase text-secondary">NEW SEQUENCE</NeonText>
        <div class="w-9 h-9"></div>
      </header>

      <div class="flex-1 p-6 flex flex-col gap-6">
        <div>
          <h1 class="text-3xl font-bold tracking-widest text-white font-heading uppercase">Add Segment</h1>
          <p class="text-xs text-muted-foreground mt-2">Input telemetry data for your next journey.</p>
        </div>
        
        {#if error}
          <div class="p-3 border border-red-500/30 bg-red-500/5 text-red-500 text-xs rounded-lg flex items-start gap-2">
            <AlertCircle class="h-4 w-4 shrink-0 mt-0.5" />
            <span>{error}</span>
          </div>
        {/if}
        
        {@render children()}
      </div>
    </div>
  </div>
{/if}

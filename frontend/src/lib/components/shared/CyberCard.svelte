<script lang="ts">
  import type { Snippet } from 'svelte';
  
  let { 
    children, 
    class: className = '', 
    glowState = 'none', 
    ...rest 
  }: { 
    children: Snippet, 
    class?: string, 
    glowState?: 'none' | 'primary' | 'secondary',
    [key: string]: any
  } = $props();
</script>

<div 
  class="rounded-xl border border-border bg-card p-4 transition-all duration-300 {className}"
  class:shadow-[0_0_15px_rgba(255,42,122,0.3)]={glowState === 'primary'}
  class:shadow-[0_0_15px_rgba(0,230,184,0.3)]={glowState === 'secondary'}
  {...rest}
>
  {@render children()}
</div>

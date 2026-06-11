<script lang="ts">
  import type { Snippet } from 'svelte';

  let { 
    children, 
    class: className = '', 
    color = 'primary' 
  }: {
    children: Snippet,
    class?: string,
    color?: 'primary' | 'secondary'
  } = $props();
</script>

<span 
  class="font-heading font-bold {className}"
  class:text-primary={color === 'primary'}
  class:text-secondary={color === 'secondary'}
  style="text-shadow: 0 0 8px {color === 'primary' ? 'rgba(255,42,122,0.6)' : 'rgba(0,230,184,0.6)'};"
>
  {@render children()}
</span>

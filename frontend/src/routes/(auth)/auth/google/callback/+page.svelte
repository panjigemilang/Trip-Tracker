<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { page } from '$app/stores';
  import { authStore } from '$lib/stores/auth.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';

  let statusMessage = $state('Verifying security clearance...');
  let error = $state(false);

  onMount(async () => {
    const token = $page.url.searchParams.get('token');
    if (!token) {
      statusMessage = 'Access Denied: Security token missing.';
      error = true;
      setTimeout(() => goto('/login?error=Token missing'), 2000);
      return;
    }

    try {
      statusMessage = 'Decrypting credentials...';
      await authStore.handleGoogleCallback(token);
      statusMessage = 'Identity verified. Redirecting...';
      setTimeout(() => goto('/trips'), 1000);
    } catch (err: any) {
      statusMessage = 'Authentication failed: Invalid record.';
      error = true;
      setTimeout(() => goto('/login?error=Google authentication failed'), 2000);
    }
  });
</script>

<div class="min-h-screen w-full flex flex-col items-center justify-center p-4 bg-background relative overflow-hidden font-sans">
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,42,122,0.15),transparent_50%)]"></div>
  
  <div class="z-10 flex flex-col items-center gap-6 text-center">
    <div class="relative flex items-center justify-center">
      <div class="w-16 h-16 border-2 border-primary/20 border-t-primary rounded-full animate-spin"></div>
      <div class="absolute w-12 h-12 border-2 border-secondary/20 border-b-secondary rounded-full animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
    </div>

    <div class="flex flex-col gap-2">
      <NeonText class="text-2xl tracking-widest uppercase font-bold">AUTHORIZING</NeonText>
      <p class="text-sm font-mono text-muted-foreground tracking-wide min-h-6 animate-pulse {error ? 'text-red-500' : ''}">
        {statusMessage}
      </p>
    </div>
  </div>
</div>

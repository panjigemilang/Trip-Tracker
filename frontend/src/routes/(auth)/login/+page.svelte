<script lang="ts">
  import { Button } from '$lib/components/ui/button';
  import { Input } from '$lib/components/ui/input';
  import { Label } from '$lib/components/ui/label';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { User, Lock, AlertTriangle, AlertCircle } from 'lucide-svelte';
  import { authStore } from '$lib/stores/auth.svelte';
  import { goto } from '$app/navigation';

  let email = $state('');
  let password = $state('');
  let error = $state('');
  let loading = $state(false);
  let guestLoading = $state(false);

  async function handleSubmit(e: Event) {
    e.preventDefault();
    if (!email || !password) return;
    loading = true;
    error = '';
    try {
      await authStore.login({ email, password });
      goto('/trips');
    } catch (err: any) {
      error = err.message || 'Access Denied: Invalid credentials.';
    } finally {
      loading = false;
    }
  }

  async function handleGuestLogin() {
    guestLoading = true;
    error = '';
    try {
      await authStore.loginAsGuest();
      goto('/trips');
    } catch (err: any) {
      error = err.message || 'Failed to initialize guest session.';
    } finally {
      guestLoading = false;
    }
  }
</script>

<div class="min-h-screen w-full flex flex-col items-center justify-center p-4 relative overflow-hidden bg-[url('/bg-city.jpg')] bg-cover bg-center bg-no-repeat">
  <!-- Overlay to darken background -->
  <div class="absolute inset-0 bg-background/80 backdrop-blur-sm z-0"></div>

  <div class="z-10 w-full max-w-105 flex flex-col items-center gap-8">
    <div class="flex flex-col items-center gap-2">
      <div class="flex items-center gap-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary/20 text-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
        </div>
        <NeonText class="text-3xl tracking-widest">TRIP_TRACKER</NeonText>
      </div>
      <div class="text-[10px] text-secondary tracking-widest uppercase font-semibold">Track Your Awesome Trip!</div>
    </div>

    <CyberCard class="w-full relative overflow-hidden p-6 md:p-8 border-t border-t-primary/50 shadow-[0_4px_30px_rgba(0,0,0,0.5)]">
      <div class="mb-6">
        <h2 class="text-xl font-bold mb-1">System Access</h2>
        <p class="text-sm text-muted-foreground">Provide credentials for secure session initialization.</p>
      </div>

      {#if error}
        <div class="mb-4 p-3 border border-red-500/30 bg-red-500/5 text-red-500 text-xs rounded-lg flex items-start gap-2 animate-in fade-in duration-200">
          <AlertCircle class="h-4 w-4 shrink-0 mt-0.5" />
          <span>{error}</span>
        </div>
      {/if}

      <form onsubmit={handleSubmit} class="space-y-4 mb-4">
        <div class="space-y-2">
          <Label for="email" class="text-[10px] uppercase tracking-widest text-muted-foreground">Agent Identity (Email)</Label>
          <div class="relative">
            <User class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
            <Input id="email" type="email" placeholder="agent_name@sector.7" class="pl-10" bind:value={email} required />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="password" class="text-[10px] uppercase tracking-widest text-muted-foreground">Access Cipher (Password)</Label>
          <div class="relative">
            <Lock class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
            <Input id="password" type="password" placeholder="••••••••••••" class="pl-10" bind:value={password} required />
          </div>
        </div>

        <Button type="submit" disabled={loading} class="w-full bg-primary text-primary-foreground hover:bg-primary/80 shadow-[0_0_15px_rgba(255,42,122,0.4)]">
          {loading ? 'INITIALIZING SESSION...' : 'INITIALIZE SESSION'}
        </Button>
      </form>

      <Button class="w-full mb-4 bg-[#2A0818] border border-primary text-primary hover:bg-primary hover:text-primary-foreground shadow-none hover:shadow-[0_0_15px_rgba(255,42,122,0.6)]">
        LOGIN GOOGLE
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
      </Button>

      <div class="relative my-6 text-center">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-border"></div>
        </div>
        <span class="relative bg-card px-2 text-[10px] uppercase tracking-widest text-muted-foreground">OR</span>
      </div>

      <Button variant="outline" onclick={handleGuestLogin} disabled={guestLoading} class="w-full text-secondary border-secondary/50 hover:bg-secondary/10 hover:text-secondary shadow-none">
        {guestLoading ? 'INITIALIZING GUEST...' : 'CONTINUE AS GUEST'}
      </Button>

      <div class="mt-6 text-center text-xs text-muted-foreground">
        Need a new agent record? <a href="/register" class="text-primary hover:underline">Register account</a>
      </div>
    </CyberCard>

    <div class="w-full border border-yellow-500/30 bg-yellow-500/5 p-4 rounded-lg flex items-start gap-3">
      <AlertTriangle class="h-5 w-5 text-yellow-500 mt-0.5 shrink-0" />
      <div>
        <h4 class="text-xs font-bold text-yellow-500 uppercase tracking-wide mb-1">WARNING: Guest trips are</h4>
        <p class="text-xs text-muted-foreground">automatically purged after 30 days. Log in to preserve your journeys forever.</p>
      </div>
    </div>
  </div>
</div>

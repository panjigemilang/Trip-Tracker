<script lang="ts">
  import { Button } from '$lib/components/ui/button';
  import { Input } from '$lib/components/ui/input';
  import { Label } from '$lib/components/ui/label';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { User, Lock, AlertTriangle, AlertCircle, Sparkles } from 'lucide-svelte';
  import { authStore } from '$lib/stores/auth.svelte';
  import { goto } from '$app/navigation';

  let name = $state('');
  let email = $state('');
  let password = $state('');
  let password_confirmation = $state('');
  let error = $state('');
  let loading = $state(false);

  async function handleSubmit(e: Event) {
    e.preventDefault();
    if (password !== password_confirmation) {
      error = 'Cipher verification failed: Passwords do not match.';
      return;
    }

    loading = true;
    error = '';

    try {
      await authStore.register({ name, email, password, password_confirmation });
      goto('/trips');
    } catch (err: any) {
      error = err.message || 'Registration failed.';
    } finally {
      loading = false;
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
        <h2 class="text-xl font-bold mb-1">Create Agent Record</h2>
        <p class="text-sm text-muted-foreground">Register credentials to initialize a new persistent session.</p>
      </div>

      {#if error}
        <div class="mb-4 p-3 border border-red-500/30 bg-red-500/5 text-red-500 text-xs rounded-lg flex items-start gap-2 animate-in fade-in duration-200">
          <AlertCircle class="h-4 w-4 shrink-0 mt-0.5" />
          <span>{error}</span>
        </div>
      {/if}

      <form onsubmit={handleSubmit} class="space-y-4">
        <div class="space-y-2">
          <Label for="name" class="text-[10px] uppercase tracking-widest text-muted-foreground">Agent Codename (Name)</Label>
          <div class="relative">
            <Sparkles class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
            <Input id="name" type="text" placeholder="agent_zero" class="pl-10" bind:value={name} required />
          </div>
        </div>

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
            <Input id="password" type="password" placeholder="••••••••••••" class="pl-10" bind:value={password} required minlength={8} />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="password_confirmation" class="text-[10px] uppercase tracking-widest text-muted-foreground">Verify Cipher (Confirm Password)</Label>
          <div class="relative">
            <Lock class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
            <Input id="password_confirmation" type="password" placeholder="••••••••••••" class="pl-10" bind:value={password_confirmation} required minlength={8} />
          </div>
        </div>

        <Button type="submit" disabled={loading} class="w-full bg-primary text-primary-foreground hover:bg-primary/80 shadow-[0_0_15px_rgba(255,42,122,0.4)]">
          {loading ? 'CREATING RECORD...' : 'CREATE RECORD'}
        </Button>
      </form>

      <div class="mt-6 text-center text-xs text-muted-foreground">
        Already have a record? <a href="/login" class="text-primary hover:underline">Sign in</a>
      </div>
    </CyberCard>
  </div>
</div>

<script lang="ts">
  import { Button } from '$lib/components/ui/button';
  import { Input } from '$lib/components/ui/input';
  import { Label } from '$lib/components/ui/label';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { User, Lock, AlertTriangle } from 'lucide-svelte';
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
      <div class="text-[10px] text-secondary tracking-widest uppercase font-semibold">Track Your Awesome Amazingu Trip!</div>
    </div>

    <CyberCard class="w-full relative overflow-hidden p-6 md:p-8 border-t border-t-primary/50 shadow-[0_4px_30px_rgba(0,0,0,0.5)]">
      <div class="mb-6">
        <h2 class="text-xl font-bold mb-1">System Access</h2>
        <p class="text-sm text-muted-foreground">Provide credentials for secure session initialization.</p>
      </div>

      <div class="space-y-4 mb-6">
        <div class="space-y-2">
          <Label for="email" class="text-[10px] uppercase tracking-widest text-muted-foreground">Agent Identity (Email)</Label>
          <div class="relative">
            <User class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
            <Input id="email" type="email" placeholder="agent_name@sector.7" class="pl-10" />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="password" class="text-[10px] uppercase tracking-widest text-muted-foreground">Access Cipher (Password)</Label>
          <div class="relative">
            <Lock class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
            <Input id="password" type="password" placeholder="••••••••••••" class="pl-10" />
          </div>
          <div class="flex justify-end">
            <button class="text-xs text-secondary hover:text-secondary/80 transition-colors">Override Cipher?</button>
          </div>
        </div>
      </div>

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

      <Button variant="outline" class="w-full text-secondary border-secondary/50 hover:bg-secondary/10 hover:text-secondary shadow-none">
        CONTINUE AS GUEST
      </Button>
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

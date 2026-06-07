<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { Input } from '$lib/components/ui/input';
	import { Label } from '$lib/components/ui/label';
	import { Button } from '$lib/components/ui/button';
	import * as Card from '$lib/components/ui/card';
	import { goto } from '$app/navigation';

	let email = $state('');
	let password = $state('');
	let error = $state('');
	let loading = $state(false);

	async function handleSubmit(e: Event) {
		e.preventDefault();
		loading = true;
		error = '';

		try {
			await authStore.login({ email, password });
			goto('/');
		} catch (err: any) {
			error = err.message || 'Login failed';
		} finally {
			loading = false;
		}
	}
</script>

<Card.Root>
	<Card.Header class="space-y-1">
		<Card.Title class="text-2xl text-center">Login</Card.Title>
		<Card.Description class="text-center">
			Enter your email to sign in to your account
		</Card.Description>
	</Card.Header>
	<Card.Content>
		{#if error}
			<div class="p-3 text-sm text-red-500 bg-red-50 dark:bg-red-950/50 rounded-md mb-4 border border-red-200 dark:border-red-900">
				{error}
			</div>
		{/if}
		<form onsubmit={handleSubmit} class="space-y-4">
			<div class="space-y-2">
				<Label for="email">Email</Label>
				<Input id="email" type="email" bind:value={email} placeholder="m@example.com" required />
			</div>
			<div class="space-y-2">
				<Label for="password">Password</Label>
				<Input id="password" type="password" bind:value={password} required />
			</div>
			<Button class="w-full" type="submit" disabled={loading}>
				{loading ? 'Logging in...' : 'Login'}
			</Button>
		</form>
	</Card.Content>
	<Card.Footer>
		<div class="text-sm text-center w-full text-zinc-500">
			Don't have an account? 
			<a href="/auth/register" class="text-primary hover:underline">Sign up</a>
		</div>
	</Card.Footer>
</Card.Root>

<script lang="ts">
	import { authStore } from '$lib/stores/auth.svelte';
	import { Input } from '$lib/components/ui/input';
	import { Label } from '$lib/components/ui/label';
	import { Button } from '$lib/components/ui/button';
	import * as Card from '$lib/components/ui/card';
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
			error = 'Passwords do not match';
			return;
		}

		loading = true;
		error = '';

		try {
			await authStore.register({ name, email, password, password_confirmation });
			goto('/');
		} catch (err: any) {
			error = err.message || 'Registration failed';
		} finally {
			loading = false;
		}
	}
</script>

<Card.Root>
	<Card.Header class="space-y-1">
		<Card.Title class="text-2xl text-center">Create an account</Card.Title>
		<Card.Description class="text-center">
			Enter your information below to create your account
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
				<Label for="name">Name</Label>
				<Input id="name" type="text" bind:value={name} placeholder="John Doe" required />
			</div>
			<div class="space-y-2">
				<Label for="email">Email</Label>
				<Input id="email" type="email" bind:value={email} placeholder="m@example.com" required />
			</div>
			<div class="space-y-2">
				<Label for="password">Password</Label>
				<Input id="password" type="password" bind:value={password} required minlength={8} />
			</div>
			<div class="space-y-2">
				<Label for="password_confirmation">Confirm Password</Label>
				<Input id="password_confirmation" type="password" bind:value={password_confirmation} required minlength={8} />
			</div>
			<Button class="w-full" type="submit" disabled={loading}>
				{loading ? 'Creating account...' : 'Create account'}
			</Button>
		</form>
	</Card.Content>
	<Card.Footer>
		<div class="text-sm text-center w-full text-zinc-500">
			Already have an account? 
			<a href="/auth/login" class="text-primary hover:underline">Sign in</a>
		</div>
	</Card.Footer>
</Card.Root>

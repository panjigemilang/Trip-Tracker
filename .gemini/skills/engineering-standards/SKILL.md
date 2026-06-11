---
name: engineering-standards
description: Mandatory engineering standards for Svelte/SvelteKit development covering type-safety, component-based architecture, Shadcn-Svelte usage, logic separation, and file organization.
---

# Engineering Standards (MANDATORY)

These rules are non-negotiable and apply to every task.

Violation of any rule means the implementation is considered incorrect.

---

# 1. Type-Safety First

## Never Use

```ts
any
unknown as any
as any
```

Never bypass the type system.

---

## Always

* Create explicit interfaces
* Create explicit types
* Create enums when appropriate
* Use discriminated unions when needed
* Infer types from schemas where possible

Example:

```ts
interface Trip {
  id: string;
  name: string;
  activities: TripActivity[];
}
```

instead of:

```ts
const trip: any = {};
```

---

## API Responses

Every API response must be typed.

Example:

```ts
interface GetTripResponse {
  trip: Trip;
}
```

Never use:

```ts
const response = await api.get(...);
```

without typing.

---

## Generic Components

Always provide typed generics.

Example:

```ts
Table<Trip>
```

instead of:

```ts
Table<any>
```

---

# 2. Component-Based Architecture

Large components are forbidden.

---

## Maximum Responsibilities

A component should have a single responsibility.

Bad:

```txt
TripPage.svelte

- Fetching
- Form handling
- Modal handling
- Rendering cards
- Rendering table
- Notifications
```

Good:

```txt
TripPage.svelte

TripList.svelte
TripCard.svelte
TripForm.svelte
TripModal.svelte
TripFilters.svelte
```

---

## Split Components Early

If a component exceeds:

* 200 lines

or

* multiple responsibilities

extract components.

---

## Preferred Structure

```txt
features/

  trips/

    components/

      TripCard.svelte
      TripList.svelte
      TripForm.svelte

    services/

    types/

    stores/
```

---

# 3. Shadcn-Svelte First

Use shadcn-svelte as the default UI system.

Do not create custom UI components when shadcn already provides them.

---

## Prefer

* Button
* Card
* Dialog
* Drawer
* Dropdown Menu
* Form
* Input
* Select
* Tabs
* Tooltip
* Sheet
* Table

from shadcn-svelte.

---

## Styling Rules

Use:

```txt
Tailwind + shadcn
```

Do not introduce:

* Bootstrap
* Material UI
* DaisyUI
* Flowbite

unless explicitly requested.

---

## Design Consistency

Maintain consistent:

* Radius
* Spacing
* Typography
* Colors
* Shadows

across all screens.

Never create one-off styling.

---

# 4. Separate Logic From Views

Views should remain as dumb as possible.

---

## Forbidden

Complex business logic inside:

```svelte
<script>
```

of page components.

---

## Prefer

```txt
routes/
features/
services/
stores/
```

separation.

---

## Business Logic

Move to:

```txt
features/trips/services/
```

Example:

```ts
trip-status.service.ts
trip-validation.service.ts
trip-import.service.ts
```

---

## API Logic

Move to:

```txt
services/api/
```

Example:

```ts
trip-api.ts
journey-api.ts
history-api.ts
```

---

## State Management

Move to:

```txt
stores/
```

Never manage large application state directly in components.

---

# 5. File Organization

Use feature-first architecture.

```txt
src/

lib/

  features/

    auth/
    trips/
    journey/
    history/
    import/

  components/

    ui/
    layout/
    shared/

  services/

  stores/

  types/
```

Avoid massive global folders.

---

# 6. Reusability Before Duplication

If similar code appears twice:

Create:

```txt
component
utility
service
```

instead.

Do not copy-paste implementations.

---

# 7. Svelte 5 Best Practices

Always use:

```ts
$state()
$derived()
$effect()
```

when appropriate.

Avoid legacy Svelte patterns unless required.

---

# 8. Before Finishing Any Task

Verify:

* No any types
* No duplicated logic
* No bloated components
* Uses shadcn-svelte
* Business logic extracted
* Proper folder placement
* Fully typed APIs
* Fully typed stores

If any item fails, refactor before completion.

<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';
import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/user-password';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: edit().url,
    },
];

const isCurrentPasswordVisible = ref<boolean>(false);
const isNewPasswordVisible = ref<boolean>(false);
const isPasswordConfirmationVisible = ref<boolean>(false);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Password settings">
            <meta head-key="description" name="description" content="Change and secure your King's PC account password from your settings page." />
        </Head>

        <h1 class="sr-only">Password Settings</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <Heading
                    variant="small"
                    title="Update password"
                    description="Ensure your account is using a long, random password to stay secure"
                />

                <Form
                    v-bind="PasswordController.update.form()"
                    :options="{
                        preserveScroll: true,
                    }"
                    reset-on-success
                    :reset-on-error="[
                        'password',
                        'password_confirmation',
                        'current_password',
                    ]"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="current_password">Current password</Label>
                        <div class="relative">
                            <Input
                                id="current_password"
                                name="current_password"
                                :type="isCurrentPasswordVisible ? 'text' : 'password'"
                                class="mt-1 block w-full pr-10"
                                autocomplete="current-password"
                                placeholder="Current password"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-muted-foreground transition-colors hover:text-foreground focus-visible:text-foreground focus-visible:outline-none"
                                :aria-label="isCurrentPasswordVisible ? 'Hide current password' : 'Show current password'"
                                :aria-pressed="isCurrentPasswordVisible"
                                @click="isCurrentPasswordVisible = !isCurrentPasswordVisible"
                            >
                                <component
                                    :is="isCurrentPasswordVisible ? EyeOff : Eye"
                                    class="size-4"
                                />
                            </button>
                        </div>
                        <InputError :message="errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">New password</Label>
                        <div class="relative">
                            <Input
                                id="password"
                                name="password"
                                :type="isNewPasswordVisible ? 'text' : 'password'"
                                class="mt-1 block w-full pr-10"
                                autocomplete="new-password"
                                placeholder="New password"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-muted-foreground transition-colors hover:text-foreground focus-visible:text-foreground focus-visible:outline-none"
                                :aria-label="isNewPasswordVisible ? 'Hide new password' : 'Show new password'"
                                :aria-pressed="isNewPasswordVisible"
                                @click="isNewPasswordVisible = !isNewPasswordVisible"
                            >
                                <component
                                    :is="isNewPasswordVisible ? EyeOff : Eye"
                                    class="size-4"
                                />
                            </button>
                        </div>
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation"
                            >Confirm password</Label
                        >
                        <div class="relative">
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                :type="isPasswordConfirmationVisible ? 'text' : 'password'"
                                class="mt-1 block w-full pr-10"
                                autocomplete="new-password"
                                placeholder="Confirm password"
                            />
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-muted-foreground transition-colors hover:text-foreground focus-visible:text-foreground focus-visible:outline-none"
                                :aria-label="isPasswordConfirmationVisible ? 'Hide confirm password' : 'Show confirm password'"
                                :aria-pressed="isPasswordConfirmationVisible"
                                @click="isPasswordConfirmationVisible = !isPasswordConfirmationVisible"
                            >
                                <component
                                    :is="isPasswordConfirmationVisible ? EyeOff : Eye"
                                    class="size-4"
                                />
                            </button>
                        </div>
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-password-button"
                            >Save password</Button
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>

<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
const isPasswordVisible = ref<boolean>(false);
const isPasswordConfirmationVisible = ref<boolean>(false);
</script>

<template>
    <AuthLayout
        title="Reset password"
        description="Please enter your new password below"
    >
        <Head title="Reset password">
            <meta head-key="description" name="description" content="Reset your King's PC account password securely and regain access to your account." />
        </Head>

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="inputEmail"
                        class="mt-1 block w-full"
                        readonly
                    />
                    <InputError :message="errors.email" class="mt-2" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <div class="relative">
                        <Input
                            id="password"
                            :type="isPasswordVisible ? 'text' : 'password'"
                            name="password"
                            autocomplete="new-password"
                            class="mt-1 block w-full pr-10"
                            autofocus
                            placeholder="Password"
                        />
                        <button
                            type="button"
                            class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-muted-foreground transition-colors hover:text-foreground focus-visible:text-foreground focus-visible:outline-none"
                            :aria-label="isPasswordVisible ? 'Hide password' : 'Show password'"
                            :aria-pressed="isPasswordVisible"
                            @click="isPasswordVisible = !isPasswordVisible"
                        >
                            <component
                                :is="isPasswordVisible ? EyeOff : Eye"
                                class="size-4"
                            />
                        </button>
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">
                        Confirm Password
                    </Label>
                    <div class="relative">
                        <Input
                            id="password_confirmation"
                            :type="isPasswordConfirmationVisible ? 'text' : 'password'"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="mt-1 block w-full pr-10"
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

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :disabled="processing"
                    data-test="reset-password-button"
                >
                    <Spinner v-if="processing" />
                    Reset password
                </Button>
            </div>
        </Form>
    </AuthLayout>
</template>

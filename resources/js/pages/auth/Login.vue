<script setup lang="ts">
import { library } from '@fortawesome/fontawesome-svg-core';
import { faGoogle, faFacebook } from '@fortawesome/free-brands-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import Layout from '@/layouts/MainLayout.vue';
import { register } from '@/routes';
import { redirect as facebookRedirect } from '@/routes/facebook';
import { redirect as googleRedirect } from '@/routes/google';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

library.add(faGoogle, faFacebook);

defineOptions({ layout: Layout });

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const isPasswordVisible = ref<boolean>(false);
</script>

<template>
    <AuthBase
        title="Sign in to your account"
        description="Enter your email and password below to Sign in"
    >
        <Head title="Sign in">
            <meta head-key="description" name="description" content="Sign in to your King's PC account to manage orders, profile settings, and checkout." />
        </Head>

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autofocus
                        required
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm"
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <div class="relative">
                        <Input
                            id="password"
                            required
                            :type="isPasswordVisible ? 'text' : 'password'"
                            name="password"
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Password"
                            class="pr-10"
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

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full cursor-pointer"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    Sign in
                </Button>
            </div>

            <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                Don't have an account?
                <TextLink :href="register()" :tabindex="5">Sign up</TextLink>
            </div>
        </Form>
        <div>
            <Button
                as="a"
                variant="outline"
                class="w-full"
                :tabindex="6"
                :href="googleRedirect().url"
            >
            <FontAwesomeIcon :icon="faGoogle" size="lg"/> Sign in with Google
            </Button>
            <div class="g-signin2" data-onsuccess="onSignIn"></div>
        </div>

        <div>
            <Button
                as="a"
                variant="outline"
                class="w-full"
                :tabindex="7"
                :href="facebookRedirect().url"
            >
            <FontAwesomeIcon :icon="faFacebook" size="lg"/> Sign in with Facebook
            </Button>
        </div>
    </AuthBase>
</template>

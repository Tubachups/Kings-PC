<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { redirect as facebookRedirect } from '@/routes/facebook';
import { redirect as googleRedirect } from '@/routes/google';
import { confirm as confirmPassword } from '@/routes/password';
import { store } from '@/routes/password/confirm';
import { show } from '@/routes/two-factor';

const page = usePage();

const isOauthUser = computed(() => {
    return Boolean(page.props.auth.user?.google_id || page.props.auth.user?.fb_id);
});

const intendedUrl = computed(() => {
    const [, rawQuery = ''] = page.url.split('?');
    const intended = new URLSearchParams(rawQuery).get('intended');

    if (!intended || !intended.startsWith('/')) {
        return show.url();
    }

    return intended;
});

const googleConfirmationUrl = computed(() => {
    if (!page.props.auth.user?.google_id) {
        return null;
    }

    return googleRedirect({
        query: {
            confirm_password: 1,
            intended: intendedUrl.value,
        },
    }).url;
});

const facebookConfirmationUrl = computed(() => {
    if (!page.props.auth.user?.fb_id) {
        return null;
    }

    return facebookRedirect({
        query: {
            confirm_password: 1,
            intended: intendedUrl.value,
        },
    }).url;
});

const oauthErrorMessage = computed(() => {
    const errors = page.props.errors as Record<string, string> | undefined;

    return errors?.password ?? errors?.socialite ?? null;
});
</script>

<template>
    <AuthLayout
        :title="isOauthUser ? 'Confirm your social login' : 'Confirm your password'"
        :description="
            isOauthUser
                ? 'This is a secure area of the application. Please continue with your linked social account before continuing.'
                : 'This is a secure area of the application. Please confirm your password before continuing.'
        "
    >
        <Head :title="isOauthUser ? 'Confirm social login' : 'Confirm password'">
            <meta head-key="description" name="description" content="Confirm your credentials to continue with sensitive actions in your King's PC account." />
        </Head>

        <div v-if="isOauthUser" class="space-y-4">
            <Button
                v-if="googleConfirmationUrl"
                as="a"
                variant="outline"
                class="w-full"
                :href="googleConfirmationUrl"
                data-test="confirm-google-button"
            >
                Continue with Google
            </Button>

            <Button
                v-if="facebookConfirmationUrl"
                as="a"
                variant="outline"
                class="w-full"
                :href="facebookConfirmationUrl"
                data-test="confirm-facebook-button"
            >
                Continue with Facebook
            </Button>

            <InputError :message="oauthErrorMessage ?? undefined" />

            <Button as="a" variant="ghost" class="w-full" :href="confirmPassword().url">
                Retry confirmation
            </Button>
        </div>

        <Form
            v-else
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <Spinner v-if="processing" />
                        Confirm Password
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>

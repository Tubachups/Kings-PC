<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3'
import Layout from '@/layouts/MainLayout.vue'

defineOptions({ layout: Layout })

type ContactsPageProps = {
    flash?: {
        success?: string
    }
}

const page = usePage<ContactsPageProps>()

const form = useForm({
    name: '',
    email: '',
    message: '',
})

function submitForm() {
    form.post('/contacts', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'email', 'message')
        },
    })
}
</script>

<template>
    <div class="bg-gray-50 px-2 py-8 dark:bg-neutral-950">
        <div class="mx-auto max-w-5xl">
            <h1 class="mb-2 text-center text-3xl font-bold text-gray-900 dark:text-gray-100 md:text-4xl">Stay Connected with Us</h1>
            <p class="mb-8 text-center text-lg text-gray-600 dark:text-gray-400">
                Reach out for inquiries, support, or collaboration -- we'd love to hear from you!
            </p>
            <div class="flex flex-col gap-6 rounded-2xl bg-white p-4 shadow dark:bg-neutral-900 dark:ring-1 dark:ring-gray-800 md:flex-row md:p-8">
                <form class="flex flex-1 flex-col gap-4" @submit.prevent="submitForm">
                    <div
                        v-if="page.props.flash?.success"
                        class="rounded border border-green-300 bg-green-50 px-4 py-2 text-sm text-green-700 dark:border-green-900 dark:bg-green-950/40 dark:text-green-300"
                    >
                        {{ page.props.flash.success }}
                    </div>
                    <div>
                        <label class="mb-1 block font-semibold text-gray-900 dark:text-gray-100">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Enter your name here..."
                            class="w-full rounded border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-black/20 dark:border-gray-700 dark:bg-neutral-950 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:ring-white/20"
                            required
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label class="mb-1 block font-semibold text-gray-900 dark:text-gray-100">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Enter your Email here..."
                            class="w-full rounded border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-black/20 dark:border-gray-700 dark:bg-neutral-950 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:ring-white/20"
                            required
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</div>
                    </div>
                    <div>
                        <label class="mb-1 block font-semibold text-gray-900 dark:text-gray-100">Message</label>
                        <textarea
                            v-model="form.message"
                            placeholder="Type here"
                            rows="3"
                            class="w-full resize-none rounded border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-black/20 dark:border-gray-700 dark:bg-neutral-950 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:ring-white/20"
                            required
                        ></textarea>
                        <div v-if="form.errors.message" class="mt-1 text-sm text-red-500">{{ form.errors.message }}</div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-4 rounded-lg bg-black py-2 font-semibold text-white transition hover:bg-gray-900 disabled:opacity-50 dark:bg-neutral-950 dark:text-gray-100 dark:hover:bg-accent cursor-pointer"
                    >
                        Send Message
                    </button>
                </form>

                <div class="flex min-h-75 flex-1 items-center justify-center">
                    <iframe
                        class="h-72 w-full rounded-lg border border-gray-200 dark:border-gray-700 md:h-full"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.4456144253263!2d121.0799753!3d14.573665700000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c7df9a5178cd%3A0xaae67460a6454e84!2sKing&#39;s%20Pc!5e0!3m2!1sen!2sph!4v1771234911051!5m2!1sen!2sph"
                        allowfullscreen="false"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</template>

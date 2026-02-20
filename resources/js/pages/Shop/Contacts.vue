<script setup lang="ts">
import { useForm, Form } from '@inertiajs/vue3'
import Layout from '@/layouts/MainLayout.vue'

defineOptions({ layout: Layout })

const form = useForm({
    name: '',
    email: '',
    message: '',
    services: [] as string[],
})

function submitForm() {
    // Replace '/contacts' with your actual route if needed
    alert(`Form submitted with:\nName: ${form.name}\nEmail: ${form.email}\nMessage: ${form.message}`)
    
}
</script>

<template>
    <div class="bg-gray-50 min-h-screen py-8 px-2">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold text-center mb-2">Stay Connected with Us</h1>
            <p class="text-lg text-center text-gray-600 mb-8">
                Reach out for inquiries, support, or collaboration—we'd love to hear from you!
            </p>
            <div class="bg-white rounded-2xl shadow flex flex-col md:flex-row gap-6 p-4 md:p-8">
                <!-- Contact Form -->
                <Form
                    class="flex-1 flex flex-col gap-4"
                    @submit.prevent="submitForm"
                >
                    <div>
                        <label class="block font-semibold mb-1">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Enter your name here..."
                            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-black/20"
                            required
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Enter your Email here..."
                            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-black/20"
                            required
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Message</label>
                        <textarea
                            v-model="form.message"
                            placeholder="Type here"
                            rows="3"
                            class="w-full border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-black/20 resize-none"
                            required
                        ></textarea>
                        <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">{{ form.errors.message }}</div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-4 bg-black text-white rounded-lg py-2 font-semibold hover:bg-gray-900 transition disabled:opacity-50"
                    >
                        Send Message
                    </button>
                </Form>
                <!-- Map -->
                <div class="flex-1 min-h-75 flex items-center justify-center">
                    <iframe
                        class="w-full h-72 md:h-full rounded-lg border"
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

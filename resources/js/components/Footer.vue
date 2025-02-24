<template>
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <p class="mb-2">123 Barber Street</p>
                    <p class="mb-2">London, UK</p>
                    <p class="mb-2">Phone: (123) 456-7890</p>
                    <p>Email: info@mickyesbarbers.com</p>
                </div>

                <!-- Opening Hours -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Opening Hours</h3>
                    <p class="mb-2">Monday - Friday: 9am - 7pm</p>
                    <p class="mb-2">Saturday: 9am - 6pm</p>
                    <p>Sunday: Closed</p>
                </div>

                <!-- Social Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-gray-300 transition">
                            Facebook
                        </a>
                        <a href="#" class="hover:text-gray-300 transition">
                            Instagram
                        </a>
                        <a href="#" class="hover:text-gray-300 transition">
                            Twitter
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p>&copy; {{ new Date().getFullYear() }} Mickye's Barbers. All rights reserved.</p>
            </div>
        </div>
    </footer>
</template>

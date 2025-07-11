<template>
    <TransitionRoot as="template" :show="master.basketCanvas">
        <Dialog as="div" class="relative z-[60]" @close="closeBasket">
            <TransitionChild as="template" 
                enter="ease-in-out duration-500" 
                enter-from="opacity-0" 
                enter-to="opacity-100" 
                leave="ease-in-out duration-500" 
                leave-from="opacity-100" 
                leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <TransitionChild as="template" 
                            enter="transform transition ease-in-out duration-500 sm:duration-700" 
                            enter-from="translate-x-full" 
                            enter-to="translate-x-0" 
                            leave="transform transition ease-in-out duration-500 sm:duration-700" 
                            leave-from="translate-x-0" 
                            leave-to="translate-x-full">
                            <DialogPanel class="pointer-events-auto w-screen max-w-md">
                                <!-- Your basket content -->
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
export default {
    // ... rest of the component options ...
};
</script>

<style>
    /* ... rest of the component styles ... */
</style> 
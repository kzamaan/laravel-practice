<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
            </div>
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                <div class="mt-6 text-gray-500">
                    Laravel provides a beautiful, robust starting point for your next Laravel application.
                    Laravel is designed
                    to help you build your application using a development environment that is simple, powerful, and
                    enjoyable. We believe
                    you should love expressing your creativity through programming, so we have spent time carefully
                    crafting the Laravel
                    ecosystem to be a breath of fresh air. We hope you love it.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

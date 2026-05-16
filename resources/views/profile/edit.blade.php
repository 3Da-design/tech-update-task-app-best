<x-app-layout>
    <x-slot name="header">プロフィール</x-slot>

    <x-app-container narrow>
        <x-card>
            @include('profile.partials.update-profile-information-form')
        </x-card>

        <x-card>
            @include('profile.partials.update-password-form')
        </x-card>

        <x-card>
            @include('profile.partials.delete-user-form')
        </x-card>
    </x-app-container>
</x-app-layout>

<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ ->route('token') }}">

        <div>
            <x-input-label for="email" :value="'Correo electronico'" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', ->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="'Nueva contrasena'" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="'Confirmar contrasena'" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Restablecer contrasena
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
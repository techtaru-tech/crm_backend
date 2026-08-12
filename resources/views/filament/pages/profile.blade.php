<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/views/filament/pages/profile.css') }}">
    <div class="profile-stack">
        <form wire:submit.prevent="saveProfile">
            {{ $this->profileForm }}
            <div class="profile-actions">
                <x-filament::button type="submit">{{ __('filament/profile.save_profile') }}</x-filament::button>
            </div>
        </form>

        <form wire:submit.prevent="changePassword">
            {{ $this->passwordForm }}
            <div class="profile-actions">
                <x-filament::button type="submit" color="gray">{{ __('filament/profile.update_password') }}</x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>

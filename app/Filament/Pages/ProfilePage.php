<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use UnitEnum;

class ProfilePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 40;
    protected string $view = 'filament.pages.profile';

    public static function getNavigationLabel(): string
    {
        return __('filament/profile.nav_label');
    }

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->profileForm->fill([
            'name'        => $user->name,
            'email'       => $user->email,
            'timezone'    => $user->timezone,
            'avatar_path' => $user->avatar_path,
            // Pre-populate the language preference so users see their
            // current selection.  Falls back to the app-wide default
            // locale when the column is null (legacy users who pre-
            // date the migration).
            'language'    => $user->language ?? config('app.locale'),
        ]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/profile.title');
    }

    public function profileForm(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.personal_information'))
                    ->schema([
                        FileUpload::make('avatar_path')
                            ->label(__('filament/profile.avatar'))
                            ->image()
                            // Tenant-prefix the upload path so two
                            // tenants whose user ids happen to share
                            // a hashed filename can't collide on
                            // storage/app/public/avatars/.  Mirrors
                            // the existing tenant-asset convention.
                            ->directory(fn () => 'avatars/' . (auth()->user()?->tenant_id ?? 'unscoped'))
                            // Public disk so the uploaded file lands at
                            // storage/app/public/avatars/{id}.ext and
                            // `asset('storage/...')` resolves via the
                            // public/storage symlink.  Using `local`
                            // stored under storage/app/private/, which
                            // has NO public URL — requests got a 404
                            // or a corrupted response body.
                            ->disk('public')
                            ->maxSize(2048)
                            ->avatar()
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label(__('filament/profile.full_name'))
                            ->required()
                            ->maxLength(100),

                        TextInput::make('email')
                            ->label(__('filament/profile.email_address'))
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignorable: auth()->user()),

                        Select::make('timezone')
                            ->label(__('filament/profile.timezone'))
                            ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($tz) => [$tz => $tz]))
                            ->searchable()
                            ->default('UTC'),

                        // Language preference Select.  Added the
                        // users.language column + HasLocalePreference
                        // contract, but until this UI shipped users had
                        // no way to actually PICK their language —
                        // the column stayed null forever, preferredLocale()
                        // returned null → all 9 ShouldQueue notifications
                        // rendered English regardless of which UI locale
                        // the user had selected via /locale/{code}.  Wiring
                        // this Select into the profile form closes that gap.
                        //
                        // Options are pulled from config/locales.php so
                        // adding a new locale (entries with translations
                        // under lang/{code}/*) automatically surfaces here.
                        Select::make('language')
                            ->label(__('filament/profile.language_label'))
                            ->helperText(__('filament/profile.language_helper'))
                            ->options(collect(config('locales.supported', []))
                                ->mapWithKeys(fn ($meta, $code) => [
                                    $code => ($meta['flag'] ?? '') . ' ' . ($meta['name'] ?? $code),
                                ]))
                            ->default(fn () => config('app.locale'))
                            ->native(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('profileData');
    }

    public function passwordForm(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('sections.change_password'))
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('filament/profile.current_password'))
                            ->password()
                            ->required()
                            ->currentPassword(),

                        TextInput::make('password')
                            ->label(__('filament/profile.new_password'))
                            ->password()
                            ->required()
                            ->rule(Password::defaults())
                            ->confirmed(),

                        TextInput::make('password_confirmation')
                            ->label(__('filament/profile.confirm_new_password'))
                            ->password()
                            ->required(),
                    ]),
            ])
            ->statePath('passwordData');
    }

    protected function getForms(): array
    {
        return ['profileForm', 'passwordForm'];
    }

    public function saveProfile(): void
    {
        $data = $this->profileForm->getState();

        // Validate the submitted language against the supported set so
        // a tampered form payload can't write an unsupported value to
        // users.language (which would then propagate through
        // preferredLocale() into Notification::locale()).
        $submittedLang = $data['language'] ?? null;
        $supported     = array_keys(config('locales.supported', ['en' => []]));
        $language      = is_string($submittedLang) && in_array($submittedLang, $supported, true)
            ? $submittedLang
            : null;

        auth()->user()->update([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'timezone'    => $data['timezone'] ?? null,
            'avatar_path' => $data['avatar_path'] ?? null,
            'language'    => $language,
        ]);

        // Flip the active session to the newly-selected locale so the
        // very next request renders in the user's chosen language
        // without forcing them to also hit /locale/{code}.  Stays in
        // sync with SetLocale middleware on the next page load.
        if ($language) {
            session(['locale' => $language]);
            app()->setLocale($language);
        }

        Notification::make()->title(__('notifications.profile_updated'))->success()->send();
    }

    public function changePassword(): void
    {
        $data = $this->passwordForm->getState();
        auth()->user()->update(['password' => Hash::make($data['password'])]);
        $this->passwordForm->fill();
        Notification::make()->title(__('notifications.password_changed'))->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

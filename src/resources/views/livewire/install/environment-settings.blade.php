<div class="w-full">
    @isset($errorMessage)
        <p class="text-red-500 mb-4">{{ $errorMessage }}</p>
    @endisset


    {{-- APP URL --}}
    <h2 class="border-b w-full pb-1 text-amber-500">App</h2>

    <div class="mt-6">
        <label for="app-url">App Url</label>
        <input type="text" name="app-url" id="app-url" placeholder="https://domain.com" wire:model.blur="appUrl">
        @error('appUrl')
            <span class="error">{{ $message }}</span>
        @enderror
    </div>


    {{-- DATABASE --}}
    @if ($isDatabaseRequired)
        <h2 class="border-b w-full pb-1 mt-12 text-amber-500">Database</h2>
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <label for="db-host">Host</label>
                <input type="text" name="db-host" id="db-host" placeholder="127.0.0.1" wire:model.blur="dbHost">
                @error('dbHost')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="db-port">Port</label>
                <input type="text" name="db-port" id="db-port" placeholder="3306" wire:model.blur="dbPort">
                @error('dbPort')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-span-full">
                <label for="db-database">Database</label>
                <input type="text" name="db-database" id="db-database" placeholder="Laravel" wire:model.blur="dbDatabase">
                @error('dbDatabase')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="db-username">Username</label>
                <input type="text" name="db-username" id="db-username" placeholder="root" wire:model.blur="dbUsername">
                @error('dbUsername')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="db-password">Password</label>
                <input type="password" name="db-password" id="db-password" wire:model.blur="dbPassword">
                @error('dbPassword')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    @endif

    {{-- MAIL --}}
    @if ($isMailRequired)
        <h2 class="border-b w-full pb-1 mt-12 text-amber-500">Mail</h2>
        <div class="grid grid-cols-2 gap-4 mt-6">
            <div>
                <label for="mail-mailer">Mailer</label>
                <input type="text" name="mail-mailer" id="mail-mailer" placeholder="root" wire:model.blur="mailMailer">
                @error('mailMailer')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div></div>

            <div>
                <label for="mail-host">Host</label>
                <input type="text" name="mail-host" id="mail-host" placeholder="127.0.0.1" wire:model.blur="mailHost">
                @error('mailHost')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="mail-port">Port</label>
                <input type="text" name="mail-port" id="mail-port" wire:model.blur="mailPort">
                @error('mailPort')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="mail-username">Username</label>
                <input type="text" name="mail-username" id="mail-username" wire:model.blur="mailUsername">
                @error('mailUsername')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="mail-password">Password</label>
                <input type="password" name="mail-password" id="mail-password" wire:model.blur="mailPassword">
                @error('mailPassword')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-span-full">
                <label for="mail-from-address">From Address</label>
                <input type="text" name="mail-from-address" id="mail-from-address" placeholder="yourmail@domain.com" wire:model.blur="mailFromAddress">
                @error('mailFromAddress')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-span-full">
                <label for="mail-from-name">From Name</label>
                <input type="text" name="mail-from-name" id="mail-from-name" placeholder="Admin" wire:model.blur="mailFromName">
                @error('mailFromName')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>
        </div>
    @endif

</div>

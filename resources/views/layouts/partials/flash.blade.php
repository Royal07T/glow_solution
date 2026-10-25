@if(session('success'))
    <div class="mb-6">
        <x-alert variant="success" dismissible>{{ session('success') }}</x-alert>
    </div>
@endif

@if(session('status'))
    <div class="mb-6">
        <x-alert variant="success" dismissible>{{ session('status') }}</x-alert>
    </div>
@endif

@if(session('error'))
    <div class="mb-6">
        <x-alert variant="danger" dismissible>{{ session('error') }}</x-alert>
    </div>
@endif

@if($errors->any())
    <div class="mb-6">
        <x-alert variant="danger" title="Please fix the following:">
            <ul class="mt-1 list-inside list-disc space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    </div>
@endif

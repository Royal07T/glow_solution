<div data-confirm-dialog hidden class="fixed inset-0 z-[100] flex items-end justify-center p-4 sm:items-center">
    <div data-confirm-dialog-backdrop class="absolute inset-0 bg-brand-950/65 backdrop-blur-sm"></div>

    <div
        data-confirm-dialog-panel
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-dialog-title"
        aria-describedby="confirm-dialog-message"
        tabindex="-1"
        class="relative w-full max-w-md overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-2xl"
    >
        <div aria-hidden="true" class="h-1 bg-gradient-to-r from-gold-500 via-gold-400 to-brand-700"></div>

        <div class="p-5 sm:p-6">
            <div class="flex items-start gap-4">
                <span data-confirm-dialog-icon-wrap class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                    <x-icon name="help-circle" data-confirm-dialog-icon="primary" class="h-6 w-6" />
                    <x-icon name="check" data-confirm-dialog-icon="success" class="hidden h-6 w-6" />
                    <x-icon name="alert-circle" data-confirm-dialog-icon="danger" class="hidden h-6 w-6" />
                </span>

                <div class="min-w-0 flex-1">
                    <h2 id="confirm-dialog-title" data-confirm-dialog-title class="font-display text-lg font-bold text-brand-900">Please confirm</h2>
                    <p id="confirm-dialog-message" data-confirm-dialog-message class="mt-2 text-sm leading-6 text-slate-600">Are you sure you want to continue?</p>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button type="button" data-confirm-dialog-cancel class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold-400 focus-visible:ring-offset-2">
                    Cancel
                </button>
                <button type="button" data-confirm-dialog-confirm class="rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-300 focus-visible:ring-offset-2">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

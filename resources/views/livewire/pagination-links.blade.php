<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
    <div class="flex justify-between flex-1 sm:hidden itens-center">
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled" dusk="previousPage.before" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </button>
        @endif



        <div class="flex justify-between items-center">
            <span class="mx-2 text-sm text-gray-700 self-center">
                Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>
            <select wire:model.lazy="perPage" wire:loading.attr="disabled" class="px-4 py-2 border rounded-md">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled" dusk="nextPage.after" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </button>
        @else
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-gray-100 border border-gray-300 cursor-default leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </div>

    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700 leading-5">
                {!! __('Wyświetlanie') !!}
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                {!! __('do') !!}
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
                {!! __('z') !!}
                <span class="font-medium">{{ $paginator->total() }}</span>
                {!! __('wyników') !!}
            </p>
        </div>

        <div>
            <span class="relative z-0 inline-flex shadow-sm rounded-md">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 00-1.414 0L7 9.172a1 1 0 000 1.414l3.879 3.879a1 1 0 001.414-1.414L9.414 10l2.879-2.879a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </span>
                @else
                    <button wire:click="previousPage" dusk="previousPage" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 00-1.414 0L7 9.172a1 1 0 000 1.414l3.879 3.879a1 1 0 001.414-1.414L9.414 10l2.879-2.879a1 1 0 000-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                @endif

                <!-- Informacja o aktualnej stronie w paginacji -->
                <span class="mx-2 text-sm text-gray-700">
                    Strona {{ $paginator->currentPage() }} z {{ $paginator->lastPage() }}
                </span>

                @if ($paginator->hasMorePages())
                    <button wire:click="nextPage" dusk="nextPage" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 001.414-1.414L6.414 10l2.879-2.879a1 1 0 10-1.414-1.414L4.293 10l3.879 3.879a1 1 0 001.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 001.414-1.414L6.414 10l2.879-2.879a1 1 0 10-1.414-1.414L4.293 10l3.879 3.879a1 1 0 001.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </div>
</nav>
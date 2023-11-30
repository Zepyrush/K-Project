@if ($paginator->hasPages())
    <nav>
        <ul class="pagination m-0 p-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-arrow-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link bg-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link bg-primary" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif

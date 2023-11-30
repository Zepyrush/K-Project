<aside class="main-sidebar sidebar-dark-primary elevation-4">
    @if (Auth::user()->isAdmin())
        @include('layouts.sub-dashboard.sidebar.admin')
    @elseif (Auth::user()->isDosen())
        @include('layouts.sub-dashboard.sidebar.dosen')
    @else
        @include('layouts.sub-dashboard.sidebar.mahasiswa')
    @endif
</aside>


@push('js')
    <script>
        $(document).ready(function() {
            // Set sidebar scroll position from local storage
            let item = `${noIndukUser}_sidebarScrollTop`;
            let storedScrollTop = localStorage.getItem(item);
            if (storedScrollTop) {
                $('.sidebar').scrollTop(storedScrollTop);
            }

            // Set sidebar scroll position to local storage on scroll
            $('.sidebar').scroll(function() {
                let scrollTop = $(this).scrollTop();
                localStorage.setItem(item, scrollTop);
            });
        });
    </script>
@endpush

<div class="col-lg-5">
    <div class="card card-primary card-outline mb-2">
        <div class="card-header">
            <h5 class="m-0 p-0 font-weight-bold">
                <i class="fas fa-history text-primary mr-2"></i>Riwayat Login
            </h5>
        </div>
    </div>

    <div class="card">

        <div class="card-body p-0">
            <div style="overflow: auto;max-height: 367px; border-radius: 5px;">
                <table class="table table-hover">
                    <thead style="position: sticky; top:0;">
                        <tr style="background: #fff; box-shadow: 1px 7px 11px -11px #000">
                            <th>Info User</th>
                            <th>Terakhir Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($last_login as $item)
                            @php
                                if (file_exists('assets/image/users/' . $item->foto)) {
                                    $avatar = asset('assets/image/users/' . $item->foto);
                                } else {
                                    $avatar = asset('assets/image/avatar.png');
                                }

                                (strlen($item->name) > 15) ? $item->name = substr($item->name, 0, 15) . '...'
                                    : $item->name = $item->name;

                                (strlen($item->email) > 15) ? $item->email = substr($item->email, 0, 15) . '...'
                                    : $item->email = $item->email;
                            @endphp

                            <tr>
                                <td>
                                    <a href="javascript:void(0)" class="d-flex align-items-center" style="cursor: default">
                                        <img src="{{ $avatar }}" width="40" class="avatar rounded-circle me-3">
                                        <div class="d-block ml-3" >
                                            <span class="fw-bold name-user">{{ $item->name }}</span>
                                            <div class="small text-secondary">{{ $item->email }}</div>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    @if($item->last_seen != null)
                                        {{ \Carbon\Carbon::parse($item->last_seen)->diffForHumans() }}
                                    @else
                                        <span class="badge badge-secondary">Belum pernah login</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

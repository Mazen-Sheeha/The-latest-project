@extends('layouts.app')
@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">
            لوحة التحكم
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        المدراء
    </span>
@endsection
@section('content')
    <div class="card min-w-full">
        <div class="card-header">
            <h3 class="card-title">
                المدراء
            </h3>
            <div class="flex items-center gap-5">
                <div class="menu" data-menu="true">
                    <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-start"
                        data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                        <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                            <i class="ki-filled ki-dots-vertical">
                            </i>
                        </button>
                        <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                            <div class="menu-item">
                                <a class="menu-link" href="{{ route('admins.create') }}">
                                    <span class="menu-icon">
                                        <i class="ki-filled ki-add-files">
                                        </i>
                                    </span>
                                    <span class="menu-title">
                                        إضافة
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-table scrollable-x-auto">
            <div class="scrollable-auto">
                <table class="table align-middle text-2sm text-gray-600">
                    <tr class="bg-gray-100">
                        <th class="text-start font-medium min-w-5">#</th>
                        <th class="text-start font-medium min-w-15">الاسم</th>
                        <th class="text-start font-medium min-w-56">البريد الإلكتروني</th>
                        <th class="min-w-16">التحكم</th>
                    </tr>
                    @foreach ($admins as $admin)
                        <tr>
                            <td>
                                {{ $admin->id }}
                            </td>
                            <td>
                                {{ $admin->name }}
                            </td>
                            <td>
                                {{ $admin->email }}
                            </td>
                            <td>
                                @canany(['update', 'delete'], $admin)
                                    <div class="menu inline-flex" data-menu="true">
                                        <div class="menu-item" data-menu-item-offset="0, 10px"
                                            data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start"
                                            data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical">
                                                </i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[175px]"
                                                data-menu-dismiss="true">
                                                @can('update', $admin)
                                                    <div class="menu-item">
                                                        <a class="menu-link" href="{{ route('admins.edit', $admin->id) }}">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-pencil">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                تعديل
                                                            </span>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete', $admin)
                                                    <form class="menu-item delete-form" method="POST"
                                                        action="{{ route('admins.destroy', $admin->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="menu-link" href="#">
                                                            <span class="menu-icon">
                                                                <i class="ki-filled ki-trash">
                                                                </i>
                                                            </span>
                                                            <span class="menu-title">
                                                                حذف
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="card-footer justify-center">
            {{ $admins->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.querySelectorAll(".delete-form").forEach(form => {
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "هل حقا تريد حذف هذا المدير ؟",
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "حذف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = form.action;
                        const token = form.querySelector("[name='_token']").value
                        fetch(url, {
                                method: "DELETE",
                                headers: {
                                    "Content-Type": "application/json",
                                    "Accept": "application/json, text-plain, */*",
                                    "X-Requested-With": "XMLHttpRequest",
                                    "X-CSRF-TOKEN": token
                                },
                            }).then((res) => res.json())
                            .then(data => {
                                if (data['success'] && data['message']) {
                                    form.closest("tr").remove();
                                    return toastify().success(data['message']);
                                }
                                toastify().error(data['message'])
                            })
                            .catch(() => {
                                toastify().error("حدث خطأ أثناء حذف المدير")
                            })
                    }
                });
            });
        });
    </script>
@endsection

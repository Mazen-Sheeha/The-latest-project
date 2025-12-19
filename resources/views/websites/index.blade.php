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
        الدومينات
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                أضف دومين
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('websites.store') }}" method="post" id="add-website"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">ال key</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="key" placeholder="key"
                            value="{{ old('key') }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold">الدومين</h5>
                    </div>
                    <div class="flex items-center relative">
                        <input type="text" placeholder="trendow.com" class="input w-full" placeholder="الدومين"
                            name="domain" value="{{ old('domain') }}">
                    </div>
                </div>
                <button type="submit"
                    class="btn btn-light hover:bg-blue-600 text-white px-4 py-2 rounded-md md:ml-auto transition-colors duration-200">
                    إضافة<i class="fas fa-plus me-2 mr-5"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="card min-w-full">
        <div class="card-header">
            <h3 class="card-title">
                الدومينات
            </h3>
        </div>
        <div class="card-table scrollable-x-auto">
            <div class="scrollable-auto">
                <table class="table align-middle text-2sm text-gray-600" id="companies-table">
                    @if ($websites->count() > 0)
                        <tr class="bg-gray-100">
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-56">ال key</th>
                            <th class="text-start font-medium min-w-15">الدومين</th>
                            <th class="min-w-16">التحكم</th>
                        </tr>
                        @foreach ($websites as $website)
                            <tr>
                                <td>
                                    {{ $website->id }}
                                </td>
                                <td>
                                    {{ $website->key }}
                                </td>
                                <td>
                                    {{ $website->domain }}
                                </td>
                                <td>
                                    <div class="menu inline-flex" data-menu="true">
                                        <div class="menu-item" data-menu-item-offset="0, 10px"
                                            data-menu-item-placement="bottom-end"
                                            data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown"
                                            data-menu-item-trigger="click|lg:click">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical">
                                                </i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[175px]"
                                                data-menu-dismiss="true">
                                                <div class="menu-item">
                                                    <a class="menu-link" href="{{ route('websites.edit', $website->id) }}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            تعديل
                                                        </span>
                                                    </a>
                                                </div>
                                                @can('access-delete-any-thing')
                                                    @if (!$website->products_count)
                                                        <form class="menu-item delete-form"
                                                            action="{{ route('websites.destroy', $website->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" class="id" value="{{ $website->id }}">
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
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <h5 class="p-3 text-center" id="no-items">لا توجد دومينات</h5>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer justify-center">
            {{ $websites->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const addForm = document.getElementById("add-website");

            if (addForm) {
                addForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    const form = e.target;

                    sendRequest("POST", form, "حدث خطأ أثناء إضافة الدومين", {
                        key: form.querySelector("[name='key']").value,
                        domain: form.querySelector("[name='domain']").value,
                    });
                });
            }

            document.querySelectorAll(".delete-form").forEach(form => {
                form.addEventListener("submit", deleteHandler);
            });

            function deleteHandler(e) {
                e.preventDefault();
                const form = e.target;
                Swal.fire({
                    title: "هل حقا تريد حذف هذا الدومين ؟",
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "حذف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendRequest("DELETE", form, "حدث خطأ أثناء حذف الدومين");
                    }
                });
            }

            function sendRequest(method, form, errMsg, data = null) {
                const token = form.querySelector("[name='_token']").value;
                const url = form.action;
                fetch(url, {
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json, text-plain, */*",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": token
                        },
                        method,
                        body: method === "POST" && data ? JSON.stringify(data) : null
                    })
                    .then(res => res.json())
                    .then((data) => {
                        if (data.success && data.message) {
                            toastify().success(data.message);

                            if (method === "DELETE") {
                                form.closest("tr").remove();
                            }

                            if (method === "POST" && data.website) {
                                appendCompanyRow(data.website);
                                form.reset();
                            }

                        } else {
                            toastify().error(data.message || errMsg);
                        }
                    });
            }

            function appendCompanyRow(website) {
                let table = document.querySelector("#companies-table tbody");
                if (!table) {
                    document.getElementById("no-items").remove();
                    table = document.querySelector("#companies-table");
                    const tbody = document.createElement('tbody');
                    const tableHeader = document.createElement('tr');
                    tableHeader.classList.add("bg-gray-100");
                    tableHeader.innerHTML = `
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">ال key</th>
                            <th class="text-start font-medium min-w-56">الدومين</th>
                            <th class="min-w-16">التحكم</th>
                    `;
                    tbody.appendChild(tableHeader);
                    table.appendChild(tbody);
                }
                table = document.querySelector("#companies-table tbody");
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                                <td>
                                   ${website.id}
                                </td>
                                <td>
                                    ${website.key}
                                </td>
                                <td>
                                    ${website.domain}
                                </td>
                                <td>
                                    <div class="menu inline-flex" data-menu="true">
                                        <div class="menu-item" data-menu-item-offset="0, 10px"
                                            data-menu-item-placement="bottom-end"
                                            data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown"
                                            data-menu-item-trigger="click|lg:click">
                                            <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                                <i class="ki-filled ki-dots-vertical">
                                                </i>
                                            </button>
                                            <div class="menu-dropdown menu-default w-full max-w-[175px]"
                                                data-menu-dismiss="true">
                                                <div class="menu-item">
                                                    <a class="menu-link"
                                                        href="/websites/${website.id}/edit">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            تعديل
                                                        </span>
                                                    </a>
                                                </div>
                                                <form class="menu-item delete-form"
                                                 action="/websites/${website.id}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" class="id"
                                                        value="${website.id}">
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
                                            </div>
                                        </div>
                                    </div>
                                </td>
            `;
                table.appendChild(newRow);
                const newDeleteForm = newRow.querySelector(".delete-form");
                if (newDeleteForm) {
                    newDeleteForm.addEventListener("submit", deleteHandler);
                }
            }
        });
    </script>
@endsection

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
        شركات الشحن
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                أضف شركة شحن
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('shipping_companies.store') }}" method="post" id="add-company"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">اسم شركة الشحن</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="name" placeholder="اسم شركة الشحن"
                            value="{{ old('name') }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold">سعر الشركة</h5>
                    </div>
                    <div class="flex items-center relative">
                        <input type="text" min="1" max="1000" placeholder="5" class="input" id="price"
                            placeholder="السعر" name="price" style="width: 100px"
                            value="{{ old('shipping_price') ? old('shipping_price') : 20 }}">
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
                شركات الشحن
            </h3>
        </div>
        <div class="card-table scrollable-x-auto">
            <div class="scrollable-auto">
                <table class="table align-middle text-2sm text-gray-600" id="companies-table">
                    @if ($shipping_companies->count() > 0)
                        <tr class="bg-gray-100">
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">الاسم</th>
                            <th class="text-start font-medium min-w-56">السعر</th>
                            <th class="min-w-16">التحكم</th>
                        </tr>
                        @foreach ($shipping_companies as $shipping_company)
                            <tr>
                                <td>
                                    {{ $shipping_company->id }}
                                </td>
                                <td>
                                    {{ $shipping_company->name }}
                                </td>
                                <td>
                                    AED {{ $shipping_company->price }}
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
                                                        href="{{ route('shipping_companies.edit', $shipping_company->id) }}">
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
                                                    @if (!$shipping_company->products_count)
                                                        <form class="menu-item delete-form"
                                                            action="{{ route('shipping_companies.destroy', $shipping_company->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" class="id"
                                                                value="{{ $shipping_company->id }}">
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
                        <h5 class="p-3 text-center" id="no-items">لا توجد شركات شحن</h5>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer justify-center">
            {{ $shipping_companies->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById('price').addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9.]/g, '');
                if ((this.value.match(/\./g) || []).length > 1) {
                    this.value = this.value.substr(0, this.value.length - 1);
                }
            });

            const addForm = document.getElementById("add-company");

            if (addForm) {
                addForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    const form = e.target;

                    sendRequest("POST", form, "حدث خطأ أثناء إضافة الشركة", {
                        name: form.querySelector("[name='name']").value,
                        price: form.querySelector("[name='price']").value,
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
                    title: "هل حقا تريد حذف هذه الشركة ؟",
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "حذف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendRequest("DELETE", form, "حدث خطأ أثناء حذف الشركة");
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

                            if (method === "POST" && data.company) {
                                appendCompanyRow(data.company);
                                form.reset();
                            }

                        } else {
                            toastify().error(data.message || errMsg);
                        }
                    });
            }

            function appendCompanyRow(company) {
                let table = document.querySelector("#companies-table tbody");
                if (!table) {
                    document.getElementById("no-items").remove();
                    table = document.querySelector("#companies-table");
                    const tbody = document.createElement('tbody');
                    const tableHeader = document.createElement('tr');
                    tableHeader.classList.add("bg-gray-100");
                    tableHeader.innerHTML = `
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">الاسم</th>
                            <th class="text-start font-medium min-w-56">السعر</th>
                            <th class="min-w-16">التحكم</th>
                    `;
                    tbody.appendChild(tableHeader);
                    table.appendChild(tbody);
                }
                table = document.querySelector("#companies-table tbody");
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                                <td>
                                   ${company.id}
                                </td>
                                <td>
                                    ${company.name}
                                </td>
                                <td>
                                    AED ${company.price}
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
                                                        href="/shipping_companies/${company.id}/edit">
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
                                                 action="/shipping_companies/${company.id}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" class="id"
                                                        value="${company.id}">
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

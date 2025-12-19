@extends('layouts.app')
@section('style')
    <style>
        .active-and-not {
            color: white;
            width: 70px !important;
            padding: 2px 5px;
            border-radius: 5px;
            cursor: pointer;
        }

        .active-y {
            background: green;
        }

        .active-n {
            background: red;
        }
    </style>
@endsection
@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">
            لوحة التحكم
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        مجموعات الحملات الإعلانية
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                أضف مجموعة حملات إعلانية
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('adsets.store') }}" method="post" id="add-adset"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">اسم المجموعة</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="name" placeholder="اسم المجموعة"
                            value="{{ old('name') }}">
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
                مجموعات الحملات الإعلانية
            </h3>
        </div>
        <div class="card-table scrollable-x-auto">
            <div class="scrollable-auto">
                <table class="table align-middle text-2sm text-gray-600" id="adsets-table">
                    @if ($adsets->count() > 0)
                        <tr class="bg-gray-100">
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">الاسم</th>
                            <th class="text-start font-medium min-w-15">الحملات الإعلانية</th>
                            <th class="text-start font-medium min-w-15">نشط</th>
                            <th class="min-w-16">التحكم</th>
                        </tr>
                        @foreach ($adsets as $adset)
                            <tr>
                                <td>
                                    {{ $adset->id }}
                                </td>
                                <td class="adset-name">
                                    {{ $adset->name }}
                                </td>
                                <td>
                                    <a href="{{ route('campaigns.index', ['adset' => $adset->id]) }}"
                                        style="color: blue; text-decoration: underline">الحملات الإعلانية</a>
                                </td>
                                <td>
                                    <form action="{{ route('adsets.changeActive', $adset->id) }}" class="active_form"
                                        style="margin: 0 !important" method="POST">
                                        @csrf
                                        <button
                                            class="active-and-not @if ($adset->active) active-y @else active-n @endif">
                                            {{ $adset->active ? 'نشط' : 'غير نشط' }}
                                        </button>
                                    </form>
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
                                                <form class="menu-item update-form"
                                                    action="{{ route('adsets.update', $adset->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="name" value="{{ $adset->name }}">
                                                    <button class="menu-link" href="#">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            تعديل
                                                        </span>
                                                    </button>
                                                </form>
                                                @can('access-delete-any-thing')
                                                    @if (!$adset->products_count)
                                                        <form class="menu-item delete-form"
                                                            action="{{ route('adsets.destroy', $adset->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" class="id" value="{{ $adset->id }}">
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
                        <h5 class="p-3 text-center" id="no-items">لا توجد مجموعات حملات الإعلانية</h5>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer justify-center">
            {{ $adsets->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const addForm = document.getElementById("add-adset");

            if (addForm) {
                addForm.addEventListener("submit", function(e) {
                    e.preventDefault();
                    const form = e.target;

                    sendRequest("POST", form, "حدث خطأ أثناء إضافة المجموعة", {
                        name: form.querySelector("[name='name']").value,
                    });
                });
            }

            document.querySelectorAll(".delete-form").forEach(form => {
                form.addEventListener("submit", deleteHandler);
            });

            document.querySelectorAll(".update-form").forEach(form => {
                form.addEventListener("submit", updateHandler);
            })

            document.querySelectorAll(".active_form").forEach(form => {
                form.addEventListener("submit", handleChangeActive);
            })

            function deleteHandler(e) {
                e.preventDefault();
                const form = e.target;
                Swal.fire({
                    title: "هل أنت متأكد من حذف هذا المجموعة ؟",
                    text: "كل الحملات التابعة لهذا المجموعة سيتم حذفها",
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "حذف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendRequest("DELETE", form, "حدث خطأ أثناء حذف المجموعة");
                    }
                });
            }

            function updateHandler(e) {
                e.preventDefault();
                const form = e.target.closest("form");
                Swal.fire({
                    title: "اكتب اسم المجموعة",
                    input: "text",
                    inputValue: form.querySelector("[name='name']").value,
                    inputAttributes: {
                        autocapitalize: "off"
                    },
                    showCancelButton: true,
                    confirmButtonText: "تغيير",
                    cancelButtonText: "إلغاء",
                    showLoaderOnConfirm: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendRequest("PUT", form,
                            "حدث خطأ أثناء تعديل اسم المجموعة", {
                                name: result.value
                            });
                    }
                });
            }

            function handleChangeActive(e) {
                e.preventDefault();
                sendRequest("POST", e.target, "حدث خطأ أثناء تغيير حالة ال Active");
            }

            function sendRequest(method, form, errMsg, bodyData = null) {
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
                        body: bodyData ? JSON.stringify(bodyData) : null
                    })
                    .then(res => res.json())
                    .then((data) => {
                        if (data.success && data.message) {
                            toastify().success(data.message);

                            if (method === "DELETE") {
                                form.closest("tr").remove();
                            }

                            if (method === "POST" && 'adset' in data) {
                                appendCompanyRow(data.adset);
                                form.reset();
                            }

                            if (method === "POST" && 'active' in data) {
                                const btn = form.querySelector("button");
                                btn.innerHTML = data.active ? "نشط" :
                                    "غير نشط";
                                btn.style.background = data.active ?
                                    "green" : "red";
                            }

                            if (method === 'PUT') {
                                form.closest("tr").querySelector(".adset-name").innerHTML = bodyData.name;
                            }

                        } else {
                            toastify().error(data.message || errMsg);
                        }
                    });
            }

            function appendCompanyRow(adset) {
                let table = document.querySelector("#adsets-table tbody");
                if (!table) {
                    document.getElementById("no-items").remove();
                    table = document.querySelector("#adsets-table");
                    const tbody = document.createElement('tbody');
                    const tableHeader = document.createElement('tr');
                    tableHeader.classList.add("bg-gray-100");
                    tableHeader.innerHTML = `
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">الاسم</th>
                            <th class="text-start font-medium min-w-15">نشط</th>
                            <th class="text-start font-medium min-w-15">الحملات الإعلانية</th>
                            <th class="min-w-16">التحكم</th>
                    `;
                    tbody.appendChild(tableHeader);
                    table.appendChild(tbody);
                }
                table = document.querySelector("#adsets-table tbody");
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                                <td>
                                   ${adset.id}
                                </td>
                                <td class="adset-name">
                                    ${adset.name}
                                </td>
                                <td>
                                    <a href='/campaigns?adset=${adset.id}' style="color: blue; text-decoration: underline">الحملات الإعلانية</a>
                                </td>
                                <td>
                                    <form action="/adsets/${adset.id}/active" class="active_form"
                                        style="margin: 0 !important" method="POST">
                                        @csrf
                                        <button
                                            class="active-and-not active-y">
                                            نشط
                                        </button>
                                    </form>
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
                                                 <form class="menu-item update-form"
                                                    action="/adsets/${adset.id}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="name" value="${adset.name}">
                                                    <button class="menu-link" href="#">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-pencil">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            تعديل
                                                        </span>
                                                    </button>
                                                </form>
                                                @can('access-delete-any-thing')
                                                    <form class="menu-item delete-form"
                                                     action="/adsets/${adset.id}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" class="id"
                                                            value="${adset.id}">
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
                                </td>
                            `;
                table.appendChild(newRow);
                const newDeleteForm = newRow.querySelector(".delete-form");
                const newUpdateForm = newRow.querySelector(".update-form");
                const newChangeActiveForm = newRow.querySelector(".active_form");
                if (newDeleteForm)
                    newDeleteForm.addEventListener("submit", deleteHandler);
                if (newUpdateForm)
                    newUpdateForm.addEventListener("submit", updateHandler);
                if (newChangeActiveForm)
                    newChangeActiveForm.addEventListener("submit", handleChangeActive);
            }
        });
    </script>
@endsection

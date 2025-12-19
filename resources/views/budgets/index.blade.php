@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection
@section('url_pages')
    <span class="text-gray-700">
        <a href="{{ route('home') }}" style="color:rgb(114, 114, 255);">
            لوحة التحكم
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    @if ($budgets->count() > 0)
        <span class="text-gray-700">
            {{ $budgets[0]->campaign->campaign }}
        </span>
        <i class="ki-filled ki-left text-gray-500 text-3xs">
        </i>
    @endif
    <span class="text-gray-700">
        الميزانية
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                أضف ميزانية
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('budgets.store') }}" method="post" id="add-budget"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                <input type="hidden" value="{{ request()->campaign }}" name="campaign_id">
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">الميزانية</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="budget" placeholder="الميزانية"
                            value="{{ old('budget') }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">التاريخ</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="search" id="date" name="date" placeholder="التاريخ" class="input"
                            value="{{ old('date') ? old('date') : now() }}" />

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
                الميزانية
            </h3>
        </div>
        <div class="card-table scrollable-x-auto">
            <div class="scrollable-auto">
                <table class="table align-middle text-2sm text-gray-600" id="budgets-table">
                    @if ($budgets->count() > 0)
                        <tr class="bg-gray-100">
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">الميزانية</th>
                            <th class="text-start font-medium min-w-15">التاريخ</th>
                            <th class="min-w-16">التحكم</th>
                        </tr>
                        @foreach ($budgets as $budget)
                            <tr>
                                <td>
                                    {{ $budget->id }}
                                </td>
                                <td class="budget-name">
                                    AED {{ $budget->budget }}
                                </td>
                                <td>
                                    {{ $budget->date }}
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
                                                    <a class="menu-link" href="{{ route('budgets.edit', $budget->id) }}">
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
                                                    <form class="menu-item delete-form"
                                                        action="{{ route('budgets.destroy', $budget->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" class="id" value="{{ $budget->id }}">
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
                            </tr>
                        @endforeach
                    @else
                        <h5 class="p-3 text-center" id="no-items">لا توجد ميزانية</h5>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer justify-center">
            {{ $budgets->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <script>
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            locale: "ar"
        });

        document.addEventListener("DOMContentLoaded", () => {
            const addForm = document.getElementById("add-budget");

            addForm?.addEventListener("submit", function(e) {
                e.preventDefault();
                const form = e.target;
                console.log(form.querySelector("[name='date']").value)

                sendRequest("POST", form, "حدث خطأ أثناء إضافة الميزانية", {
                    budget: form.querySelector("[name='budget']").value,
                    date: form.querySelector("[name='date']").value,
                    campaign_id: form.querySelector("[name='campaign_id']").value,
                });
            });

            document.querySelectorAll(".delete-form").forEach(form => {
                form.addEventListener("submit", deleteHandler);
            });

            function deleteHandler(e) {
                e.preventDefault();
                const form = e.target;
                Swal.fire({
                    title: "هل أنت متأكد من حذف هذه الميزانية ؟",
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "حذف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendRequest("DELETE", form, "حدث خطأ أثناء حذف الميزانية");
                    }
                });
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

                            if (method === "POST" && data.budget) {
                                appendCompanyRow(data.budget);
                                form.reset();
                            }

                            if (method === 'PUT') {
                                form.closest("tr").querySelector(".budget-name").innerHTML = bodyData.name;
                            }

                        } else {
                            toastify().error(data.message || errMsg);
                        }
                    });
            }

            function appendCompanyRow(budget) {
                let table = document.querySelector("#budgets-table tbody");
                if (!table) {
                    document.getElementById("no-items").remove();
                    table = document.querySelector("#budgets-table");
                    const tbody = document.createElement('tbody');
                    const tableHeader = document.createElement('tr');
                    tableHeader.classList.add("bg-gray-100");
                    tableHeader.innerHTML = `
                             <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">الميزانية</th>
                            <th class="text-start font-medium min-w-15">التاريخ</th>
                            <th class="min-w-16">التحكم</th>
                    `;
                    tbody.appendChild(tableHeader);
                    table.appendChild(tbody);
                }
                table = document.querySelector("#budgets-table tbody");
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                                <td>
                                   ${budget.id}
                                </td>
                                <td>
                                   AED ${budget.budget}
                                </td>
                                <td>
                                    ${budget.date}
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
                                                    <a class="menu-link" href="/budgets/${budget.id}/edit">
                                                        <span class="menu-icon"><i class="ki-filled ki-pencil"></i></span>
                                                        <span class="menu-title">تعديل</span>
                                                    </a>
                                                </div>
                                                <form class="menu-item delete-form"
                                                    action="/budgets/${budget.id}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" class="id" value="${budget.id}">
                                                    <button class="menu-link">
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

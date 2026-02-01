@extends('layouts.app')
@section('style')
    <style>
        .active-and-not {
            color: white;
            width: 100% !important;
            padding: 2px 5px;
            border-radius: 5px;
            cursor: pointer
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
        <a href="{{ route('adsets.index') }}" style="color:rgb(114, 114, 255);">
            مجموعات الحملات الإعلانية
        </a>
    </span>
    <i class="ki-filled ki-left text-gray-500 text-3xs">
    </i>
    <span class="text-gray-700">
        الحملات الإعلانية
    </span>
@endsection
@section('content')
    <div class="card bg-white shadow-sm rounded-lg border border-gray-200 flex mb-5">
        <div class="card-header">
            <h3 class="card-title">
                أضف حملة إعلانية
            </h3>
        </div>
        <div class="card-body p-6">
            <form action="{{ route('campaigns.store') }}" method="post" id="add-campaign"
                class="flex flex-col md:flex-row items-center justify-between gap-6">
                @csrf
                <input type="hidden" name="adset_id" value="{{ request()->adset }}">
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">اسم الحملة</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <input type="text" class="input w-full" name="campaign" placeholder="اسم الحملة"
                            value="{{ old('campaign') }}">
                    </div>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">مصدر الحملة</h5>
                    </div>
                    <div class="flex items-center relative flex-1">
                        <select name="source" class="select">
                            @php
                                $sources = ['facebook', 'tiktok', 'snapchat', 'google', 'whatsapp'];
                            @endphp
                            @foreach ($sources as $src)
                                <option value="{{ $src }}" @selected(old('source') === '{{ $src }}')>{{ $src }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- @php
                    use App\Models\Page;
                    $pages = Page::select('id', 'slug')->where('is_active', 1)->get();
                @endphp --}}

                <div class="flex gap-2 flex-1">
                    <div class="flex items-center">
                        <h5 class="text-gray-800 font-semibold text-nowrap">
                            رابط المنتوج
                        </h5>
                    </div>

                    <div class="flex items-center relative flex-1">
                        {{-- <select name="page_id" class="form-select" required>
                            <option value="">اختر صفحة المنوج</option>

                            @foreach ($pages as $page)
                                <option value="{{ $page->id }}">
                                    {{ $page->slug }}
                                </option>
                            @endforeach
                        </select> --}}
                        <input type="text" class="input w-full" name="url" placeholder="رابط المنتوج"
                            value="{{ old('url') }}">
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
                الحملات الإعلانية
            </h3>
        </div>
        <div class="card-table scrollable-x-auto">
            <div class="scrollable-auto">
                <table class="table align-middle text-2sm text-gray-600" id="campaigns-table">
                    @if ($campaigns->count() > 0)
                        <tr class="bg-gray-100">
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">اسم الحملة</th>
                            <th class="text-start font-medium min-w-15">مصدر الحملة</th>
                            <th class="text-start font-medium min-w-15">الرابط</th>
                            <th class="text-start font-medium min-w-15">الميزانية</th>
                            <th class="text-start font-medium min-w-15">نشط</th>
                            <th class="min-w-16">التحكم</th>
                        </tr>
                        @foreach ($campaigns as $campaign)
                            <tr>
                                <td>
                                    {{ $campaign->id }}
                                </td>
                                <td class="campaign-name">
                                    {{ $campaign->campaign }}
                                </td>
                                <td>
                                    {{ $campaign->source }}
                                </td>
                                <td>
                                    <a href='{{ $campaign->url }}'
                                        style="color:blue; text-decoration: underline;">{{ $campaign->url }}</a>

                                </td>
                                <td>
                                    {{ $campaign->budgets_sum_budget ?? 0 }}
                                </td>
                                <td>
                                    <form action="{{ route('campaigns.changeActive', $campaign->id) }}" class="active_form"
                                        style="margin: 0 !important" method="POST">
                                        @csrf
                                        <button
                                            class="active-and-not @if ($campaign->active) active-y @else active-n @endif">
                                            {{ $campaign->active ? 'نشط' : 'غير نشط' }}
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
                                                <div class="menu-item">
                                                    <a class="menu-link"
                                                        href="{{ route('budgets.index', ['campaign' => $campaign->id]) }}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-dollar"></i>
                                                        </span>
                                                        <span class="menu-title">
                                                            إدارة الميزانية
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-separator">
                                                </div>
                                                <div class="menu-item">
                                                    <a class="menu-link"
                                                        href="{{ route('campaigns.edit', $campaign->id) }}">
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
                                                        action="{{ route('campaigns.destroy', $campaign->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" class="id" value="{{ $campaign->id }}">
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
                        <h5 class="p-3 text-center" id="no-items">لا توجد حملات إعلانية</h5>
                    @endif
                </table>
            </div>
        </div>
        <div class="card-footer justify-center">
            {{ $campaigns->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const addForm = document.getElementById("add-campaign");

            addForm?.addEventListener("submit", function(e) {
                e.preventDefault();
                const form = e.target;

                sendRequest("POST", form, "حدث خطأ أثناء إضافة الحملة", {
                    campaign: form.querySelector("[name='campaign']").value,
                    source: form.querySelector("[name='source']").value,
                    // page_id: form.querySelector("[name='page_id']").value,
                    url: form.querySelector("[name='url']").value,
                    adset_id: form.querySelector("[name='adset_id']").value,
                });
            });

            document.querySelectorAll(".delete-form").forEach(form => {
                form.addEventListener("submit", deleteHandler);
            });

            document.querySelectorAll(".active_form").forEach(form => {
                form.addEventListener("submit", handleChangeActive);
            })

            function deleteHandler(e) {
                e.preventDefault();
                const form = e.target;
                Swal.fire({
                    title: "هل أنت متأكد من حذف هذه الحملة ؟",
                    text: "كل الحملات التابعة لهذه الحملة سيتم حذفها",
                    showCancelButton: true,
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "حذف",
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendRequest("DELETE", form, "حدث خطأ أثناء حذف الحملة");
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

                            if (method === "POST" && data.campaign) {
                                appendCompanyRow(data.campaign);
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
                                form.closest("tr").querySelector(".campaign-name").innerHTML = bodyData.name;
                            }

                        } else {
                            toastify().error(data.message || errMsg);
                        }
                    });
            }

            function appendCompanyRow(campaign) {
                let table = document.querySelector("#campaigns-table tbody");
                if (!table) {
                    document.getElementById("no-items").remove();
                    table = document.querySelector("#campaigns-table");
                    const tbody = document.createElement('tbody');
                    const tableHeader = document.createElement('tr');
                    tableHeader.classList.add("bg-gray-100");
                    tableHeader.innerHTML = `
                            <th class="text-start font-medium min-w-5">#</th>
                            <th class="text-start font-medium min-w-15">اسم الحملة</th>
                            <th class="text-start font-medium min-w-15">مصدر الحملة</th>
                            <th class="text-start font-medium min-w-15">الرابط</th>
                            <th class="text-start font-medium min-w-15">الميزانية</th>
                            <th class="text-start font-medium min-w-15">نشط</th>
                            <th class="min-w-16">التحكم</th>
                    `;
                    tbody.appendChild(tableHeader);
                    table.appendChild(tbody);
                }
                table = document.querySelector("#campaigns-table tbody");
                const newRow = document.createElement("tr");
                newRow.innerHTML = `
                                <td>
                                   ${campaign.id}
                                </td>
                                <td>
                                    ${campaign.campaign}
                                </td>
                                <td>
                                    ${campaign.source}
                                </td>
                                <td>
                                    <a href='${campaign.url}'' style="color:blue; text-decoration: underline;">${campaign.url}</a>
                                </td>
                                <td>
                                    0
                                </td>
                                <td>
                                    <form action="/campaigns/${campaign.id}/active" class="active_form"
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
                                                <div class="menu-item">
                                                    <a class="menu-link" href="/budgets?campaign=${campaign.id}">
                                                        <span class="menu-icon">
                                                            <i class="ki-filled ki-search-list">
                                                            </i>
                                                        </span>
                                                        <span class="menu-title">
                                                            إدارة الميزانية
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="menu-separator">
                                                </div>
                                                <div class="menu-item">
                                                    <a class="menu-link" href="/campaigns/${campaign.id}/edit">
                                                        <span class="menu-icon"><i class="ki-filled ki-pencil"></i></span>
                                                        <span class="menu-title">تعديل</span>
                                                    </a>
                                                </div>
                                                @can('access-delete-any-thing')
                                                    <form class="menu-item delete-form"
                                                            action="/campaigns/${campaign.id}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" class="id" value="${campaign.id}">
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
                const newChangeActiveForm = newRow.querySelector(".active_form");
                if (newDeleteForm) {
                    newDeleteForm.addEventListener("submit", deleteHandler);
                }
                if (newChangeActiveForm) {
                    newChangeActiveForm.addEventListener("submit", handleChangeActive);
                }
            }
        });
    </script>
@endsection

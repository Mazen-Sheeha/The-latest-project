@toastifyCss
<!DOCTYPE html>
<html class="h-full" data-theme="true" data-theme-mode="light" dir="ltr" lang="en">

<head>
    <base href="../../">
    <title>
        Admin Dashboard
    </title>
    <meta charset="utf-8" />
    <meta content="follow, index" name="robots" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="@keenthemes" name="twitter:site" />
    <meta content="@keenthemes" name="twitter:creator" />
    <meta content="summary_large_image" name="twitter:card" />
    <meta content="Metronic - Tailwind CSS " name="twitter:title" />
    <meta content="" name="twitter:description" />
    <meta content="{{ asset('adminTemplate') }}/media/app/og-image.png" name="twitter:image" />
    <meta content="en_US" property="og:locale" />
    <meta content="website" property="og:type" />
    <meta content="@keenthemes" property="og:site_name" />
    <meta content="Metronic - Tailwind CSS " property="og:title" />
    <meta content="" property="og:description" />
    <meta content="{{ asset('adminTemplate') }}/media/app/og-image.png" property="og:image" />
    <link href="{{ asset('adminTemplate') }}/media/app/apple-touch-icon.png" rel="apple-touch-icon" sizes="180x180" />
    <link href="{{ asset('images/shopping-cart.svg') }}" rel="icon" type="image/svg" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ asset('adminTemplate') }}/vendors/apexcharts/apexcharts.css" rel="stylesheet" />
    <link href="{{ asset('adminTemplate') }}/vendors/keenicons/styles.bundle.css" rel="stylesheet" />
    <link href="{{ asset('adminTemplate') }}/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    @yield('style')
    <style>
        * {
            font-family: "Cairo", sans-serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
            font-variation-settings:
                "slnt" 0;
        }
    </style>
</head>

<body
    class="antialiased flex h-full text-base text-gray-700 [--tw-page-bg:#fefefe] [--tw-page-bg-dark:var(--tw-coal-500)] demo1 sidebar-fixed header-fixed bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]"
    dir="rtl">
    @include('inc.loader')
    @include('inc.message')
    <!-- Page -->
    @auth
        @include('inc.notifications')
        <div class="flex grow">
            <!-- Sidebar -->
            <div class="sidebar dark:bg-coal-600 bg-light border-e border-e-gray-200 dark:border-e-coal-100 fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0"
                data-drawer="true" data-drawer-class="drawer drawer-start top-0 bottom-0" data-drawer-enable="true|lg:false"
                id="sidebar">
                <div class="sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0"
                    id="sidebar_header">
                    <a class="dark:hidden overflow-hidden" href="{{ route('home') }}">
                        الصفحة الرئيسية
                    </a>
                    <button
                        class="btn btn-icon btn-icon-md size-[30px] rounded-lg border border-gray-200 dark:border-gray-300 bg-light text-gray-500 hover:text-gray-700 toggle absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
                        data-toggle="body" data-toggle-class="sidebar-collapse" id="sidebar_toggle">
                        <i
                            class="ki-filled ki-black-left-line toggle-active:rotate-180 transition-all duration-300 rtl:translate rtl:rotate-180 rtl:toggle-active:rotate-0">
                        </i>
                    </button>
                </div>

                <div class="sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
                    <div class="scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3" data-scrollable="true"
                        data-scrollable-dependencies="#sidebar_header" data-scrollable-height="auto"
                        data-scrollable-offset="0px" data-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
                        <!-- Sidebar Menu -->
                        <div class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false"
                            id="sidebar_menu">
                            @can('access-statistics')
                                <div class="menu-item @if (request()->is('statistics*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('statistics') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            الإحصائيات
                                        </a>
                                    </div>
                                </div>
                            @endcan
                            @can('access-orders')
                                <div class="menu-item @if (request()->is('orders*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('orders.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            الطلبات
                                        </a>
                                    </div>
                                </div>
                            @endcan
                            @can('access-products')
                                <div class="menu-item @if (request()->is('products*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('products.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            المنتوجات
                                        </a>
                                    </div>
                                </div>
                            @endcan
                            @can('access-ads')
                                <div class="menu-item @if (request()->is('adsets*') ||
                                        request()->is('campaigns*') ||
                                        request()->is('adset_statistics*') ||
                                        request()->is('budgets*') ||
                                        request()->is('campaign_statistics*')) show @endif"
                                    data-menu-item-toggle="accordion" data-menu-item-trigger="click">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <span
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            UTM
                                        </span>
                                        <span class="menu-arrow text-gray-400 w-[20px] shrink-0 justify-end ms-1 me-[-10px]">
                                            <i class="ki-filled ki-plus text-2xs menu-item-show:hidden">
                                            </i>
                                            <i class="ki-filled ki-minus text-2xs hidden menu-item-show:inline-flex">
                                            </i>
                                        </span>
                                    </div>
                                    <div
                                        class="menu-accordion gap-0.5 ps-[10px] relative before:absolute before:start-[20px] before:top-0 before:bottom-0 before:border-s before:border-gray-200">
                                        <div class="menu-item @if (request()->is('adsets*') || request()->is('campaigns*') || request()->is('budgets*')) active @endif">
                                            <a class="menu-link border border-transparent items-center grow menu-item-active:bg-secondary-active dark:menu-item-active:bg-coal-300 dark:menu-item-active:border-gray-100 menu-item-active:rounded-lg hover:bg-secondary-active dark:hover:bg-coal-300 dark:hover:border-gray-100 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                                href="{{ route('adsets.index') }}" tabindex="0">
                                                <span
                                                    class="menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 menu-item-active:before:bg-primary menu-item-hover:before:bg-primary">
                                                </span>
                                                <span
                                                    class="menu-title text-2sm font-normal text-gray-800 menu-item-active:text-primary menu-item-active:font-semibold menu-link-hover:!text-primary">
                                                    الحملات الإعلانية
                                                </span>
                                            </a>
                                        </div>
                                        <div class="menu-item @if (request()->is('adset_statistics*') || request()->is('campaign_statistics*')) active @endif">
                                            <a class="menu-link border border-transparent items-center grow menu-item-active:bg-secondary-active dark:menu-item-active:bg-coal-300 dark:menu-item-active:border-gray-100 menu-item-active:rounded-lg hover:bg-secondary-active dark:hover:bg-coal-300 dark:hover:border-gray-100 hover:rounded-lg gap-[14px] ps-[10px] pe-[10px] py-[8px]"
                                                href="{{ route('adsets.statistics') }}" tabindex="0">
                                                <span
                                                    class="menu-bullet flex w-[6px] -start-[3px] rtl:start-0 relative before:absolute before:top-0 before:size-[6px] before:rounded-full rtl:before:translate-x-1/2 before:-translate-y-1/2 menu-item-active:before:bg-primary menu-item-hover:before:bg-primary">
                                                </span>
                                                <span
                                                    class="menu-title text-2sm font-normal text-gray-800 menu-item-active:text-primary menu-item-active:font-semibold menu-link-hover:!text-primary">
                                                    التقرير العام
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                            @can('access-shipping-companies')
                                <div class="menu-item @if (request()->is('shipping_companies*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('shipping_companies.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            شركات الشحن
                                        </a>
                                    </div>
                                </div>
                            @endcan
                            @can('viewany', App\Models\User::class)
                                <div class="menu-item @if (request()->is('admins*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('admins.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            المدراء
                                        </a>
                                    </div>
                                </div>
                            @endcan
                            @can('access-websites')
                                <div class="menu-item @if (request()->is('websites*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('websites.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            الدومينات
                                        </a>
                                    </div>
                                </div>
                            @endcan

                            @can('access-pages')
                                <div class="menu-item @if (request()->is('pages*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('pages.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            الصفحات
                                        </a>
                                    </div>
                                </div>
                            @endcan

                            @can('access-domains')
                                <div class="menu-item @if (request()->is('domains*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('domains.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            دومينات الصفحات
                                        </a>
                                    </div>
                                </div>
                            @endcan

                            @can('access-pages')
                                <div class="menu-item @if (request()->is('pixels*')) active @endif">
                                    <div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] ps-[10px] pe-[10px] py-[6px]"
                                        tabindex="0">
                                        <a href="{{ route('pixels.index') }}"
                                            class="menu-title text-sm font-medium text-gray-800 menu-item-active:text-primary menu-link-hover:!text-primary">
                                            البكسلات
                                        </a>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <!-- End of Sidebar Menu -->
                    </div>
                </div>
            </div>
            <!-- End of Sidebar -->
            <!-- Wrapper -->
            <div class="wrapper flex grow flex-col">
                <div class="container-fixed">
                    <header
                        class="header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-[--tw-page-bg] dark:bg-[--tw-page-bg-dark]"
                        data-sticky="true" data-sticky-class="shadow-sm" data-sticky-name="header" id="header">
                        <!-- Container -->
                        <div class="container-fixed flex justify-between items-stretch lg:gap-4" id="header_container">
                            <!-- Mobile Logo -->
                            <div class="flex gap-1 lg:hidden items-center -ms-1">
                                <div class="flex items-center">
                                    <button class="btn btn-icon btn-light btn-clear btn-sm" data-drawer-toggle="#sidebar">
                                        <i class="ki-filled ki-menu">
                                        </i>
                                    </button>
                                </div>
                            </div>
                            <!-- End of Mobile Logo -->
                            <!-- Breadcrumbs -->
                            <div class="flex [.header_&]:below-lg:hidden items-center gap-1.25 text-xs lg:text-sm font-medium mb-2.5 lg:mb-0"
                                data-reparent="true" data-reparent-mode="prepend|lg:prepend"
                                data-reparent-target="#content_container|lg:#header_container">
                                @yield('url_pages')
                            </div>

                            <!-- End of Breadcrumbs -->
                            <!-- Topbar -->
                            <div class="flex items-center gap-2 lg:gap-3.5">
                                <div class="dropdown" data-dropdown="true" data-dropdown-offset="70px, 10px"
                                    data-dropdown-offset-rtl="-70px, 10px" data-dropdown-placement="bottom-end"
                                    data-dropdown-placement-rtl="bottom-start" data-dropdown-trigger="click|lg:click">
                                    <button
                                        class="dropdown-toggle btn btn-icon btn-icon-lg relative cursor-pointer size-9 rounded-full hover:bg-primary-light hover:text-primary dropdown-open:bg-primary-light dropdown-open:text-primary text-gray-500">
                                        {{ auth()->user()->name }}
                                    </button>
                                    <div class="dropdown-content light:border-gray-300 w-full max-w-[200px] p-2.5 pb-0">
                                        <div class="flex flex-col h-fit">
                                            <form class="grid grid-cols-12" id="notifications_all_footer" method="POST"
                                                action="{{ route('logout') }}">
                                                @csrf
                                                <button class="btn btn-sm btn-light justify-center">
                                                    تسجيل خروج
                                                    <i class="ki-filled ki-exit-right"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of Topbar -->
                        </div>
                        <!-- End of Container -->
                    </header>
                    <main class="grow content pt-5 pb-5" id="content" role="content">
                        @yield('content')
                    </main>
                </div>
            </div>
            <!-- End of Wrapper -->
        </div>
    @else
        @yield('login_form')
    @endauth
    <!-- End of Page -->
    <!-- Scripts -->
    <script src="{{ asset('adminTemplate') }}/js/core.bundle.js"></script>
    <script src="{{ asset('adminTemplate') }}/vendors/apexcharts/apexcharts.min.js"></script>
    <script src="{{ asset('adminTemplate') }}/js/widgets/general.js"></script>
    <script src="{{ asset('adminTemplate') }}/js/layouts/demo1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('script')
    <!-- End of Scripts -->
</body>

</html>
@toastifyJs

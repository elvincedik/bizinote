<template>
    <div
        class="side-content-wrap"
        @mouseenter="isMenuOver = true"
        @mouseleave="isMenuOver = false"
        @touchstart="isMenuOver = true"
    >
        <vue-perfect-scrollbar
            :settings="{ suppressScrollX: true, wheelPropagation: false }"
            :class="{ open: getSideBarToggleProperties.isSideNavOpen }"
            ref="myData"
            class="sidebar-left rtl-ps-none ps scroll"
        >
            <div class="">
                <ul class="navigation-left w-100">
                    <li
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'dashboard' }"
                        class="nav-item border-0"
                        data-item="dashboard"
                    >
                        <router-link
                            tag="a"
                            class="customize-nav-item-hold"
                            to="/app/dashboard"
                        >
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M10 14V8.66667C10 8.48986 9.92976 8.32029 9.80474 8.19526C9.67971 8.07024 9.51014 8 9.33333 8H6.66667C6.48986 8 6.32029 8.07024 6.19526 8.19526C6.07024 8.32029 6 8.48986 6 8.66667V14"
                                    stroke="#2A008C"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M2 6.66697C1.99995 6.47302 2.04222 6.28139 2.12386 6.10545C2.20549 5.92951 2.32453 5.7735 2.47267 5.6483L7.13933 1.64897C7.37999 1.44558 7.6849 1.33398 8 1.33398C8.3151 1.33398 8.62001 1.44558 8.86067 1.64897L13.5273 5.6483C13.6755 5.7735 13.7945 5.92951 13.8761 6.10545C13.9578 6.28139 14 6.47302 14 6.66697V12.667C14 13.0206 13.8595 13.3597 13.6095 13.6098C13.3594 13.8598 13.0203 14.0003 12.6667 14.0003H3.33333C2.97971 14.0003 2.64057 13.8598 2.39052 13.6098C2.14048 13.3597 2 13.0206 2 12.667V6.66697Z"
                                    stroke="#2A008C"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("dashboard") }}</span>
                        </router-link>
                    </li>
                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes('products_add') ||
                                currentUserPermissions.includes(
                                    'products_view'
                                ) ||
                                currentUserPermissions.includes(
                                    'opening_stock_import'
                                ) ||
                                currentUserPermissions.includes(
                                    'barcode_view'
                                ) ||
                                currentUserPermissions.includes('brand') ||
                                currentUserPermissions.includes('unit') ||
                                currentUserPermissions.includes(
                                    'count_stock'
                                ) ||
                                currentUserPermissions.includes('category'))
                        "
                        @mouseenter="toggleSubMenu"
                        class="nav-item border-0"
                        :class="{ active: selectedParentMenu == 'products' }"
                        data-item="products"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12.6667 2H3.33333C2.59695 2 2 2.59695 2 3.33333V12.6667C2 13.403 2.59695 14 3.33333 14H12.6667C13.403 14 14 13.403 14 12.6667V3.33333C14 2.59695 13.403 2 12.6667 2Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M6 5.33301H10.6667"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M5.33333 8H9.33333"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M7.33333 10.667H10.6667"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("Products") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes(
                                'adjustment_view'
                            ) ||
                                currentUserPermissions.includes(
                                    'adjustment_add'
                                ))
                        "
                        @mouseenter="toggleSubMenu"
                        class="nav-item border-0"
                        :class="{ active: selectedParentMenu == 'adjustments' }"
                        data-item="adjustments"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="17"
                                viewBox="0 0 16 17"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M7 14.5H3.33333C2.97971 14.5 2.64057 14.3595 2.39052 14.1095C2.14048 13.8594 2 13.5203 2 13.1667V3.83333C2 3.47971 2.14048 3.14057 2.39052 2.89052C2.64057 2.64048 2.97971 2.5 3.33333 2.5H12.6667C13.0203 2.5 13.3594 2.64048 13.6095 2.89052C13.8595 3.14057 14 3.47971 14 3.83333V7.5"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M9.53333 13.5665L10.2 13.2998"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M10 2.5V7.5"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M10.1333 11.7664L9.53333 11.5664"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M11.0667 14.9662L11.2667 14.3662"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M11.2 10.6999L10.9333 10.0332"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M12.7333 10.6332L12.9333 10.0332"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M13.0667 14.9665L12.8 14.2998"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M13.8 11.7003L14.4667 11.4336"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M14.4667 13.4334L13.8667 13.2334"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M6 2.5V14.5"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M12 14.5C13.1046 14.5 14 13.6046 14 12.5C14 11.3954 13.1046 10.5 12 10.5C10.8954 10.5 10 11.3954 10 12.5C10 13.6046 10.8954 14.5 12 14.5Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{
                                $t("StockAdjustement")
                            }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes(
                                'Quotations_view'
                            ) ||
                                currentUserPermissions.includes(
                                    'Quotations_add'
                                ))
                        "
                        @mouseenter="toggleSubMenu"
                        class="nav-item border-0"
                        :class="{ active: selectedParentMenu == 'quotations' }"
                        data-item="quotations"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g clip-path="url(#clip0_280_173)">
                                    <path
                                        d="M8 14.6663C11.6819 14.6663 14.6667 11.6816 14.6667 7.99967C14.6667 4.31778 11.6819 1.33301 8 1.33301C4.3181 1.33301 1.33333 4.31778 1.33333 7.99967C1.33333 11.6816 4.3181 14.6663 8 14.6663Z"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M8 12C10.2091 12 12 10.2091 12 8C12 5.79086 10.2091 4 8 4C5.79086 4 4 5.79086 4 8C4 10.2091 5.79086 12 8 12Z"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M8 9.33366C8.73638 9.33366 9.33333 8.73671 9.33333 8.00033C9.33333 7.26395 8.73638 6.66699 8 6.66699C7.26362 6.66699 6.66667 7.26395 6.66667 8.00033C6.66667 8.73671 7.26362 9.33366 8 9.33366Z"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </g>
                                <defs>
                                    <clipPath id="clip0_280_173">
                                        <rect
                                            width="16"
                                            height="16"
                                            fill="white"
                                        />
                                    </clipPath>
                                </defs>
                            </svg>

                            <span class="nav-text">{{ $t("Quotations") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes(
                                'Purchases_view'
                            ) ||
                                currentUserPermissions.includes(
                                    'Purchases_add'
                                ))
                        "
                        @mouseenter="toggleSubMenu"
                        class="nav-item border-0"
                        :class="{ active: selectedParentMenu == 'purchases' }"
                        data-item="purchases"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M10.6667 6.66699C10.6667 7.37424 10.3857 8.05251 9.88562 8.55261C9.38552 9.05271 8.70724 9.33366 8 9.33366C7.29275 9.33366 6.61448 9.05271 6.11438 8.55261C5.61428 8.05251 5.33333 7.37424 5.33333 6.66699"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M2.06866 4.02246H13.9313"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M2.26667 3.64434C2.09357 3.87514 2 4.15585 2 4.44434V13.333C2 13.6866 2.14048 14.0258 2.39052 14.2758C2.64057 14.5259 2.97971 14.6663 3.33333 14.6663H12.6667C13.0203 14.6663 13.3594 14.5259 13.6095 14.2758C13.8595 14.0258 14 13.6866 14 13.333V4.44434C14 4.15585 13.9064 3.87514 13.7333 3.64434L12.4 1.86634C12.2758 1.70075 12.1148 1.56634 11.9296 1.47377C11.7445 1.3812 11.5403 1.33301 11.3333 1.33301H4.66667C4.45967 1.33301 4.25552 1.3812 4.07038 1.47377C3.88524 1.56634 3.7242 1.70075 3.6 1.86634L2.26667 3.64434Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("Purchases") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes('Sales_view') ||
                                currentUserPermissions.includes('Sales_add') ||
                                currentUserPermissions.includes('Pos_view') ||
                                currentUserPermissions.includes('shipment'))
                        "
                        class="nav-item border-0"
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'sales' }"
                        data-item="sales"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M2.66667 12.9997V2.99967C2.66667 2.55765 2.84226 2.13372 3.15482 1.82116C3.46738 1.5086 3.89131 1.33301 4.33333 1.33301H12.6667C12.8435 1.33301 13.013 1.40325 13.1381 1.52827C13.2631 1.65329 13.3333 1.82286 13.3333 1.99967V13.9997C13.3333 14.1765 13.2631 14.3461 13.1381 14.4711C13.013 14.5961 12.8435 14.6663 12.6667 14.6663H4.33333C3.89131 14.6663 3.46738 14.4907 3.15482 14.1782C2.84226 13.8656 2.66667 13.4417 2.66667 12.9997ZM2.66667 12.9997C2.66667 12.5576 2.84226 12.1337 3.15482 11.8212C3.46738 11.5086 3.89131 11.333 4.33333 11.333H13.3333"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M6 6.33333L7.33333 7.66667L10 5"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("Sales") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>

                    <li
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Sale_Returns_view')
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'sale_return' }"
                        class="nav-item border-0"
                        data-item="sale_return"
                    >
                        <router-link
                            tag="a"
                            class="customize-nav-item-hold"
                            to="/app/sale_return/list"
                        >
                            <i class="nav-icon i-Right"></i>
                            <span class="nav-text">{{
                                $t("SalesReturn")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'Purchase_Returns_view'
                            )
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{
                            active: selectedParentMenu == 'purchase_return',
                        }"
                        class="nav-item border-0"
                        data-item="purchase_return"
                    >
                        <router-link
                            tag="a"
                            class="customize-nav-item-hold"
                            to="/app/purchase_return/list"
                        >
                            <i class="nav-icon i-Left"></i>
                            <span class="nav-text">{{
                                $t("PurchasesReturn")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes('company') ||
                                currentUserPermissions.includes('department') ||
                                currentUserPermissions.includes(
                                    'designation'
                                ) ||
                                currentUserPermissions.includes(
                                    'office_shift'
                                ) ||
                                currentUserPermissions.includes(
                                    'view_employee'
                                ) ||
                                currentUserPermissions.includes('attendance') ||
                                currentUserPermissions.includes('leave') ||
                                currentUserPermissions.includes('holiday') ||
                                currentUserPermissions.includes('payroll'))
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'hrm' }"
                        class="nav-item border-0"
                        data-item="hrm"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g clip-path="url(#clip0_280_121)">
                                    <path
                                        d="M4 14.6663V2.66634C4 2.31272 4.14048 1.97358 4.39052 1.72353C4.64057 1.47348 4.97971 1.33301 5.33333 1.33301H10.6667C11.0203 1.33301 11.3594 1.47348 11.6095 1.72353C11.8595 1.97358 12 2.31272 12 2.66634V14.6663H4Z"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M4 8H2.66667C2.31304 8 1.9739 8.14048 1.72386 8.39052C1.47381 8.64057 1.33333 8.97971 1.33333 9.33333V13.3333C1.33333 13.687 1.47381 14.0261 1.72386 14.2761C1.9739 14.5262 2.31304 14.6667 2.66667 14.6667H4"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M12 6H13.3333C13.687 6 14.0261 6.14048 14.2761 6.39052C14.5262 6.64057 14.6667 6.97971 14.6667 7.33333V13.3333C14.6667 13.687 14.5262 14.0261 14.2761 14.2761C14.0261 14.5262 13.687 14.6667 13.3333 14.6667H12"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M6.66667 4H9.33333"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M6.66667 6.66699H9.33333"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M6.66667 9.33301H9.33333"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M6.66667 12H9.33333"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </g>
                                <defs>
                                    <clipPath id="clip0_280_121">
                                        <rect
                                            width="16"
                                            height="16"
                                            fill="white"
                                        />
                                    </clipPath>
                                </defs>
                            </svg>

                            <span class="nav-text">{{ $t("hrm") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes('transfer_view') ||
                                currentUserPermissions.includes('transfer_add'))
                        "
                        @mouseenter="toggleSubMenu"
                        class="nav-item border-0"
                        :class="{ active: selectedParentMenu == 'transfers' }"
                        data-item="transfers"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g clip-path="url(#clip0_280_131)">
                                    <path
                                        d="M10.9773 4.99967C10.4287 2.82434 9.30133 1.33301 8 1.33301C6.15933 1.33301 4.66667 4.31767 4.66667 7.99967C4.66667 11.6817 6.15933 14.6663 8 14.6663C8.228 14.6663 8.45133 14.6203 8.66667 14.533"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M10.1293 9.1377L12.672 10.3777L11.432 12.9204"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M12.6667 10.3803C11.464 10.9703 9.81733 11.3337 8 11.3337C4.318 11.3337 1.33333 9.84099 1.33333 8.00033C1.33333 6.15966 4.318 4.66699 8 4.66699C11.2253 4.66699 13.9153 5.81233 14.5333 7.33366"
                                        stroke="#333333"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </g>
                                <defs>
                                    <clipPath id="clip0_280_131">
                                        <rect
                                            width="16"
                                            height="16"
                                            fill="white"
                                        />
                                    </clipPath>
                                </defs>
                            </svg>

                            <span class="nav-text">{{
                                $t("StockTransfers")
                            }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes('expense_view') ||
                                currentUserPermissions.includes(
                                    'expense_add'
                                ) ||
                                currentUserPermissions.includes(
                                    'deposit_view'
                                ) ||
                                currentUserPermissions.includes(
                                    'deposit_add'
                                ) ||
                                currentUserPermissions.includes('account') ||
                                currentUserPermissions.includes(
                                    'transfer_money'
                                ))
                        "
                        @mouseenter="toggleSubMenu"
                        class="nav-item border-0"
                        :class="{ active: selectedParentMenu == 'accounting' }"
                        data-item="accounting"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="17"
                                viewBox="0 0 16 17"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12.6667 5.16667V3.16667C12.6667 2.98986 12.5964 2.82029 12.4714 2.69526C12.3464 2.57024 12.1768 2.5 12 2.5H3.33333C2.97971 2.5 2.64057 2.64048 2.39052 2.89052C2.14048 3.14057 2 3.47971 2 3.83333C2 4.18696 2.14048 4.52609 2.39052 4.77614C2.64057 5.02619 2.97971 5.16667 3.33333 5.16667H13.3333C13.5101 5.16667 13.6797 5.2369 13.8047 5.36193C13.9298 5.48695 14 5.65652 14 5.83333V8.5M14 8.5H12C11.6464 8.5 11.3072 8.64048 11.0572 8.89052C10.8071 9.14057 10.6667 9.47971 10.6667 9.83333C10.6667 10.187 10.8071 10.5261 11.0572 10.7761C11.3072 11.0262 11.6464 11.1667 12 11.1667H14C14.1768 11.1667 14.3464 11.0964 14.4714 10.9714C14.5964 10.8464 14.6667 10.6768 14.6667 10.5V9.16667C14.6667 8.98986 14.5964 8.82029 14.4714 8.69526C14.3464 8.57024 14.1768 8.5 14 8.5Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M2 3.83301V13.1663C2 13.52 2.14048 13.8591 2.39052 14.1092C2.64057 14.3592 2.97971 14.4997 3.33333 14.4997H13.3333C13.5101 14.4997 13.6797 14.4294 13.8047 14.3044C13.9298 14.1794 14 14.0098 14 13.833V11.1663"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("Accounting") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>

                    <li
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'subscription_product'
                            )
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{
                            active:
                                selectedParentMenu == 'subscription_product',
                        }"
                        class="nav-item border-0"
                        data-item="subscription_product"
                    >
                        <router-link
                            tag="a"
                            class="customize-nav-item-hold"
                            to="/app/subscription_product/list"
                        >
                            <svg
                                width="14"
                                height="13"
                                viewBox="0 0 14 13"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M1 4.5C1 4.14638 1.14048 3.80724 1.39052 3.55719C1.64057 3.30714 1.97971 3.16667 2.33333 3.16667H11.6667C12.0203 3.16667 12.3594 3.30714 12.6095 3.55719C12.8595 3.80724 13 4.14638 13 4.5M1 5.83333H3C3.53333 5.83333 4.06667 6.03333 4.4 6.43333L5.13333 7.03333C6.2 8.1 7.86667 8.1 8.93333 7.03333L9.66667 6.43333C10 6.1 10.5333 5.83333 11.0667 5.83333H13M2.33333 0.5H11.6667C12.403 0.5 13 1.09695 13 1.83333V11.1667C13 11.903 12.403 12.5 11.6667 12.5H2.33333C1.59695 12.5 1 11.903 1 11.1667V1.83333C1 1.09695 1.59695 0.5 2.33333 0.5Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{
                                $t("Subscription_Product")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes(
                                'Customers_view'
                            ) ||
                                currentUserPermissions.includes(
                                    'Suppliers_view'
                                ) ||
                                currentUserPermissions.includes('users_view'))
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'People' }"
                        class="nav-item border-0"
                        data-item="People"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12 14.0003C12 12.5858 11.4381 11.2293 10.4379 10.2291C9.43771 9.2289 8.08115 8.66699 6.66667 8.66699C5.25218 8.66699 3.89562 9.2289 2.89543 10.2291C1.89524 11.2293 1.33333 12.5858 1.33333 14.0003"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M6.66667 8.66667C8.50761 8.66667 10 7.17428 10 5.33333C10 3.49238 8.50761 2 6.66667 2C4.82572 2 3.33333 3.49238 3.33333 5.33333C3.33333 7.17428 4.82572 8.66667 6.66667 8.66667Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M14.6667 13.3335C14.6667 11.0868 13.3333 9.00013 12 8.00013C12.4383 7.67131 12.7887 7.23952 13.0204 6.74298C13.252 6.24643 13.3577 5.70044 13.328 5.15333C13.2984 4.60622 13.1343 4.07485 12.8503 3.60626C12.5664 3.13767 12.1713 2.7463 11.7 2.4668"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("People") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            currentUserPermissions.includes('projects')
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'projects' }"
                        class="nav-item border-0"
                        data-item="projects"
                    >
                        <router-link
                            tag="a"
                            class="customize-nav-item-hold"
                            to="/app/projects"
                        >
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M13.3333 11.3337C13.687 11.3337 14.0261 11.1932 14.2761 10.9431C14.5262 10.6931 14.6667 10.3539 14.6667 10.0003V6.00033C14.6667 5.6467 14.5262 5.30756 14.2761 5.05752C14.0261 4.80747 13.687 4.66699 13.3333 4.66699H10.7333C10.5103 4.66918 10.2904 4.6154 10.0935 4.51058C9.8967 4.40576 9.72931 4.25324 9.60667 4.06699L9.06667 3.26699C8.94526 3.08264 8.77998 2.93131 8.58567 2.82659C8.39135 2.72187 8.17407 2.66703 7.95333 2.66699H5.33333C4.97971 2.66699 4.64057 2.80747 4.39052 3.05752C4.14048 3.30756 4 3.6467 4 4.00033V10.0003C4 10.3539 4.14048 10.6931 4.39052 10.9431C4.64057 11.1932 4.97971 11.3337 5.33333 11.3337H13.3333Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M1.33333 5.33301V12.6663C1.33333 13.02 1.47381 13.3591 1.72386 13.6092C1.9739 13.8592 2.31304 13.9997 2.66667 13.9997H12"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("Projects") }}</span>
                        </router-link>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            currentUserPermissions.includes('tasks')
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'tasks' }"
                        class="nav-item border-0"
                        data-item="tasks"
                    >
                        <router-link
                            tag="a"
                            class="customize-nav-item-hold"
                            to="/app/tasks"
                        >
                            <i class="nav-icon i-Check"></i>
                            <span class="nav-text">{{ $t("Tasks") }}</span>
                        </router-link>
                    </li>

                    <!-- //import component Modules -->

                    <li
                        v-for="ComponentModule in getallmodules"
                        v-show="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                ComponentModule.permission
                            )
                        "
                        @mouseenter="toggleSubMenu"
                        class="nav-item"
                    >
                        <a
                            class="nav-item-hold nav-item-module"
                            target="_blank"
                            :href="ComponentModule.url"
                        >
                            <i :class="ComponentModule.icon"></i>
                            <span class="nav-text">{{
                                ComponentModule.name
                            }}</span>
                        </a>
                        <div class="addon_triangle">
                            <span>Addon</span>
                        </div>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes(
                                'setting_system'
                            ) ||
                                currentUserPermissions.includes(
                                    'sms_settings'
                                ) ||
                                currentUserPermissions.includes(
                                    'notification_template'
                                ) ||
                                currentUserPermissions.includes(
                                    'pos_settings'
                                ) ||
                                currentUserPermissions.includes(
                                    'appearance_settings'
                                ) ||
                                currentUserPermissions.includes(
                                    'translations_settings'
                                ) ||
                                currentUserPermissions.includes(
                                    'module_settings'
                                ) ||
                                currentUserPermissions.includes(
                                    'payment_gateway'
                                ) ||
                                currentUserPermissions.includes(
                                    'mail_settings'
                                ) ||
                                currentUserPermissions.includes('warehouse') ||
                                currentUserPermissions.includes('backup') ||
                                currentUserPermissions.includes(
                                    'payment_methods'
                                ) ||
                                currentUserPermissions.includes('currency') ||
                                currentUserPermissions.includes(
                                    'permissions_view'
                                ))
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'settings' }"
                        class="nav-item border-0"
                        data-item="settings"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <svg
                                width="16"
                                height="17"
                                viewBox="0 0 16 17"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M8.14667 1.83301H7.85333C7.49971 1.83301 7.16057 1.97348 6.91053 2.22353C6.66048 2.47358 6.52 2.81272 6.52 3.16634V3.28634C6.51976 3.52016 6.45804 3.7498 6.34103 3.95223C6.22401 4.15466 6.05583 4.32277 5.85333 4.43967L5.56667 4.60634C5.36398 4.72337 5.13405 4.78497 4.9 4.78497C4.66595 4.78497 4.43603 4.72337 4.23333 4.60634L4.13333 4.55301C3.82738 4.37652 3.46389 4.32864 3.12267 4.41988C2.78145 4.51112 2.49037 4.73403 2.31333 5.03967L2.16667 5.29301C1.99018 5.59896 1.9423 5.96245 2.03354 6.30367C2.12478 6.6449 2.34769 6.93597 2.65333 7.11301L2.75333 7.17967C2.95485 7.29602 3.12241 7.46307 3.23937 7.66423C3.35632 7.86539 3.4186 8.09366 3.42 8.32634V8.66634C3.42093 8.90129 3.35977 9.13231 3.2427 9.33601C3.12563 9.53972 2.95681 9.70887 2.75333 9.82634L2.65333 9.88634C2.34769 10.0634 2.12478 10.3545 2.03354 10.6957C1.9423 11.0369 1.99018 11.4004 2.16667 11.7063L2.31333 11.9597C2.49037 12.2653 2.78145 12.4882 3.12267 12.5795C3.46389 12.6707 3.82738 12.6228 4.13333 12.4463L4.23333 12.393C4.43603 12.276 4.66595 12.2144 4.9 12.2144C5.13405 12.2144 5.36398 12.276 5.56667 12.393L5.85333 12.5597C6.05583 12.6766 6.22401 12.8447 6.34103 13.0471C6.45804 13.2495 6.51976 13.4792 6.52 13.713V13.833C6.52 14.1866 6.66048 14.5258 6.91053 14.7758C7.16057 15.0259 7.49971 15.1663 7.85333 15.1663H8.14667C8.50029 15.1663 8.83943 15.0259 9.08948 14.7758C9.33953 14.5258 9.48 14.1866 9.48 13.833V13.713C9.48024 13.4792 9.54196 13.2495 9.65898 13.0471C9.77599 12.8447 9.94418 12.6766 10.1467 12.5597L10.4333 12.393C10.636 12.276 10.866 12.2144 11.1 12.2144C11.3341 12.2144 11.564 12.276 11.7667 12.393L11.8667 12.4463C12.1726 12.6228 12.5361 12.6707 12.8773 12.5795C13.2186 12.4882 13.5096 12.2653 13.6867 11.9597L13.8333 11.6997C14.0098 11.3937 14.0577 11.0302 13.9665 10.689C13.8752 10.3478 13.6523 10.0567 13.3467 9.87967L13.2467 9.82634C13.0432 9.70887 12.8744 9.53972 12.7573 9.33601C12.6402 9.13231 12.5791 8.90129 12.58 8.66634V8.33301C12.5791 8.09806 12.6402 7.86704 12.7573 7.66334C12.8744 7.45963 13.0432 7.29048 13.2467 7.17301L13.3467 7.11301C13.6523 6.93597 13.8752 6.6449 13.9665 6.30367C14.0577 5.96245 14.0098 5.59896 13.8333 5.29301L13.6867 5.03967C13.5096 4.73403 13.2186 4.51112 12.8773 4.41988C12.5361 4.32864 12.1726 4.37652 11.8667 4.55301L11.7667 4.60634C11.564 4.72337 11.3341 4.78497 11.1 4.78497C10.866 4.78497 10.636 4.72337 10.4333 4.60634L10.1467 4.43967C9.94418 4.32277 9.77599 4.15466 9.65898 3.95223C9.54196 3.7498 9.48024 3.52016 9.48 3.28634V3.16634C9.48 2.81272 9.33953 2.47358 9.08948 2.22353C8.83943 1.97348 8.50029 1.83301 8.14667 1.83301Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M8 10.5C9.10457 10.5 10 9.60457 10 8.5C10 7.39543 9.10457 6.5 8 6.5C6.89543 6.5 6 7.39543 6 8.5C6 9.60457 6.89543 10.5 8 10.5Z"
                                    stroke="#333333"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>

                            <span class="nav-text">{{ $t("Settings") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>

                    <li
                        v-show="
                            currentUserPermissions &&
                            (currentUserPermissions.includes(
                                'Reports_payments_Sales'
                            ) ||
                                currentUserPermissions.includes(
                                    'Reports_payments_Purchases'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_payments_Sale_Returns'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_payments_purchase_Return'
                                ) ||
                                currentUserPermissions.includes(
                                    'Warehouse_report'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_profit'
                                ) ||
                                currentUserPermissions.includes(
                                    'inventory_valuation'
                                ) ||
                                currentUserPermissions.includes(
                                    'expenses_report'
                                ) ||
                                currentUserPermissions.includes(
                                    'deposits_report'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_purchase'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_quantity_alerts'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_sales'
                                ) ||
                                currentUserPermissions.includes(
                                    'product_sales_report'
                                ) ||
                                currentUserPermissions.includes(
                                    'product_purchases_report'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_suppliers'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_customers'
                                ) ||
                                currentUserPermissions.includes(
                                    'Top_products'
                                ) ||
                                currentUserPermissions.includes(
                                    'Top_customers'
                                ) ||
                                currentUserPermissions.includes(
                                    'users_report'
                                ) ||
                                currentUserPermissions.includes(
                                    'product_report'
                                ) ||
                                currentUserPermissions.includes(
                                    'report_transactions'
                                ) ||
                                currentUserPermissions.includes(
                                    'report_sales_by_category'
                                ) ||
                                currentUserPermissions.includes(
                                    'report_sales_by_brand'
                                ) ||
                                currentUserPermissions.includes(
                                    'report_error_logs'
                                ) ||
                                currentUserPermissions.includes('stock_report'))
                        "
                        @mouseenter="toggleSubMenu"
                        :class="{ active: selectedParentMenu == 'reports' }"
                        class="nav-item border-0"
                        data-item="reports"
                        :data-submenu="true"
                    >
                        <a class="customize-nav-item-hold" href="#">
                            <i class="nav-icon i-Line-Chart"></i>
                            <span class="nav-text">{{ $t("Reports") }}</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                </ul>
            </div>
        </vue-perfect-scrollbar>

        <vue-perfect-scrollbar
            :class="{ open: getSideBarToggleProperties.isSecondarySideNavOpen }"
            :settings="{ suppressScrollX: true, wheelPropagation: false }"
            class="sidebar-left-secondary ps rtl-ps-none"
        >
            <div ref="sidebarChild">
                <ul
                    class="childNav d-none"
                    data-parent="products"
                    :class="{ 'd-block': selectedParentMenu == 'products' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('products_add')
                        "
                    >
                        <router-link tag="a" class to="/app/products/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("AddProduct")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('products_view')
                        "
                    >
                        <router-link tag="a" class to="/app/products/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("productsList")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'opening_stock_import'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/products/opening_stock_import"
                        >
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("Opening_Stock")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('barcode_view')
                        "
                    >
                        <router-link tag="a" class to="/app/products/barcode">
                            <i class="nav-icon i-Bar-Code"></i>
                            <span class="item-name">{{
                                $t("Printbarcode")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('count_stock')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/products/count_stock"
                        >
                            <i class="nav-icon i-Check-2"></i>
                            <span class="item-name">{{
                                $t("CountStock")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('category')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/products/Categories"
                        >
                            <i class="nav-icon i-Duplicate-Layer"></i>
                            <span class="item-name">{{
                                $t("Categories")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('brand')
                        "
                    >
                        <router-link tag="a" class to="/app/products/Brands">
                            <i class="nav-icon i-Bookmark"></i>
                            <span class="item-name">{{ $t("Brand") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('unit')
                        "
                    >
                        <router-link tag="a" class to="/app/products/Units">
                            <i class="nav-icon i-Quotes"></i>
                            <span class="item-name">{{ $t("Units") }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="adjustments"
                    :class="{ 'd-block': selectedParentMenu == 'adjustments' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('adjustment_add')
                        "
                    >
                        <router-link tag="a" class to="/app/adjustments/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("CreateAdjustment")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('adjustment_view')
                        "
                    >
                        <router-link tag="a" class to="/app/adjustments/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("ListAdjustments")
                            }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="transfers"
                    :class="{ 'd-block': selectedParentMenu == 'transfers' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('transfer_add')
                        "
                    >
                        <router-link tag="a" class to="/app/transfers/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("CreateTransfer")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('transfer_view')
                        "
                    >
                        <router-link tag="a" class to="/app/transfers/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("ListTransfers")
                            }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="accounting"
                    :class="{ 'd-block': selectedParentMenu == 'accounting' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('account')
                        "
                    >
                        <router-link tag="a" class to="/app/accounts">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("List_accounts")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('transfer_money')
                        "
                    >
                        <router-link tag="a" class to="/app/transfer_money">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("Transfers_Money")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('expense_add')
                        "
                    >
                        <router-link tag="a" class to="/app/expenses/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("Create_Expense")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('expense_view')
                        "
                    >
                        <router-link tag="a" class to="/app/expenses/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("ListExpenses")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('deposit_add')
                        "
                    >
                        <router-link tag="a" class to="/app/deposits/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("Create_deposit")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('deposit_view')
                        "
                    >
                        <router-link tag="a" class to="/app/deposits/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("List_Deposit")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('expense_view')
                        "
                    >
                        <router-link tag="a" class to="/app/expenses/category">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("Expense_Category")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('deposit_view')
                        "
                    >
                        <router-link tag="a" class to="/app/deposits/category">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("deposit_Category")
                            }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="quotations"
                    :class="{ 'd-block': selectedParentMenu == 'quotations' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Quotations_add')
                        "
                    >
                        <router-link tag="a" class to="/app/quotations/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{ $t("AddQuote") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Quotations_view')
                        "
                    >
                        <router-link tag="a" class to="/app/quotations/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("ListQuotations")
                            }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="purchases"
                    :class="{ 'd-block': selectedParentMenu == 'purchases' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Purchases_add')
                        "
                    >
                        <router-link tag="a" class to="/app/purchases/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("AddPurchase")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Purchases_view')
                        "
                    >
                        <router-link tag="a" class to="/app/purchases/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{
                                $t("ListPurchases")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Purchases_add')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/purchases/import_purchases"
                        >
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{
                                $t("Import_Purchases")
                            }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="sales"
                    :class="{ 'd-block': selectedParentMenu == 'sales' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Sales_add')
                        "
                    >
                        <router-link tag="a" class to="/app/sales/store">
                            <i class="nav-icon i-Add-File"></i>
                            <span class="item-name">{{ $t("AddSale") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Sales_view')
                        "
                    >
                        <router-link tag="a" class to="/app/sales/list">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{ $t("ListSales") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Pos_view')
                        "
                    >
                        <router-link tag="a" class to="/app/pos">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">POS</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('shipment')
                        "
                    >
                        <router-link tag="a" class to="/app/sales/shipment">
                            <i class="nav-icon i-Files"></i>
                            <span class="item-name">{{ $t("Shipments") }}</span>
                        </router-link>
                    </li>
                </ul>

                <!-- hrm -->
                <ul
                    class="childNav d-none"
                    data-parent="hrm"
                    :class="{ 'd-block': selectedParentMenu == 'hrm' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('company')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/company">
                            <i class="nav-icon i-Management"></i>
                            <span class="item-name">{{ $t("Company") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('department')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/departments">
                            <i class="nav-icon i-Shop"></i>
                            <span class="item-name">{{
                                $t("Departments")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('designation')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/designations">
                            <i class="nav-icon i-Shutter"></i>
                            <span class="item-name">{{
                                $t("Designations")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('office_shift')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/office_Shift">
                            <i class="nav-icon i-Clock"></i>
                            <span class="item-name">{{
                                $t("Office_Shift")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('view_employee')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/employees">
                            <i class="nav-icon i-Engineering"></i>
                            <span class="item-name">{{ $t("Employees") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('attendance')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/attendance">
                            <i class="nav-icon i-Clock"></i>
                            <span class="item-name">{{
                                $t("Attendance")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('leave')
                        "
                        @click.prevent="toggleSidebarDropdwon($event)"
                        class="nav-item dropdown-sidemenu"
                    >
                        <a href="#">
                            <i class="nav-icon i-Calendar"></i>
                            <span class="item-name">{{
                                $t("Leave_request")
                            }}</span>
                            <i class="dd-arrow i-Arrow-Down"></i>
                        </a>
                        <ul class="submenu">
                            <li>
                                <router-link
                                    tag="a"
                                    class
                                    to="/app/hrm/leaves/list"
                                >
                                    <i class="nav-icon i-ID-Card"></i>
                                    <span class="item-name">{{
                                        $t("Leave_request")
                                    }}</span>
                                </router-link>
                            </li>
                            <li>
                                <router-link
                                    tag="a"
                                    class
                                    to="/app/hrm/leaves/type"
                                >
                                    <i class="nav-icon i-ID-Card"></i>
                                    <span class="item-name">{{
                                        $t("Leave_type")
                                    }}</span>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('holiday')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/holidays">
                            <i class="nav-icon i-Christmas-Bell"></i>
                            <span class="item-name">{{ $t("Holidays") }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('payroll')
                        "
                    >
                        <router-link tag="a" class to="/app/hrm/payrolls">
                            <i class="nav-icon i-Money-2"></i>
                            <span class="item-name">{{ $t("Payroll") }}</span>
                        </router-link>
                    </li>
                </ul>

                <!-- People -->
                <ul
                    class="childNav d-none"
                    data-parent="People"
                    :class="{ 'd-block': selectedParentMenu == 'People' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Customers_view')
                        "
                    >
                        <router-link tag="a" class to="/app/People/Customers">
                            <i class="nav-icon i-Administrator"></i>
                            <span class="item-name">{{ $t("Customers") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Suppliers_view')
                        "
                    >
                        <router-link tag="a" class to="/app/People/Suppliers">
                            <i class="nav-icon i-Administrator"></i>
                            <span class="item-name">{{ $t("Suppliers") }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('users_view')
                        "
                    >
                        <router-link tag="a" class to="/app/People/Users">
                            <i class="nav-icon i-Administrator"></i>
                            <span class="item-name">{{ $t("Users") }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="settings"
                    :class="{ 'd-block': selectedParentMenu == 'settings' }"
                >
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('setting_system')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/System_settings"
                        >
                            <i class="nav-icon i-Gear"></i>
                            <span class="item-name">{{
                                $t("SystemSettings")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'appearance_settings'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/appearance_settings"
                        >
                            <i class="nav-icon i-Data-Settings"></i>
                            <span class="item-name"
                                >{{ $t("Dynamic_Appearance") }}
                            </span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'translations_settings'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/translations_settings"
                        >
                            <i class="nav-icon i-Data-Settings"></i>
                            <span class="item-name"
                                >{{ $t("Languages") }}
                            </span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('payment_methods')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/payment_methods"
                        >
                            <i class="nav-icon i-Money-2"></i>
                            <span class="item-name">{{
                                $t("Payment_Methods")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('sms_settings')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/sms_settings"
                        >
                            <i class="nav-icon i-Speach-Bubble"></i>
                            <span class="item-name">{{
                                $t("sms_settings")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'notification_template'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/sms_templates"
                        >
                            <i class="nav-icon i-Speach-Bubble"></i>
                            <span class="item-name">{{
                                $t("sms_templates")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('mail_settings')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/mail_settings"
                        >
                            <i class="nav-icon i-Email"></i>
                            <span class="item-name">{{
                                $t("mail_settings")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'notification_template'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/email_templates"
                        >
                            <i class="nav-icon i-Email"></i>
                            <span class="item-name">{{
                                $t("email_templates")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('pos_settings')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/pos_settings"
                        >
                            <i class="nav-icon i-Cash-Register"></i>
                            <span class="item-name">{{
                                $t("Pos_Settings")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('module_settings')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/module_settings"
                        >
                            <i class="nav-icon i-Data-Settings"></i>
                            <span class="item-name">{{
                                $t("module_settings")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('setting_system')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/update_settings"
                        >
                            <i class="nav-icon i-Upgrade"></i>
                            <span class="item-name">{{
                                $t("update_settings")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('payment_gateway')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/payment_gateway"
                        >
                            <i class="nav-icon i-Money-2"></i>
                            <span class="item-name">{{
                                $t("Payment_Gateway")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('permissions_view')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/permissions"
                        >
                            <i class="nav-icon i-Key"></i>
                            <span class="item-name">{{
                                $t("GroupPermissions")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('warehouse')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/Warehouses"
                        >
                            <i class="nav-icon i-Clothing-Store"></i>
                            <span class="item-name">{{
                                $t("Warehouses")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('currency')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/settings/Currencies"
                        >
                            <i class="nav-icon i-Dollar-Sign"></i>
                            <span class="item-name">{{
                                $t("Currencies")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('backup')
                        "
                    >
                        <router-link tag="a" class to="/app/settings/Backup">
                            <i class="nav-icon i-Data-Backup"></i>
                            <span class="item-name">{{ $t("Backup") }}</span>
                        </router-link>
                    </li>
                </ul>

                <ul
                    class="childNav d-none"
                    data-parent="reports"
                    :class="{ 'd-block': selectedParentMenu == 'reports' }"
                >
                    <li
                        v-if="
                            currentUserPermissions &&
                            (currentUserPermissions.includes(
                                'Reports_payments_Purchases'
                            ) ||
                                currentUserPermissions.includes(
                                    'Reports_payments_Sales'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_payments_Sale_Returns'
                                ) ||
                                currentUserPermissions.includes(
                                    'Reports_payments_purchase_Return'
                                ))
                        "
                        @click.prevent="toggleSidebarDropdwon($event)"
                        class="nav-item dropdown-sidemenu"
                    >
                        <a href="#">
                            <i class="nav-icon i-Credit-Card"></i>
                            <span class="item-name">{{ $t("Payments") }}</span>
                            <i class="dd-arrow i-Arrow-Down"></i>
                        </a>
                        <ul class="submenu">
                            <li
                                v-if="
                                    currentUserPermissions &&
                                    currentUserPermissions.includes(
                                        'Reports_payments_Purchases'
                                    )
                                "
                            >
                                <router-link
                                    tag="a"
                                    class
                                    to="/app/reports/payments_purchase"
                                >
                                    <i class="nav-icon i-ID-Card"></i>
                                    <span class="item-name">{{
                                        $t("Purchases")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    currentUserPermissions &&
                                    currentUserPermissions.includes(
                                        'Reports_payments_Sales'
                                    )
                                "
                            >
                                <router-link
                                    tag="a"
                                    class
                                    to="/app/reports/payments_sale"
                                >
                                    <i class="nav-icon i-ID-Card"></i>
                                    <span class="item-name">{{
                                        $t("Sales")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    currentUserPermissions &&
                                    currentUserPermissions.includes(
                                        'Reports_payments_Sale_Returns'
                                    )
                                "
                            >
                                <router-link
                                    tag="a"
                                    class
                                    to="/app/reports/payments_sales_returns"
                                >
                                    <i class="nav-icon i-ID-Card"></i>
                                    <span class="item-name">{{
                                        $t("SalesReturn")
                                    }}</span>
                                </router-link>
                            </li>
                            <li
                                v-if="
                                    currentUserPermissions &&
                                    currentUserPermissions.includes(
                                        'Reports_payments_purchase_Return'
                                    )
                                "
                            >
                                <router-link
                                    tag="a"
                                    class
                                    to="/app/reports/payments_purchases_returns"
                                >
                                    <i class="nav-icon i-ID-Card"></i>
                                    <span class="item-name">{{
                                        $t("PurchasesReturn")
                                    }}</span>
                                </router-link>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'report_transactions'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/report_transactions"
                        >
                            <i class="nav-icon i-Dollar"></i>
                            <span class="item-name">{{
                                $t("Report_Transactions")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Reports_profit')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/profit_and_loss"
                        >
                            <i class="nav-icon i-Money-Bag"></i>
                            <span class="item-name">{{
                                $t("ProfitandLoss")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'inventory_valuation'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/inventory_valuation_summary"
                        >
                            <i class="nav-icon i-Pie-Chart"></i>
                            <span class="item-name">{{
                                $t("Inventory_Valuation")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('expenses_report')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/expenses_report"
                        >
                            <i class="nav-icon i-Receipt-3"></i>
                            <span class="item-name">{{
                                $t("Expense_Report")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('deposits_report')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/deposits_report"
                        >
                            <i class="nav-icon i-Safe-Box"></i>
                            <span class="item-name">{{
                                $t("Deposits_Report")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'Reports_quantity_alerts'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/quantity_alerts"
                        >
                            <i class="nav-icon i-Alarm"></i>
                            <span class="item-name">{{
                                $t("ProductQuantityAlerts")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Warehouse_report')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/warehouse_report"
                        >
                            <i class="nav-icon i-Warehouse"></i>
                            <span class="item-name">{{
                                $t("Warehouse_report")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('stock_report')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/stock_report"
                        >
                            <i class="nav-icon i-Box-Full"></i>
                            <span class="item-name">{{
                                $t("stock_report")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('product_report')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/product_report"
                        >
                            <i class="nav-icon i-Bar-Code"></i>
                            <span class="item-name">{{
                                $t("product_report")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Reports_sales')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/sales_report"
                        >
                            <i class="nav-icon i-Bar-Chart"></i>
                            <span class="item-name">{{
                                $t("SalesReport")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'product_sales_report'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/product_sales_report"
                        >
                            <i class="nav-icon i-Line-Chart"></i>
                            <span class="item-name">{{
                                $t("product_sales_report")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'report_sales_by_category'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/report_sales_by_category"
                        >
                            <i class="nav-icon i-Tag-3"></i>
                            <span class="item-name">{{
                                $t("Sales_by_Category")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'report_sales_by_brand'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/report_sales_by_brand"
                        >
                            <i class="nav-icon i-Shop"></i>
                            <span class="item-name">{{
                                $t("Sales_by_Brand")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Reports_purchase')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/purchase_report"
                        >
                            <i class="nav-icon i-Checkout"></i>
                            <span class="item-name">{{
                                $t("PurchasesReport")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes(
                                'product_purchases_report'
                            )
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/product_purchases_report"
                        >
                            <i class="nav-icon i-Shopping-Basket"></i>
                            <span class="item-name">{{
                                $t("Product_purchases_report")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Reports_customers')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/customers_report"
                        >
                            <i class="nav-icon i-User"></i>
                            <span class="item-name">{{
                                $t("CustomersReport")
                            }}</span>
                        </router-link>
                    </li>
                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Reports_suppliers')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/providers_report"
                        >
                            <i class="nav-icon i-User"></i>
                            <span class="item-name">{{
                                $t("SuppliersReport")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Top_products')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/top_selling_products"
                        >
                            <i class="nav-icon i-Trophy"></i>
                            <span class="item-name">{{
                                $t("Top_Selling_Products")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('Top_customers')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/top_customers"
                        >
                            <i class="nav-icon i-Trophy"></i>
                            <span class="item-name">{{
                                $t("Top_customers")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('users_report')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/users_report"
                        >
                            <i class="nav-icon i-User"></i>
                            <span class="item-name">{{
                                $t("Users_Report")
                            }}</span>
                        </router-link>
                    </li>

                    <li
                        class="nav-item"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('report_error_logs')
                        "
                    >
                        <router-link
                            tag="a"
                            class
                            to="/app/reports/report_error_logs"
                        >
                            <i class="nav-icon i-Bug"></i>
                            <span class="item-name">{{
                                $t("Error_Logs")
                            }}</span>
                        </router-link>
                    </li>
                </ul>
            </div>
        </vue-perfect-scrollbar>
        <div
            @click="removeOverlay()"
            class="sidebar-overlay"
            :class="{ open: getSideBarToggleProperties.isSecondarySideNavOpen }"
        ></div>
    </div>
    <!--=============== Left side End ================-->
</template>

<script>
import Topnav from "./TopNav";
import { isMobile } from "mobile-device-detect";

import { mapGetters, mapActions } from "vuex";

export default {
    components: {
        Topnav,
    },

    data() {
        return {
            isDisplay: true,
            isMenuOver: false,
            isStyle: true,
            selectedParentMenu: "",
            isMobile,
        };
    },
    mounted() {
        this.toggleSelectedParentMenu();
        window.addEventListener("resize", this.handleWindowResize);
        document.addEventListener("click", this.returnSelectedParentMenu);
        this.handleWindowResize();
    },

    beforeDestroy() {
        document.removeEventListener("click", this.returnSelectedParentMenu);
        window.removeEventListener("resize", this.handleWindowResize);
    },

    computed: {
        ...mapGetters([
            "getSideBarToggleProperties",
            "currentUserPermissions",
            "getallmodules",
        ]),
    },

    methods: {
        ...mapActions([
            "changeSecondarySidebarProperties",
            "changeSecondarySidebarPropertiesViaMenuItem",
            "changeSecondarySidebarPropertiesViaOverlay",
            "changeSidebarProperties",
        ]),

        handleWindowResize() {
            if (window.innerWidth <= 1200) {
                if (this.getSideBarToggleProperties.isSideNavOpen) {
                    this.changeSidebarProperties();
                }
                if (this.getSideBarToggleProperties.isSecondarySideNavOpen) {
                    this.changeSecondarySidebarProperties();
                }
            } else {
                if (!this.getSideBarToggleProperties.isSideNavOpen) {
                    this.changeSidebarProperties();
                }
            }
        },
        toggleSelectedParentMenu() {
            const currentParentUrl = this.$route.path
                .split("/")
                .filter((x) => x !== "")[1];
            if (currentParentUrl !== undefined || currentParentUrl !== null) {
                this.selectedParentMenu = currentParentUrl.toLowerCase();
            } else {
                this.selectedParentMenu = "dashboard";
            }
        },
        toggleSubMenu(e) {
            let hasSubmenu = e.target.dataset.submenu;
            let parent = e.target.dataset.item;

            if (hasSubmenu) {
                this.selectedParentMenu = parent;

                this.changeSecondarySidebarPropertiesViaMenuItem(true);
            } else {
                this.selectedParentMenu = parent;
                this.changeSecondarySidebarPropertiesViaMenuItem(false);
            }
        },

        removeOverlay() {
            this.changeSecondarySidebarPropertiesViaOverlay();
            if (window.innerWidth <= 1200) {
                this.changeSidebarProperties();
            }
            this.toggleSelectedParentMenu();
        },
        returnSelectedParentMenu() {
            if (!this.isMenuOver) {
                this.toggleSelectedParentMenu();
            }
        },

        toggleSidebarDropdwon(event) {
            let dropdownMenus = this.$el.querySelectorAll(
                ".dropdown-sidemenu.open"
            );

            event.currentTarget.classList.toggle("open");

            dropdownMenus.forEach((dropdown) => {
                dropdown.classList.remove("open");
            });
        },
    },
};
</script>

<style scoped>
    .sidebar-left {
        background-color: #ffffff;
        color: #333;
        transition: all 0.3s ease;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        padding: 30px 0;
    }

    .customize-nav-item-hold {
        display: flex !important;
        /* justify-content: flex-start !important; */
        align-items: center;
        padding: 0 15px;
        font-size: 12px !important;
        height: 50px;
        border-radius: 10px;
        border-bottom: none !important;
        color: black !important;
    }

    .customize-nav-item-hold:hover {
        background-color: #dae3f2;
        color: blue !important;
    }

    .customize-nav-item-hold svg,
    .customize-nav-item-hold .nav-icon {
        margin-right: 10px !important;
    }
</style>

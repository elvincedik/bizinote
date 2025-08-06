<template>
    <div class="main-content">
      <div>
            <p>HRM / Employees</p>
        </div>
        <h3 class="mb-0">Employees</h3>
        <h5 class="mt-4">Employees</h5>
        <hr class="mt-1" style="border-left: 14px solid #0944aa; height: 3px" />
        <!-- <breadcumb :page="$t('Employees')" :folder="$t('hrm')" /> -->

        <div
            v-if="isLoading"
            class="loading_page spinner spinner-primary mr-3"
        ></div>
        <div v-else>
            <vue-good-table
                mode="remote"
                :columns="columns"
                :totalRows="totalRows"
                :rows="employees"
                @on-page-change="onPageChange"
                @on-per-page-change="onPerPageChange"
                @on-sort-change="onSortChange"
                @on-search="onSearch"
                :search-options="{
                    enabled: true,
                    placeholder: $t('Search_this_table'),
                }"
                :select-options="{
                    enabled: true,
                    clearSelectionText: '',
                }"
                @on-selected-rows-change="selectionChanged"
                :pagination-options="{
                    enabled: true,
                    mode: 'records',
                    nextLabel: 'next',
                    prevLabel: 'prev',
                }"
                styleClass="tableOne table-hover vgt-table"
            >
                <div slot="selected-row-actions">
                    <button
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('delete_employee')
                        "
                        class="btn btn-danger btn-sm"
                        @click="delete_by_selected()"
                    >
                        {{ $t("Del") }}
                    </button>
                </div>
                <div slot="table-actions" class="mt-2 mb-3 d-flex">
                    <b-button
                        variant="outline-info ripple m-1"
                        size="sm"
                        v-b-toggle.sidebar-right
                    >
                        {{ $t("Filter") }}
                        <i class="i-Filter-2"></i>
                    </b-button>

                    <vue-excel-xlsx
                        class="btn btn-sm btn-outline-success ripple m-1"
                        :data="employees"
                        :columns="columns"
                        :file-name="'employees'"
                        :file-type="'xlsx'"
                        :sheet-name="'employees'"
                    >
                        Excel
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M14.6855 11.5133L6.38379 10.0508V20.8575C6.38369 20.975 6.40678 21.0913 6.45173 21.1998C6.49667 21.3083 6.5626 21.4069 6.64573 21.4899C6.72885 21.5729 6.82754 21.6387 6.93613 21.6834C7.04472 21.7282 7.16108 21.7511 7.27854 21.7508H21.6035C21.7211 21.7513 21.8376 21.7285 21.9464 21.6839C22.0552 21.6392 22.154 21.5734 22.2373 21.4904C22.3206 21.4074 22.3867 21.3088 22.4317 21.2002C22.4768 21.0916 22.4999 20.9751 22.4998 20.8575V16.8758L14.6855 11.5133Z"
                                fill="#185C37"
                            />
                            <path
                                d="M14.6855 2.25H7.27854C7.16108 2.24971 7.04472 2.2726 6.93613 2.31736C6.82754 2.36213 6.72885 2.42789 6.64573 2.51088C6.5626 2.59386 6.49667 2.69244 6.45173 2.80096C6.40678 2.90947 6.38369 3.0258 6.38379 3.14325V7.125L14.6855 12L19.0813 13.4625L22.4998 12V7.125L14.6855 2.25Z"
                                fill="#21A366"
                            />
                            <path
                                d="M6.38379 7.125H14.6855V12H6.38379V7.125Z"
                                fill="#107C41"
                            />
                            <path
                                opacity="0.1"
                                d="M12.3253 6.15039H6.38379V18.3379H12.3253C12.562 18.3367 12.7888 18.2423 12.9564 18.0751C13.124 17.9079 13.219 17.6814 13.2208 17.4446V7.04364C13.219 6.80689 13.124 6.58038 12.9564 6.41318C12.7888 6.24598 12.562 6.15157 12.3253 6.15039Z"
                                fill="black"
                            />
                            <path
                                opacity="0.2"
                                d="M11.837 6.63672H6.38379V18.8242H11.837C12.0738 18.823 12.3005 18.7286 12.4682 18.5614C12.6358 18.3942 12.7308 18.1677 12.7325 17.931V7.52997C12.7308 7.29322 12.6358 7.06671 12.4682 6.89951C12.3005 6.73231 12.0738 6.6379 11.837 6.63672Z"
                                fill="black"
                            />
                            <path
                                opacity="0.2"
                                d="M11.837 6.63672H6.38379V17.8492H11.837C12.0738 17.848 12.3005 17.7536 12.4682 17.5864C12.6358 17.4192 12.7308 17.1927 12.7325 16.956V7.52997C12.7308 7.29322 12.6358 7.06671 12.4682 6.89951C12.3005 6.73231 12.0738 6.6379 11.837 6.63672Z"
                                fill="black"
                            />
                            <path
                                opacity="0.2"
                                d="M11.3488 6.63672H6.38379V17.8492H11.3488C11.5855 17.848 11.8123 17.7536 11.9799 17.5864C12.1475 17.4192 12.2425 17.1927 12.2443 16.956V7.52997C12.2425 7.29322 12.1475 7.06671 11.9799 6.89951C11.8123 6.73231 11.5855 6.6379 11.3488 6.63672Z"
                                fill="black"
                            />
                            <path
                                d="M2.3955 6.63672H11.349C11.5862 6.63652 11.8137 6.7305 11.9816 6.89799C12.1496 7.06549 12.2441 7.2928 12.2445 7.52997V16.4685C12.2441 16.7056 12.1496 16.933 11.9816 17.1005C11.8137 17.2679 11.5862 17.3619 11.349 17.3617H2.3955C2.27798 17.3621 2.16154 17.3393 2.05286 17.2946C1.94418 17.2499 1.84541 17.1841 1.7622 17.1011C1.679 17.0181 1.613 16.9195 1.56801 16.8109C1.52301 16.7024 1.4999 16.586 1.5 16.4685V7.52997C1.4999 7.41245 1.52301 7.29607 1.56801 7.1875C1.613 7.07894 1.679 6.98033 1.7622 6.89733C1.84541 6.81434 1.94418 6.74859 2.05286 6.70387C2.16154 6.65915 2.27798 6.63633 2.3955 6.63672Z"
                                fill="url(#paint0_linear_435_10350)"
                            />
                            <path
                                d="M4.27539 14.9052L6.15864 11.9922L4.43364 9.0957H5.81889L6.76014 10.9505C6.84714 11.126 6.91014 11.2565 6.93864 11.3435H6.95139C7.01289 11.203 7.07789 11.0665 7.14639 10.934L8.15289 9.0987H9.42789L7.65864 11.9787L9.47289 14.9075H8.11614L7.02864 12.8742C6.97829 12.7867 6.93539 12.6951 6.90039 12.6005H6.88239C6.85061 12.6927 6.80833 12.7811 6.75639 12.8637L5.63664 14.9052H4.27539Z"
                                fill="white"
                            />
                            <path
                                d="M21.6043 2.25001H14.6855V7.125H22.4998V3.14325C22.4999 3.02573 22.4768 2.90935 22.4318 2.80079C22.3868 2.69222 22.3208 2.59361 22.2376 2.51061C22.1544 2.42762 22.0556 2.36187 21.9469 2.31715C21.8383 2.27243 21.7218 2.24961 21.6043 2.25001Z"
                                fill="#33C481"
                            />
                            <path
                                d="M14.6855 12H22.4998V16.875H14.6855V12Z"
                                fill="#107C41"
                            />
                            <defs>
                                <linearGradient
                                    id="paint0_linear_435_10350"
                                    x1="3.3705"
                                    y1="5.93472"
                                    x2="10.374"
                                    y2="18.0637"
                                    gradientUnits="userSpaceOnUse"
                                >
                                    <stop stop-color="#18884F" />
                                    <stop offset="0.5" stop-color="#117E43" />
                                    <stop offset="1" stop-color="#0B6631" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </vue-excel-xlsx>
                    <b-button
                        @click="Employee_PDF()"
                        size="sm"
                        variant="outline-danger ripple m-1"
                    >
                        PDF
                        <svg
                            width="17"
                            height="20"
                            viewBox="0 0 17 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M9.5 7H15L9.5 1.5V7ZM2.5 0H10.5L16.5 6V18C16.5 18.5304 16.2893 19.0391 15.9142 19.4142C15.5391 19.7893 15.0304 20 14.5 20H2.5C1.96957 20 1.46086 19.7893 1.08579 19.4142C0.710714 19.0391 0.5 18.5304 0.5 18V2C0.5 1.46957 0.710714 0.960859 1.08579 0.585786C1.46086 0.210714 1.96957 0 2.5 0ZM7.43 10.44C7.84 11.34 8.36 12.08 8.96 12.59L9.37 12.91C8.5 13.07 7.3 13.35 6.03 13.84L5.92 13.88L6.42 12.84C6.87 11.97 7.2 11.18 7.43 10.44ZM13.91 14.25C14.09 14.07 14.18 13.84 14.19 13.59C14.22 13.39 14.17 13.2 14.07 13.04C13.78 12.57 13.03 12.35 11.79 12.35L10.5 12.42L9.63 11.84C9 11.32 8.43 10.41 8.03 9.28L8.07 9.14C8.4 7.81 8.71 6.2 8.05 5.54C7.96927 5.46161 7.87378 5.40003 7.76907 5.35883C7.66435 5.31763 7.5525 5.29764 7.44 5.3H7.2C6.83 5.3 6.5 5.69 6.41 6.07C6.04 7.4 6.26 8.13 6.63 9.34V9.35C6.38 10.23 6.06 11.25 5.55 12.28L4.59 14.08L3.7 14.57C2.5 15.32 1.93 16.16 1.82 16.69C1.78 16.88 1.8 17.05 1.87 17.23L1.9 17.28L2.38 17.59L2.82 17.7C3.63 17.7 4.55 16.75 5.79 14.63L5.97 14.56C7 14.23 8.28 14 10 13.81C11.03 14.32 12.24 14.55 13 14.55C13.44 14.55 13.74 14.44 13.91 14.25ZM13.5 13.54L13.59 13.65C13.58 13.75 13.55 13.76 13.5 13.78H13.46L13.27 13.8C12.81 13.8 12.1 13.61 11.37 13.29C11.46 13.19 11.5 13.19 11.6 13.19C13 13.19 13.4 13.44 13.5 13.54ZM4.33 15C3.68 16.19 3.09 16.85 2.64 17C2.69 16.62 3.14 15.96 3.85 15.31L4.33 15ZM7.35 8.09C7.12 7.19 7.11 6.46 7.28 6.04L7.35 5.92L7.5 5.97C7.67 6.21 7.69 6.53 7.59 7.07L7.56 7.23L7.4 8.05L7.35 8.09Z"
                                fill="#EF5350"
                            />
                        </svg>
                    </b-button>
                    <router-link
                        class="btn-sm btn btn-primary ripple btn-icon m-1"
                        to="/app/hrm/employees/store"
                        v-if="
                            currentUserPermissions &&
                            currentUserPermissions.includes('add_employee')
                        "
                        style="background-color: #0944aa"
                    >
                        <span class="ul-btn__icon">
                            <!-- <span class="ul-btn__text ml-1">{{$t('Add')}}</span> -->
                            <span class="ul-btn__text ml-1">Create New</span>
                            <i class="i-Add"></i>
                        </span>
                    </router-link>
                </div>

                <template slot="table-row" slot-scope="props">
                    <span v-if="props.column.field == 'actions'">
                        <router-link
                            v-if="
                                currentUserPermissions &&
                                currentUserPermissions.includes('view_employee')
                            "
                            title="detail"
                            :to="'/app/hrm/employees/detail/' + props.row.id"
                        >
                            <i class="i-Eye text-25 text-info"></i>
                        </router-link>
                        <router-link
                            v-if="
                                currentUserPermissions &&
                                currentUserPermissions.includes('edit_employee')
                            "
                            title="Edit"
                            v-b-tooltip.hover
                            :to="'/app/hrm/employees/edit/' + props.row.id"
                        >
                            <i class="i-Edit text-25 text-success"></i>
                        </router-link>
                        <a
                            title="Delete"
                            v-b-tooltip.hover
                            v-if="
                                currentUserPermissions &&
                                currentUserPermissions.includes(
                                    'delete_employee'
                                )
                            "
                            @click="Remove_Employee(props.row.id)"
                        >
                            <i class="i-Close-Window text-25 text-danger"></i>
                        </a>
                    </span>
                </template>
            </vue-good-table>
        </div>

        <!-- Multiple Filters -->
        <b-sidebar
            id="sidebar-right"
            :title="$t('Filter')"
            bg-variant="white"
            right
            shadow
        >
            <div class="px-3 py-2">
                <b-row>
                    <!-- Username  -->
                    <b-col md="12">
                        <b-form-group :label="$t('username')">
                            <b-form-input
                                label="Username"
                                :placeholder="$t('username')"
                                v-model="Filter_username"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>

                    <!-- Employment Type  -->
                    <b-col md="12">
                        <b-form-group :label="$t('Employment_type')">
                            <v-select
                                v-model="Filter_employment_type"
                                :reduce="(label) => label.value"
                                :placeholder="$t('Employment_type')"
                                :options="[
                                    { label: 'Full-time', value: 'full_time' },
                                    { label: 'Part-time', value: 'part_time' },
                                    {
                                        label: 'Self-employed',
                                        value: 'self_employed',
                                    },

                                    { label: 'Freelance', value: 'freelance' },
                                    { label: 'Contract', value: 'contract' },
                                    {
                                        label: 'Internship',
                                        value: 'internship',
                                    },
                                    {
                                        label: 'Apprenticeship',
                                        value: 'apprenticeship',
                                    },
                                    { label: 'Seasonal', value: 'seasonal' },
                                ]"
                            ></v-select>
                        </b-form-group>
                    </b-col>

                    <!-- Company  -->
                    <b-col md="12">
                        <b-form-group :label="$t('Company')">
                            <v-select
                                :reduce="(label) => label.value"
                                :placeholder="$t('Company')"
                                v-model="Filter_company"
                                :options="
                                    companies.map((companies) => ({
                                        label: companies.name,
                                        value: companies.id,
                                    }))
                                "
                            />
                        </b-form-group>
                    </b-col>

                    <b-col md="6" sm="12">
                        <b-button
                            @click="Get_Employees(serverParams.page)"
                            variant="primary m-1"
                            size="sm"
                            block
                        >
                            <i class="i-Filter-2"></i>
                            {{ $t("Filter") }}
                        </b-button>
                    </b-col>
                    <b-col md="6" sm="12">
                        <b-button
                            @click="Reset_Filter()"
                            variant="danger m-1"
                            size="sm"
                            block
                        >
                            <i class="i-Power-2"></i>
                            {{ $t("Reset") }}
                        </b-button>
                    </b-col>
                </b-row>
            </div>
        </b-sidebar>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";
import jsPDF from "jspdf";
import "jspdf-autotable";

export default {
    metaInfo: {
        title: "Employee",
    },
    data() {
        return {
            isLoading: true,
            serverParams: {
                columnFilters: {},
                sort: {
                    field: "id",
                    type: "desc",
                },
                page: 1,
                perPage: 10,
            },
            selectedIds: [],
            totalRows: "",
            search: "",
            limit: "10",
            Filter_username: "",
            Filter_employment_type: "",
            Filter_company: "",
            employees: [],
            companies: [],
        };
    },

    computed: {
        ...mapGetters(["currentUserPermissions", "currentUser"]),
        columns() {
            return [
                {
                    label: this.$t("FirstName"),
                    field: "firstname",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("LastName"),
                    field: "lastname",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Phone"),
                    field: "phone",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Company"),
                    field: "company_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Department"),
                    field: "department_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Designation"),
                    field: "designation_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Office_Shift"),
                    field: "office_shift_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Action"),
                    field: "actions",
                    html: true,
                    tdClass: "text-right",
                    thClass: "text-right",
                    sortable: false,
                },
            ];
        },
    },

    methods: {
        //---------------------- Employee PDF -------------------------------\\
        Employee_PDF() {
            var self = this;
            let pdf = new jsPDF("p", "pt");

            const fontPath = "/fonts/Vazirmatn-Bold.ttf";
            pdf.addFont(fontPath, "VazirmatnBold", "bold");
            pdf.setFont("VazirmatnBold");

            let columns = [
                { title: self.$t("FirstName"), dataKey: "firstname" },
                { title: self.$t("LastName"), dataKey: "lastname" },
                { title: self.$t("Phone"), dataKey: "phone" },
                { title: self.$t("Company"), dataKey: "company_name" },
                { title: self.$t("Department"), dataKey: "department_name" },
                { title: self.$t("Designation"), dataKey: "designation_name" },
                {
                    title: self.$t("Office_Shift"),
                    dataKey: "office_shift_name",
                },
            ];

            pdf.autoTable({
                columns: columns,
                body: self.employees,
                startY: 70,
                theme: "grid",
                didDrawPage: (data) => {
                    pdf.setFont("VazirmatnBold");
                    pdf.setFontSize(18);
                    pdf.text("Employee List", 40, 25);
                },
                styles: {
                    font: "VazirmatnBold",
                    halign: "center", //
                },
                headStyles: {
                    fillColor: [200, 200, 200],
                    textColor: [0, 0, 0],
                    fontStyle: "bold",
                },
            });

            pdf.save("Employee_List.pdf");
        },

        //------ update Params Table
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },

        //---- Event Page Change
        onPageChange({ currentPage }) {
            if (this.serverParams.page !== currentPage) {
                this.updateParams({ page: currentPage });
                this.Get_Employees(currentPage);
            }
        },

        //---- Event Per Page Change
        onPerPageChange({ currentPerPage }) {
            if (this.limit !== currentPerPage) {
                this.limit = currentPerPage;
                this.updateParams({ page: 1, perPage: currentPerPage });
                this.Get_Employees(1);
            }
        },

        //---- Event Select Rows
        selectionChanged({ selectedRows }) {
            this.selectedIds = [];
            selectedRows.forEach((row, index) => {
                this.selectedIds.push(row.id);
            });
        },

        //------ Event Sort Change
        onSortChange(params) {
            let field = "";
            if (params[0].field == "company_name") {
                field = "company_id";
            } else if (params[0].field == "department_name") {
                field = "department_id";
            } else if (params[0].field == "designation_name") {
                field = "designation_id";
            } else {
                field = params[0].field;
            }
            this.updateParams({
                sort: {
                    type: params[0].type,
                    field: field,
                },
            });
            this.Get_Employees(this.serverParams.page);
        },

        //------ Event Search
        onSearch(value) {
            this.search = value.searchTerm;
            this.Get_Employees(this.serverParams.page);
        },

        //------ Reset Filter
        Reset_Filter() {
            this.search = "";
            this.Filter_username = "";
            this.Filter_employment_type = "";
            this.Filter_company = "";
            this.Get_Employees(this.serverParams.page);
        },

        // Simply replaces null values with strings=''
        setToStrings() {
            if (this.Filter_employment_type === null) {
                this.Filter_employment_type = "";
            } else if (this.Filter_company === null) {
                this.Filter_company = "";
            }
        },

        //------------------------------------------------ Get All Expense -------------------------------\\
        Get_Employees(page) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            this.setToStrings();
            axios
                .get(
                    "employees?page=" +
                        page +
                        "&username=" +
                        this.Filter_username +
                        "&employment_type=" +
                        this.Filter_employment_type +
                        "&company_id=" +
                        this.Filter_company +
                        "&SortField=" +
                        this.serverParams.sort.field +
                        "&SortType=" +
                        this.serverParams.sort.type +
                        "&search=" +
                        this.search +
                        "&limit=" +
                        this.limit
                )
                .then((response) => {
                    this.employees = response.data.employees;
                    this.companies = response.data.companies;
                    this.totalRows = response.data.totalRows;

                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    this.isLoading = false;
                })
                .catch((response) => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },

        //------------------------------- Remove Employee -------------------------\\

        Remove_Employee(id) {
            this.$swal({
                title: this.$t("Delete_Title"),
                text: this.$t("Delete_Text"),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: this.$t("Delete_cancelButtonText"),
                confirmButtonText: this.$t("Delete_confirmButtonText"),
            }).then((result) => {
                if (result.value) {
                    // Start the progress bar.
                    NProgress.start();
                    NProgress.set(0.1);
                    axios
                        .delete("employees/" + id)
                        .then(() => {
                            this.$swal(
                                this.$t("Delete_Deleted"),
                                this.$t("Deleted_in_successfully"),
                                "success"
                            );
                            Fire.$emit("Delete_Employee");
                        })
                        .catch(() => {
                            // Complete the animation of theprogress bar.
                            setTimeout(() => NProgress.done(), 500);
                            this.$swal(
                                this.$t("Delete_Failed"),
                                this.$t("Delete_Therewassomethingwronge"),
                                "warning"
                            );
                        });
                }
            });
        },

        //---- Delete Expense by selection

        delete_by_selected() {
            this.$swal({
                title: this.$t("Delete_Title"),
                text: this.$t("Delete_Text"),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: this.$t("Delete_cancelButtonText"),
                confirmButtonText: this.$t("Delete_confirmButtonText"),
            }).then((result) => {
                if (result.value) {
                    // Start the progress bar.
                    NProgress.start();
                    NProgress.set(0.1);
                    axios
                        .post("employees/delete/by_selection", {
                            selectedIds: this.selectedIds,
                        })
                        .then(() => {
                            this.$swal(
                                this.$t("Delete_Deleted"),
                                this.$t("Deleted_in_successfully"),
                                "success"
                            );

                            Fire.$emit("Delete_Employee");
                        })
                        .catch(() => {
                            // Complete the animation of theprogress bar.
                            setTimeout(() => NProgress.done(), 500);
                            this.$swal(
                                this.$t("Delete_Failed"),
                                this.$t("Delete_Therewassomethingwronge"),
                                "warning"
                            );
                        });
                }
            });
        },
    },

    //----------------------------- Created function-------------------
    created: function () {
        this.Get_Employees(1);

        Fire.$on("Delete_Employee", () => {
            setTimeout(() => {
                // Complete the animation of theprogress bar.
                NProgress.done();
                this.Get_Employees(this.serverParams.page);
            }, 500);
        });
    },
};
</script>

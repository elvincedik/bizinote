<template>
    <div class="main-content">
        <div class="bg-white mb-3 py-1 px-3">
            <p>subscription product</p>
        </div>
        <!-- <breadcumb :page="$t('Subscription_Product')" :folder="$t('Subscriptions')"/> -->

        <div
            v-if="isLoading"
            class="loading_page spinner spinner-primary mr-3"
        ></div>
        <b-card class="wrapper" v-if="!isLoading">
            <b-alert
                show
                variant="info"
                style="background-color: #eee6ff"
                class="py-3"
            >
                ⏱️<span style="color: #4d00ff; font-size: 16px">
                    Subscription Automation </span
                ><br /><br />
                Once a subscription is created, the system uses scheduled
                commands (cron jobs) to handle billing automatically.<br /><br />

                <span style="color: #4d00ff; font-size: 16px">
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M4 10C4 8.93913 4.42143 7.92172 5.17157 7.17157C5.92172 6.42143 6.93913 6 8 6H16C17.0609 6 18.0783 6.42143 18.8284 7.17157C19.5786 7.92172 20 8.93913 20 10V20C20 20.5304 19.7893 21.0391 19.4142 21.4142C19.0391 21.7893 18.5304 22 18 22H6C5.46957 22 4.96086 21.7893 4.58579 21.4142C4.21071 21.0391 4 20.5304 4 20V10Z"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M8 10H16"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M8 18H16"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M8 22V16C8 15.4696 8.21071 14.9609 8.58579 14.5858C8.96086 14.2107 9.46957 14 10 14H14C14.5304 14 15.0391 14.2107 15.4142 14.5858C15.7893 14.9609 16 15.4696 16 16V22"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M9 6V4C9 3.46957 9.21071 2.96086 9.58579 2.58579C9.96086 2.21071 10.4696 2 11 2H13C13.5304 2 14.0391 2.21071 14.4142 2.58579C14.7893 2.96086 15 3.46957 15 4V6"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    Generate Invoices Automatically </span
                ><br />
                <code style="color: #ff5c02"
                    >php artisan subscriptions:generate-invoices</code
                ><br />
                This command will automatically create a new sale (invoice) for
                each active subscription based on its billing cycle (daily,
                weekly, monthly, yearly).<br /><br />

                <span style="color: #4d00ff; font-size: 16px">
                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M22 13V6C22 5.46957 21.7893 4.96086 21.4142 4.58579C21.0391 4.21071 20.5304 4 20 4H4C3.46957 4 2.96086 4.21071 2.58579 4.58579C2.21071 4.96086 2 5.46957 2 6V18C2 19.1 2.9 20 4 20H12"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M22 7L13.03 12.7C12.7213 12.8934 12.3643 12.996 12 12.996C11.6357 12.996 11.2787 12.8934 10.97 12.7L2 7"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M16 19L18 21L22 17"
                            stroke="#8855FF"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                    Send SMS Reminders </span
                ><br />
                <code style="color: #ff5c02"
                    >php artisan subscriptions:send-sms-reminders</code
                ><br />
                This command sends reminder messages to clients for upcoming or
                due subscription payments via SMS.<br /><br />

                📌
                <span style="color: #4d00ff; font-size: 16px"
                    >Make sure to schedule these commands in your Cron Job
                    (e.g., cPanel) to run daily or as needed.</span
                >
            </b-alert>

            <div
                class="bg-white py-1 px-3 rounded-top"
                style="box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important"
            >
                <h5 class="mt-4">Subscription</h5>
                <hr
                    class="mt-1"
                    style="border-left: 14px solid #0944aa; height: 3px"
                />
            </div>

            <div
                style="
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
                    padding: 0 1rem !important;
                    background-color: white !important;
                "
                class="rounded-bottom"
            >
                <vue-good-table
                    mode="remote"
                    :columns="columns"
                    :totalRows="totalRows"
                    :rows="subscriptions"
                    @on-page-change="onPageChange"
                    @on-per-page-change="onPerPageChange"
                    @on-sort-change="onSortChange"
                    @on-search="onSearch"
                    :search-options="{
                        enabled: true,
                        placeholder: $t('Search_this_table'),
                    }"
                    :pagination-options="{
                        enabled: true,
                        mode: 'records',
                        nextLabel: 'next',
                        prevLabel: 'prev',
                    }"
                    styleClass="table-hover tableOne vgt-table"
                >
                    <div slot="table-actions" class="mt-2 mb-3">
                        <router-link
                            class="btn-sm btn btn-primary ripple btn-icon m-1"
                            to="/app/subscription_product/store"
                        style="background-color: #0944aa"
                    >
                        Create New
                        <i class="i-Add"></i>
                        <!-- {{ $t("Add") }} -->
                        </router-link>
                    </div>

                    <template slot="table-row" slot-scope="props">
                        <span v-if="props.column.field == 'actions'">
                            <router-link
                                title="Subscription details"
                                v-b-tooltip.hover
                                :to="
                                    '/app/subscription_product/detail/' +
                                    props.row.id
                                "
                            >
                                <i class="i-Eye text-25 text-primary"></i>
                            </router-link>

                            <a
                                title="Delete"
                                v-b-tooltip.hover
                                @click="Remove_subscription(props.row.id)"
                            >
                                <i
                                    class="i-Close-Window text-25 text-danger cursor-pointer"
                                ></i>
                            </a>
                        </span>

                        <div v-else-if="props.column.field == 'status'">
                            <label class="switch switch-primary mr-3">
                                <input
                                    @change="isChecked(props.row)"
                                    type="checkbox"
                                    v-model="props.row.status"
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </template>
                </vue-good-table>
            </div>

        </b-card>
    </div>
</template>

<script>
import NProgress from "nprogress";

export default {
    metaInfo: {
        title: "Subscriptions",
    },
    data() {
        return {
            isLoading: true,
            SubmitProcessing: false,
            serverParams: {
                columnFilters: {},
                sort: {
                    field: "id",
                    type: "desc",
                },
                page: 1,
                perPage: 10,
            },
            totalRows: "",
            search: "",
            limit: "10",
            subscriptions: [],
        };
    },
    computed: {
        columns() {
            return [
                {
                    label: this.$t("Customer"),
                    field: "client_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("product_name"),
                    field: "product_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },

                {
                    label: this.$t("warehouse"),
                    field: "warehouse_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },

                {
                    label: this.$t("Billing_Cycle"),
                    field: "billing_cycle",
                    tdClass: "text-left",
                    thClass: "text-left",
                },

                {
                    label: this.$t("total_cycles"),
                    field: "total_cycles",
                    tdClass: "text-left",
                    thClass: "text-left",
                },

                {
                    label: this.$t("remaining_cycles"),
                    field: "remaining_cycles",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("next_billing_date"),
                    field: "next_billing_date",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Status"),
                    field: "status",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false,
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
        isChecked(subscription) {
            let newStatus = subscription.status ? "active" : "canceled"; // Adjust based on toggle logic

            axios
                .put(`/subscriptions/${subscription.id}/status`, {
                    status: newStatus, // Send the updated status
                })
                .then((response) => {
                    this.makeToast(
                        "success",
                        this.$t("Subscription_status_updated_successfully"),
                        this.$t("Success")
                    );
                })
                .catch((error) => {
                    this.makeToast(
                        "warning",
                        this.$t("Failed_to_update_subscription_status"),
                        this.$t("Warning")
                    );
                });
        },

        //---- update Params Table
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },

        //---- Event Page Change
        onPageChange({ currentPage }) {
            if (this.serverParams.page !== currentPage) {
                this.updateParams({ page: currentPage });
                this.get_subscriptions(currentPage);
            }
        },

        //---- Event Per Page Change
        onPerPageChange({ currentPerPage }) {
            if (this.limit !== currentPerPage) {
                this.limit = currentPerPage;
                this.updateParams({ page: 1, perPage: currentPerPage });
                this.get_subscriptions(1);
            }
        },

        //---- Event on Sort Change
        onSortChange(params) {
            this.updateParams({
                sort: {
                    type: params[0].type,
                    field: params[0].field,
                },
            });
            this.get_subscriptions(this.serverParams.page);
        },

        //---- Event on Search

        onSearch(value) {
            this.search = value.searchTerm;
            this.get_subscriptions(this.serverParams.page);
        },

        //---- Validation State Form

        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },

        //------ Toast
        makeToast(variant, msg, title) {
            this.$root.$bvToast.toast(msg, {
                title: title,
                variant: variant,
                solid: true,
            });
        },

        //--------------------------Get ALL  asset ---------------------------\\

        get_subscriptions(page) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            axios
                .get(
                    "subscriptions?page=" +
                        page +
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
                    this.subscriptions = response.data.subscriptions;
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

        //--------------------------- Remove subscription----------------\\
        Remove_subscription(id) {
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
                    axios
                        .delete("subscriptions/" + id)
                        .then(() => {
                            this.$swal(
                                this.$t("Delete_Deleted"),
                                this.$t("Deleted_in_successfully"),
                                "success"
                            );

                            Fire.$emit("Event_delete_subscription");
                        })
                        .catch(() => {
                            this.$swal(
                                this.$t("Delete_Failed"),
                                this.$t("Delete_Therewassomethingwronge"),
                                "warning"
                            );
                        });
                }
            });
        },
    }, //end Methods

    //----------------------------- Created function-------------------

    created: function () {
        this.get_subscriptions(1);

        Fire.$on("Event_delete_subscription", () => {
            setTimeout(() => {
                this.get_subscriptions(this.serverParams.page);
            }, 500);
        });
    },
};
</script>

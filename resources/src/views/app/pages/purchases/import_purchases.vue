<template>
    <div class="main-content">
        <div>
            <p>purchases / Create Purchases</p>
        </div>
        <h3 class="mb-0">Create Purchases</h3>
        <!-- <breadcumb page="Import Purchases" :folder="$t('ListPurchases')" /> -->
        <div
            v-if="isLoading"
            class="loading_page spinner spinner-primary mr-3"
        ></div>

        <validation-observer ref="create_purchase" v-if="!isLoading">
            <b-form @submit.prevent="Submit_Purchase">
                <b-row>
                    <b-col lg="12" md="12" sm="12">
                        <b-card>
                            <b-row>
                                <!-- date  -->
                                <b-col lg="4" md="4" sm="12" class="mb-3">
                                    <validation-provider
                                        name="date"
                                        :rules="{ required: true }"
                                        v-slot="validationContext"
                                    >
                                        <b-form-group
                                            :label="$t('date') + ' ' + '*'"
                                        >
                                            <b-form-input
                                                :state="
                                                    getValidationState(
                                                        validationContext
                                                    )
                                                "
                                                aria-describedby="date-feedback"
                                                type="date"
                                                v-model="purchase.date"
                                            ></b-form-input>
                                            <b-form-invalid-feedback
                                                id="date-feedback"
                                                >{{
                                                    validationContext.errors[0]
                                                }}</b-form-invalid-feedback
                                            >
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>
                                <!-- Supplier -->
                                <b-col lg="4" md="4" sm="12" class="mb-3">
                                    <validation-provider
                                        name="Supplier"
                                        :rules="{ required: true }"
                                    >
                                        <b-form-group
                                            slot-scope="{ valid, errors }"
                                            :label="$t('Supplier') + ' ' + '*'"
                                        >
                                            <v-select
                                                :class="{
                                                    'is-invalid':
                                                        !!errors.length,
                                                }"
                                                :state="
                                                    errors[0]
                                                        ? false
                                                        : valid
                                                        ? true
                                                        : null
                                                "
                                                v-model="purchase.supplier_id"
                                                :reduce="(label) => label.value"
                                                :placeholder="
                                                    $t('Choose_Supplier')
                                                "
                                                :options="
                                                    suppliers.map(
                                                        (suppliers) => ({
                                                            label: suppliers.name,
                                                            value: suppliers.id,
                                                        })
                                                    )
                                                "
                                            />
                                            <b-form-invalid-feedback>{{
                                                errors[0]
                                            }}</b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <!-- warehouse -->
                                <b-col lg="4" md="4" sm="12" class="mb-3">
                                    <validation-provider
                                        name="warehouse"
                                        :rules="{ required: true }"
                                    >
                                        <b-form-group
                                            slot-scope="{ valid, errors }"
                                            :label="$t('warehouse') + ' ' + '*'"
                                        >
                                            <v-select
                                                :class="{
                                                    'is-invalid':
                                                        !!errors.length,
                                                }"
                                                :state="
                                                    errors[0]
                                                        ? false
                                                        : valid
                                                        ? true
                                                        : null
                                                "
                                                v-model="purchase.warehouse_id"
                                                :reduce="(label) => label.value"
                                                :placeholder="
                                                    $t('Choose_Warehouse')
                                                "
                                                :options="
                                                    warehouses.map(
                                                        (warehouses) => ({
                                                            label: warehouses.name,
                                                            value: warehouses.id,
                                                        })
                                                    )
                                                "
                                            />
                                            <b-form-invalid-feedback>{{
                                                errors[0]
                                            }}</b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <!-- Order Tax  -->
                                <b-col
                                    lg="3"
                                    md="4"
                                    sm="12"
                                    class="mb-3"
                                    v-if="
                                        currentUserPermissions &&
                                        currentUserPermissions.includes(
                                            'edit_tax_discount_shipping_purchase'
                                        )
                                    "
                                >
                                    <validation-provider
                                        name="Order Tax"
                                        :rules="{ regex: /^\d*\.?\d*$/ }"
                                        v-slot="validationContext"
                                    >
                                        <b-form-group :label="$t('OrderTax')">
                                            <b-input-group append="%">
                                                <b-form-input
                                                    :state="
                                                        getValidationState(
                                                            validationContext
                                                        )
                                                    "
                                                    aria-describedby="OrderTax-feedback"
                                                    label="Order Tax"
                                                    v-model.number="
                                                        purchase.tax_rate
                                                    "
                                                    @keyup="keyup_OrderTax()"
                                                ></b-form-input>
                                            </b-input-group>
                                            <b-form-invalid-feedback
                                                id="OrderTax-feedback"
                                                >{{
                                                    validationContext.errors[0]
                                                }}</b-form-invalid-feedback
                                            >
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <!-- Discount -->
                                <b-col
                                    lg="3"
                                    md="4"
                                    sm="12"
                                    class="mb-3"
                                    v-if="
                                        currentUserPermissions &&
                                        currentUserPermissions.includes(
                                            'edit_tax_discount_shipping_purchase'
                                        )
                                    "
                                >
                                    <validation-provider
                                        name="Discount"
                                        :rules="{ regex: /^\d*\.?\d*$/ }"
                                        v-slot="validationContext"
                                    >
                                        <b-form-group :label="$t('Discount')">
                                            <b-input-group
                                                :append="currentUser.currency"
                                            >
                                                <b-form-input
                                                    :state="
                                                        getValidationState(
                                                            validationContext
                                                        )
                                                    "
                                                    aria-describedby="Discount-feedback"
                                                    label="Discount"
                                                    v-model.number="
                                                        purchase.discount
                                                    "
                                                    @keyup="keyup_Discount()"
                                                ></b-form-input>
                                            </b-input-group>
                                            <b-form-invalid-feedback
                                                id="Discount-feedback"
                                                >{{
                                                    validationContext.errors[0]
                                                }}</b-form-invalid-feedback
                                            >
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <!-- Shipping  -->
                                <b-col
                                    lg="3"
                                    md="4"
                                    sm="12"
                                    class="mb-3"
                                    v-if="
                                        currentUserPermissions &&
                                        currentUserPermissions.includes(
                                            'edit_tax_discount_shipping_purchase'
                                        )
                                    "
                                >
                                    <validation-provider
                                        name="Shipping"
                                        :rules="{ regex: /^\d*\.?\d*$/ }"
                                        v-slot="validationContext"
                                    >
                                        <b-form-group :label="$t('Shipping')">
                                            <b-input-group
                                                :append="currentUser.currency"
                                            >
                                                <b-form-input
                                                    :state="
                                                        getValidationState(
                                                            validationContext
                                                        )
                                                    "
                                                    aria-describedby="Shipping-feedback"
                                                    label="Shipping"
                                                    v-model.number="
                                                        purchase.shipping
                                                    "
                                                    @keyup="keyup_Shipping()"
                                                ></b-form-input>
                                            </b-input-group>
                                            <b-form-invalid-feedback
                                                id="Shipping-feedback"
                                                >{{
                                                    validationContext.errors[0]
                                                }}</b-form-invalid-feedback
                                            >
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <!-- Status  -->
                                <b-col lg="3" md="4" sm="12" class="mb-3">
                                    <validation-provider
                                        name="Status"
                                        :rules="{ required: true }"
                                    >
                                        <b-form-group
                                            slot-scope="{ valid, errors }"
                                            :label="$t('Status') + ' ' + '*'"
                                        >
                                            <v-select
                                                :class="{
                                                    'is-invalid':
                                                        !!errors.length,
                                                }"
                                                :state="
                                                    errors[0]
                                                        ? false
                                                        : valid
                                                        ? true
                                                        : null
                                                "
                                                v-model="purchase.statut"
                                                :reduce="(label) => label.value"
                                                :placeholder="
                                                    $t('Choose_Status')
                                                "
                                                :options="[
                                                    {
                                                        label: 'received',
                                                        value: 'received',
                                                    },
                                                    {
                                                        label: 'pending',
                                                        value: 'pending',
                                                    },
                                                    {
                                                        label: 'შეკვეთილი',
                                                        value: 'ordered',
                                                    },
                                                ]"
                                            ></v-select>
                                            <b-form-invalid-feedback>{{
                                                errors[0]
                                            }}</b-form-invalid-feedback>
                                        </b-form-group>
                                    </validation-provider>
                                </b-col>

                                <!-- <div class="d-flex justify-space-between bg-warning w-100">
                                  <b-form-group label="Choose Csv File *">
                                        <input
                                            type="file"
                                            @change="onFileSelected"
                                            label="Choose Csv File"
                                        />
                                        <b-form-invalid-feedback
                                            id="File-feedback"
                                            class="d-block"
                                            >{{
                                                $t(
                                                    "field_must_be_in_csv_format"
                                                )
                                            }}</b-form-invalid-feedback
                                        >
                                    </b-form-group>

                                    <b-button
                                        :href="'/import/exemples/import_purchases.csv'"
                                        variant="info"
                                        size="sm"
                                        block
                                        class="ms-auto"
                                        style="width: fit-content;"
                                    >{{ $t("Download_exemple") }}
                                    </b-button>

                                </div> -->

                                <!--Csv File -->
                                <b-col lg="3" md="4" sm="12" class="mb-3">
                                    <b-form-group label="Choose Csv File *">
                                        <input
                                            type="file"
                                            @change="onFileSelected"
                                            label="Choose Csv File"
                                        />
                                        <b-form-invalid-feedback
                                            id="File-feedback"
                                            class="d-block"
                                            >{{
                                                $t(
                                                    "field_must_be_in_csv_format"
                                                )
                                            }}</b-form-invalid-feedback
                                        >
                                    </b-form-group>
                                </b-col>
                                <b-col lg="3" md="4" sm="12" class="mb-3">
                                </b-col>
                                <b-col lg="3" md="4" sm="12" class="mb-3">
                                </b-col>

                                <!-- Example csv -->
                                <b-col lg="3" md="4" sm="12" class="mb-3">
                                    <b-button
                                        :href="'/import/exemples/import_purchases.csv'"
                                        variant="info"
                                        size="sm"
                                        block
                                        class="btn-outline-success text-success"
                                        >{{ $t("Download_exemple") }}
                                        <svg
                                            width="21"
                                            height="20"
                                            viewBox="0 0 21 20"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M10.5 12.5V2.5"
                                                stroke="#0944AA"
                                                stroke-width="1.66667"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M18 12.5V15.8333C18 16.2754 17.8244 16.6993 17.5118 17.0118C17.1993 17.3244 16.7754 17.5 16.3333 17.5H4.66667C4.22464 17.5 3.80072 17.3244 3.48816 17.0118C3.17559 16.6993 3 16.2754 3 15.8333V12.5"
                                                stroke="#0944AA"
                                                stroke-width="1.66667"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M6.33301 8.33203L10.4997 12.4987L14.6663 8.33203"
                                                stroke="#0944AA"
                                                stroke-width="1.66667"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </b-button>
                                </b-col>

                                <b-col md="12">
                                    <b-form-group :label="$t('Note')">
                                        <textarea
                                            v-model="purchase.notes"
                                            rows="4"
                                            class="form-control"
                                            :placeholder="$t('Afewwords')"
                                        ></textarea>
                                    </b-form-group>
                                </b-col>
                                <b-col md="12">
                                    <b-form-group>
                                        <b-button
                                            variant="primary"
                                            @click="Submit_Purchase"
                                            :disabled="SubmitProcessing"
                                            style="background-color: #0944aa"
                                            class="px-5"
                                            >
                                            <!-- <i
                                                class="i-Yes me-2 font-weight-bold"
                                            ></i> -->
                                            {{ $t("submit") }}</b-button
                                        >
                                        <div
                                            v-once
                                            class="typo__p"
                                            v-if="SubmitProcessing"
                                        >
                                            <div
                                                class="spinner sm spinner-primary mt-3"
                                            ></div>
                                        </div>
                                    </b-form-group>
                                </b-col>
                            </b-row>
                        </b-card>
                    </b-col>
                </b-row>
            </b-form>
        </validation-observer>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";

export default {
    metaInfo: {
        title: "Import Purchase",
    },
    data() {
        return {
            isLoading: true,
            SubmitProcessing: false,
            Submit_Processing_detail: false,
            data: new FormData(),
            warehouses: [],
            suppliers: [],
            products: [],
            purchases: [],
            import_products: "",
            purchase: {
                id: "",
                date: new Date().toISOString().slice(0, 10),
                statut: "received",
                notes: "",
                supplier_id: "",
                warehouse_id: "",
                tax_rate: 0,
                TaxNet: 0,
                shipping: 0,
                discount: 0,
            },
        };
    },
    computed: {
        ...mapGetters(["currentUserPermissions", "currentUser"]),
    },

    methods: {
        //------------------------------ Event Import Purchases -------------------------------\\
        onFileSelected(e) {
            this.import_products = "";
            let file = e.target.files[0];
            this.import_products = file;
        },

        //--- Submit Validate Create Purchase
        Submit_Purchase() {
            this.$refs.create_purchase.validate().then((success) => {
                if (!success) {
                    this.makeToast(
                        "danger",
                        this.$t("Please_fill_the_form_correctly"),
                        this.$t("Failed")
                    );
                } else {
                    this.Create_Purchase();
                }
            });
        },

        //---Validate State Fields
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

        //------------------------------Formetted Numbers -------------------------\\
        formatNumber(number, dec) {
            const value = (
                typeof number === "string" ? number : number.toString()
            ).split(".");
            if (dec <= 0) return value[0];
            let formated = value[1] || "";
            if (formated.length > dec)
                return `${value[0]}.${formated.substr(0, dec)}`;
            while (formated.length < dec) formated += "0";
            return `${value[0]}.${formated}`;
        },

        //---------- keyup OrderTax
        keyup_OrderTax() {
            if (isNaN(this.purchase.tax_rate) || this.purchase.tax_rate == "") {
                this.purchase.tax_rate = 0;
            }
        },

        //---------- keyup Discount

        keyup_Discount() {
            if (isNaN(this.purchase.discount) || this.purchase.discount == "") {
                this.purchase.discount = 0;
            }
        },

        //---------- keyup Shipping

        keyup_Shipping() {
            if (isNaN(this.purchase.shipping) || this.purchase.shipping == "") {
                this.purchase.shipping = 0;
            }
        },

        //--------------------------------- Create Purchase -------------------------\\
        Create_Purchase() {
            this.SubmitProcessing = true;
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            var self = this;
            self.data.append("date", self.purchase.date);
            self.data.append("supplier_id", self.purchase.supplier_id);
            self.data.append("warehouse_id", self.purchase.warehouse_id);
            self.data.append("statut", self.purchase.statut);
            self.data.append("notes", self.purchase.notes);
            self.data.append("tax_rate", self.purchase.tax_rate);
            self.data.append("discount", self.purchase.discount);
            self.data.append("shipping", self.purchase.shipping);
            self.data.append("products", self.import_products);

            axios
                .post("store_import_purchases", self.data)
                .then((response) => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();

                    this.makeToast(
                        "success",
                        this.$t("Successfully_Imported"),
                        this.$t("Success")
                    );

                    this.SubmitProcessing = false;
                    this.$router.push({ name: "index_purchases" });
                })
                .catch((error) => {
                    NProgress.done();
                    this.makeToast(
                        "danger",
                        "An error occurred while processing the CSV file.",
                        this.$t("Failed")
                    );
                    this.SubmitProcessing = false;
                });
        },

        //---------------------------------------Get Elements Purchase ------------------------------\\
        GetElements() {
            axios
                .get("get_import_purchases")
                .then((response) => {
                    this.suppliers = response.data.suppliers;
                    this.warehouses = response.data.warehouses;
                    this.isLoading = false;
                })
                .catch((response) => {
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },
    },

    //-----------------------------  Created function-------------------
    created() {
        this.GetElements();
    },
};
</script>

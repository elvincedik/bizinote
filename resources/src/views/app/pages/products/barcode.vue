<template>
    <div class="main-content">
        <div class="bg-white mb-3 py-1 px-4">
            <p>Products / Print Labels</p>
            <h3 class="mb-0">Print Labels</h3>
        </div>
        <!-- <breadcumb :page="$t('Printbarcode')" :folder="$t('Products')"/> -->
        <div
            v-if="isLoading"
            class="loading_page spinner spinner-primary mr-3"
        ></div>
        <!-- <b-card v-if="!isLoading"> -->
        <div v-if="!isLoading" style="background-color: white !important">
            <b-modal
                hide-footer
                id="open_scan"
                size="md"
                title="Barcode Scanner"
            >
                <qrcode-scanner
                    :qrbox="250"
                    :fps="10"
                    style="width: 100%; height: calc(100vh - 56px)"
                    @result="onScan"
                />
            </b-modal>

            <!-- Warehouse -->
            <b-col md="6" class="mb-3">
                <validation-observer ref="show_Barcode">
                    <validation-provider
                        name="warehouse"
                        :rules="{ required: true }"
                    >
                        <b-form-group
                            slot-scope="{ valid, errors }"
                            :label="$t('warehouse') + ' ' + '*'"
                        >
                            <v-select
                                :class="{ 'is-invalid': !!errors.length }"
                                :state="errors[0] ? false : valid ? true : null"
                                @input="Selected_Warehouse"
                                v-model="barcode.warehouse_id"
                                :reduce="(label) => label.value"
                                :placeholder="$t('Choose_Warehouse')"
                                :options="
                                    warehouses.map((warehouses) => ({
                                        label: warehouses.name,
                                        value: warehouses.id,
                                    }))
                                "
                            />
                            <b-form-invalid-feedback>{{
                                errors[0]
                            }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>
                </validation-observer>
            </b-col>

            <!-- Product -->
            <b-col md="6" class="mb-5">
                <h6>{{ $t("ProductName") }}</h6>

                <div id="autocomplete" class="autocomplete">
                    <div class="input-with-icon">
                        <img
                            src="/assets_setup/image.png"
                            alt="Scan"
                            class="scan-icon"
                            @click="showModal"
                        />
                        <input
                            :placeholder="
                                $t('Scan_Search_Product_by_Code_Name')
                            "
                            @input="(e) => (search_input = e.target.value)"
                            @keyup="search(search_input)"
                            @focus="handleFocus"
                            @blur="handleBlur"
                            ref="product_autocomplete"
                            class="autocomplete-input"
                        />
                    </div>
                    <ul class="autocomplete-result-list" v-show="focused">
                        <li
                            class="autocomplete-result"
                            v-for="product_fil in product_filter"
                            @mousedown="SearchProduct(product_fil)"
                        >
                            {{ getResultValue(product_fil) }}
                        </li>
                    </ul>
                </div>
            </b-col>

            <!-- Details Product  -->
            <b-col md="12">
                <div class="table-responsive">
                    <table class="table table-hover table-md">
                        <thead>
                            <tr>
                                <th scope="col">{{ $t("ProductName") }}</th>
                                <th scope="col">{{ $t("CodeProduct") }}</th>
                                <th scope="col">{{ $t("Quantity") }}</th>
                                <th scope="col" class="text-center">
                                    <i class="fa fa-trash"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="products_added.length === 0">
                                <td colspan="4">{{ $t("NodataAvailable") }}</td>
                            </tr>
                            <tr
                                v-for="product in products_added"
                                :key="product.code"
                            >
                                <td>{{ product.name }}</td>
                                <td>{{ product.code }}</td>
                                <td>
                                    <input
                                        v-model.number="product.qte"
                                        class="form-control w-50 h-25"
                                        id="qte"
                                        type="number"
                                        name="qte"
                                    />
                                </td>
                                <td>
                                    <i
                                        @click="delete_Product(product.code)"
                                        class="i-Close-Window text-25 text-danger"
                                    ></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </b-col>

            <!-- Paper_size  -->
            <b-col md="12">
                <b-form-group :label="$t('Paper_size')">
                    <v-select
                        v-model="paper_size"
                        @input="Selected_Paper_size"
                        :reduce="(label) => label.value"
                        :placeholder="$t('Paper_size')"
                        :options="[
                            {
                                label: '40 per sheet (a4) (1.799 * 1.003)',
                                value: 'style40',
                            },
                            {
                                label: '30 per sheet (2.625 * 1)',
                                value: 'style30',
                            },
                            {
                                label: '24 per sheet (a4) (2.48 * 1.334)',
                                value: 'style24',
                            },
                            { label: '20 per sheet (4 * 1)', value: 'style20' },
                            {
                                label: '18 per sheet (a4) (2.5 * 1.835)',
                                value: 'style18',
                            },
                            {
                                label: '14 per sheet (4 * 1.33)',
                                value: 'style14',
                            },
                            {
                                label: '12 per sheet (a4) (2.5 * 2.834)',
                                value: 'style12',
                            },
                            { label: '10 per sheet (4 * 2)', value: 'style10' },
                            { label: 'Stickers', value: 'customstyle' },
                        ]"
                    ></v-select>
                </b-form-group>
            </b-col>

            <b-col md="6" class="mt-4">
                <label class="checkbox checkbox-primary mb-3"
                    ><input type="checkbox" v-model="show_price" />
                    <h5>Display Price</h5>
                    <span class="checkmark"></span
                ></label>
            </b-col>

            <b-col md="12" class="mt-4">
                <button
                    @click="submit()"
                    type="submit"
                    class="btn btn-primary btn-sm m-1"
                    style="background-color: #0944aa"
                                            
                >
                    {{ $t("Updat") }}
                    <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.6667 3C13.1063 3.00626 13.5256 3.18598 13.8333 3.5L17 6.66667C17.314 6.97438 17.4937 7.39372 17.5 7.83333V16.3333C17.5 16.7754 17.3244 17.1993 17.0118 17.5118C16.6993 17.8244 16.2754 18 15.8333 18H4.16667C3.72464 18 3.30072 17.8244 2.98816 17.5118C2.67559 17.1993 2.5 16.7754 2.5 16.3333V4.66667C2.5 4.22464 2.67559 3.80072 2.98816 3.48816C3.30072 3.17559 3.72464 3 4.16667 3H12.6667Z" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14.1668 17.9997V12.1663C14.1668 11.9453 14.079 11.7334 13.9228 11.5771C13.7665 11.4208 13.5545 11.333 13.3335 11.333H6.66683C6.44582 11.333 6.23385 11.4208 6.07757 11.5771C5.92129 11.7334 5.8335 11.9453 5.8335 12.1663V17.9997" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.8335 3V6.33333C5.8335 6.55435 5.92129 6.76631 6.07757 6.92259C6.23385 7.07887 6.44582 7.16667 6.66683 7.16667H12.5002" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <!-- <i class="i-Edit"></i> -->
                </button>
                <button
                    @click="reset()"
                    type="submit"
                    class="btn btn-sm m-1"
                    style="background-color: #8855FF;
                        color: white;
                        "
                >
                    {{ $t("Reset") }}
                    <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5 5.5H2.5" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.83333 10.5H2.5" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.83333 15.5H2.5" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.99984 15.5C10.5245 16.1996 11.2561 16.7164 12.0908 16.9771C12.9255 17.2379 13.8212 17.2294 14.6508 16.9528C15.4804 16.6763 16.202 16.1457 16.7133 15.4363C17.2247 14.7269 17.4998 13.8745 17.4998 13C17.4998 12.0054 17.1047 11.0516 16.4015 10.3483C15.6982 9.64509 14.7444 9.25 13.7498 9.25C12.6415 9.25 11.6332 9.7 10.9082 10.425L9.1665 12.1667" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.1665 8.83301V12.1663H12.4998" stroke="white" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                </button>
                <button
                    @click="print_all_Barcode()"
                    value="Print"
                    type="submit"
                    class="btn btn-light btn-sm btn-outline-secondary m-1"
                >
                {{ $t("print") }}
                <i class="i-Billing"></i>
                </button>
            </b-col>

            <b-col md="12">
                <div
                    class="barcode-row"
                    v-if="ShowCard"
                    id="print_barcode_label"
                >
                    <div v-for="(page, pageIndex) in pages" :key="pageIndex">
                        <div :class="class_type_page">
                            <div
                                class="barcode-item"
                                :class="class_sheet"
                                v-for="(barcode, index) in page"
                                :key="index"
                            >
                                <div
                                    class="head_barcode text-left"
                                    style="
                                        padding-left: 10px;
                                        font-weight: bold;
                                        font-size: 10px;
                                    "
                                >
                                    <span class="barcode-name">{{
                                        barcode.name
                                    }}</span>
                                    <span
                                        class="barcode-price"
                                        v-if="show_price"
                                        >{{ currentUser.currency }}
                                        {{ barcode.Net_price }}</span
                                    >
                                </div>
                                <barcode
                                    class="barcode"
                                    :format="barcode.Type_barcode"
                                    :value="barcode.barcode"
                                    textmargin="0"
                                    fontoptions="bold"
                                    fontSize="15"
                                    height="25"
                                    width="1"
                                ></barcode>
                            </div>
                        </div>
                    </div>
                </div>
            </b-col>
        </div>
    </div>
</template>

<script>
import VueBarcode from "vue-barcode";
import NProgress from "nprogress";
import { mapActions, mapGetters } from "vuex";

export default {
    components: {
        barcode: VueBarcode,
    },
    data() {
        return {
            focused: false,
            timer: null,
            search_input: "",
            product_filter: [],
            isLoading: true,
            ShowCard: false,
            barcode: {
                product_id: "",
                warehouse_id: "",
                qte: 10,
            },
            count: "",
            paper_size: "",
            sheets: "",
            total_a4: "",
            class_sheet: "",
            class_type_page: "",
            rest: "",
            warehouses: [],
            submitStatus: null,
            show_price: true,
            products_added: [],
            pages: [],
            products: [],
            product: {
                name: "",
                code: "",
                Type_barcode: "",
                barcode: "",
                Net_price: "",
            },
        };
    },

    computed: {
        ...mapGetters(["currentUser"]),
    },

    methods: {
        showModal() {
            this.$bvModal.show("open_scan");
        },

        onScan(decodedText, decodedResult) {
            const code = decodedText;
            this.search_input = code;
            this.search();
            this.$bvModal.hide("open_scan");
        },

        Per_Page() {
            this.total_a4 = parseInt(this.barcode.qte / this.sheets);
            this.rest = this.barcode.qte % this.sheets;
        },
        //---------------------- Event Selected_Paper_size------------------------------\\
        Selected_Paper_size(value) {
            if (value == "style40") {
                this.sheets = 40;
                this.class_sheet = "style40";
                this.class_type_page = "barcodea4";
            } else if (value == "style30") {
                this.sheets = 30;
                this.class_type_page = "barcode_non_a4";
                this.class_sheet = "style30";
            } else if (value == "style24") {
                this.sheets = 24;
                this.class_sheet = "style24";
                this.class_type_page = "barcodea4";
            } else if (value == "style20") {
                this.sheets = 20;
                this.class_sheet = "style20";
                this.class_type_page = "barcode_non_a4";
            } else if (value == "style18") {
                this.sheets = 18;
                this.class_sheet = "style18";
                this.class_type_page = "barcodea4";
            } else if (value == "style14") {
                this.sheets = 14;
                this.class_sheet = "style14";
                this.class_type_page = "barcode_non_a4";
            } else if (value == "style12") {
                this.sheets = 12;
                this.class_sheet = "style12";
                this.class_type_page = "barcodea4";
            } else if (value == "style10") {
                this.sheets = 10;
                this.class_sheet = "style10";
                this.class_type_page = "barcode_non_a4";
            } else if (value == "customstyle") {
                this.sheets = 1;
                this.class_sheet = "customstyle";
                this.class_type_page = "barcode_custom";
            }

            this.Per_Page();
        },
        //------ Validate Form
        submit() {
            this.$refs.show_Barcode.validate().then((success) => {
                if (!success) {
                    return;
                } else {
                    this.showBarcode();
                    // this.Per_Page();
                }
            });
        },
        //---Validate State Fields
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        handleFocus() {
            this.focused = true;
        },
        handleBlur() {
            this.focused = false;
        },

        //-----------------------------------Delete Product ------------------------------\\
        delete_Product(code) {
            for (var i = 0; i < this.products_added.length; i++) {
                if (code === this.products_added[i].code) {
                    this.products_added.splice(i, 1);
                }
            }
        },

        // Search Products
        search() {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
            if (this.search_input.length < 2) {
                return (this.product_filter = []);
            }
            if (
                this.barcode.warehouse_id != "" &&
                this.barcode.warehouse_id != null
            ) {
                this.timer = setTimeout(() => {
                    const product_filter = this.products.filter(
                        (product) =>
                            product.code === this.search_input ||
                            product.barcode.includes(this.search_input)
                    );
                    if (product_filter.length === 1) {
                        this.SearchProduct(product_filter[0]);
                    } else {
                        let tokens = this.search_input.toLowerCase().split(" ");
                        this.product_filter = this.products.filter(
                            (product) => {
                                return tokens.every(
                                    (token) =>
                                        product.name
                                            .toLowerCase()
                                            .includes(token) ||
                                        product.code
                                            .toLowerCase()
                                            .includes(token) ||
                                        product.barcode
                                            .toLowerCase()
                                            .includes(token) ||
                                        (product.note &&
                                            product.note
                                                .toLowerCase()
                                                .includes(token))
                                );
                                // this.product_filter=  this.products.filter(product => {
                                //   return (
                                //     product.name.toLowerCase().includes(this.search_input.toLowerCase()) ||
                                //     product.code.toLowerCase().includes(this.search_input.toLowerCase()) ||
                                //     product.barcode.toLowerCase().includes(this.search_input.toLowerCase())
                                //     );
                            }
                        );
                        // Check if product_filter is empty and show alert
                        if (this.product_filter.length <= 0) {
                            this.makeToast(
                                "warning",
                                "Product Not Found",
                                "Warning"
                            );
                        }
                    }
                }, 800);
            } else {
                this.makeToast(
                    "warning",
                    this.$t("SelectWarehouse"),
                    this.$t("Warning")
                );
            }
        },
        //------ Search Result value
        getResultValue(result) {
            return result.code + " " + "(" + result.name + ")";
        },

        //------ Submit Search Product
        SearchProduct(result) {
            const existingProduct = this.products_added.find(
                (product) => product.code === result.code
            );

            if (existingProduct) {
                this.makeToast(
                    "warning",
                    this.$t("AlreadyAdd"),
                    this.$t("Warning")
                );
            } else {
                this.products_added.push({
                    code: result.code,
                    barcode: result.barcode,
                    name: result.name,
                    Type_barcode: result.Type_barcode,
                    Net_price: result.Net_price,
                    qte: 1, // Default quantity
                });
            }

            this.search_input = "";
            this.$refs.product_autocomplete.value = "";
            this.product_filter = [];
        },
        //------ Toast
        makeToast(variant, msg, title) {
            this.$root.$bvToast.toast(msg, {
                title: title,
                variant: variant,
                solid: true,
            });
        },
        //------------------------------------ Get Products By Warehouse -------------------------\\
        Get_Products_By_Warehouse(id) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            axios
                .get(
                    "get_Products_by_warehouse/" +
                        id +
                        "?stock=" +
                        0 +
                        "&product_service=" +
                        1 +
                        "&product_combo=" +
                        1
                )
                .then((response) => {
                    this.products = response.data;
                    NProgress.done();
                })
                .catch((error) => {});
        },
        //-------------------------------------- Print Barcode -------------------------\\
        print_all_Barcode() {
            var divContents = document.getElementById(
                "print_barcode_label"
            ).innerHTML;
            var a = window.open("", "", "height=500, width=500");
            a.document.write(
                '<link rel="stylesheet" href="/assets_setup/css/print_label.css"><html>'
            );
            a.document.write("<body >");
            a.document.write(divContents);
            a.document.write("</body></html>");
            a.document.close();

            setTimeout(() => {
                a.print();
            }, 1000);
        },

        generatePages() {
            let allBarcodes = [];
            this.products_added.forEach((product) => {
                for (let i = 0; i < product.qte; i++) {
                    allBarcodes.push({
                        name: product.name,
                        barcode: product.barcode,
                        Type_barcode: product.Type_barcode,
                        Net_price: product.Net_price,
                    });
                }
            });

            this.pages = [];
            while (allBarcodes.length > 0) {
                this.pages.push(allBarcodes.splice(0, this.sheets));
            }
        },

        //-------------------------------------- Show Barcode -------------------------\\
        showBarcode() {
            // this.Per_Page();
            // this.count = this.barcode.qte;
            this.generatePages();
            this.ShowCard = true;
        },
        //---------------------- Event Select Warehouse ------------------------------\\
        Selected_Warehouse(value) {
            this.search_input = "";
            this.product_filter = [];
            this.Get_Products_By_Warehouse(value);
        },
        //----------------------------------- GET Barcode Elements -------------------------\\
        Get_Elements: function () {
            axios
                .get("barcode_create_page")
                .then((response) => {
                    this.warehouses = response.data.warehouses;
                    this.isLoading = false;
                })
                .catch((response) => {
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },
        //----------------------------------- Reset Data -------------------------\\
        reset() {
            this.ShowCard = false;
            this.products = [];
            this.product.name = "";
            this.product.code = "";
            this.product.Net_price = "";
            this.barcode.qte = 10;
            this.count = 10;
            this.barcode.warehouse_id = "";
            this.search_input = "";
            this.$refs.product_autocomplete.value = "";
            this.product_filter = [];
        },
    }, //end Methods
    //-----------------------------Created function-------------------
    created: function () {
        this.Get_Elements();
    },
};
</script>

<style>
.input-with-icon {
    display: flex;
    align-items: center;
}

.scan-icon {
    width: 50px; /* Adjust size as needed */
    height: 50px;
    margin-right: 8px; /* Adjust spacing as needed */
    cursor: pointer;
}
</style>

<?php

namespace App\Http\Controllers;

use Twilio\Rest\Client as Client_Twilio;
use GuzzleHttp\Client as Client_guzzle;
use GuzzleHttp\Client as Client_termi;
use App\Models\SMSMessage;
use Infobip\Api\SendSmsApi;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Illuminate\Support\Str;
use App\Models\EmailMessage;
use App\Mail\CustomEmail;
use App\Models\Account;

use App\Models\PaymentMethod;
use App\Mail\SaleMail;
use App\Models\Client;
use App\Models\Unit;
use App\Models\PaymentSale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Quotation;
use App\Models\Shipment;
use App\Models\sms_gateway;
use App\Models\Role;
use App\Models\SaleReturn;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\PosSetting;
use App\Models\User;
use App\Models\UserWarehouse;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Stripe;
use App\Models\PaymentWithCreditCard;
use DB;
use PDF;
use ArPHP\I18N\Arabic;

class SalesController extends BaseController
{

    //------------- GET ALL SALES -----------\\

    public function index(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Sale::class);

        $user = Auth::user();
        $organizationId = $user->organization_id;

        $role = $user->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;

        $helpers = new helpers();

        $param = ['like', 'like', '=', 'like', '=', '=', 'like'];
        $columns = ['Ref', 'statut', 'client_id', 'payment_statut', 'warehouse_id', 'date', 'shipping_status'];
        $data = [];

        $Sales = Sale::with('facture', 'client', 'warehouse', 'user')
            ->where('deleted_at', '=', null)
            ->where('organization_id', $organizationId)
            ->where(function ($query) use ($view_records, $user) {
                if (!$view_records) {
                    return $query->where('user_id', '=', $user->id);
                }
            });

        $Filtred = $helpers->filter($Sales, $columns, $param, $request)
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('statut', 'LIKE', "%{$request->search}%")
                        ->orWhere('GrandTotal', $request->search)
                        ->orWhere('payment_statut', 'like', "%{$request->search}%")
                        ->orWhere('shipping_status', 'like', "%{$request->search}%")
                        ->orWhereHas('client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        })
                        ->orWhereHas('warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                });
            });

        $totalRows = $Filtred->count();
        if ($perPage == "-1") {
            $perPage = $totalRows;
        }

        $Sales = $Filtred->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($Sales as $Sale) {
            $item['id'] = $Sale['id'];
            $item['date'] = $Sale['date'] . ' ' . $Sale['time'];
            $item['Ref'] = $Sale['Ref'];
            $item['created_by'] = $Sale['user']->username;
            $item['statut'] = $Sale['statut'];
            $item['shipping_status'] = $Sale['shipping_status'];
            $item['discount'] = $Sale['discount'];
            $item['shipping'] = $Sale['shipping'];
            $item['warehouse_name'] = $Sale['warehouse']['name'];
            $item['client_id'] = $Sale['client']['id'];
            $item['client_name'] = $Sale['client']['name'];
            $item['client_email'] = $Sale['client']['email'];
            $item['client_tele'] = $Sale['client']['phone'];
            $item['client_code'] = $Sale['client']['code'];
            $item['client_adr'] = $Sale['client']['adresse'];
            $item['GrandTotal'] = number_format($Sale['GrandTotal'], 2, '.', '');
            $item['paid_amount'] = number_format($Sale['paid_amount'], 2, '.', '');
            $item['due'] = number_format($item['GrandTotal'] - $item['paid_amount'], 2, '.', '');
            $item['payment_status'] = $Sale['payment_statut'];

            if (SaleReturn::where('sale_id', $Sale['id'])->where('deleted_at', '=', null)->exists()) {
                $sellReturn = SaleReturn::where('sale_id', $Sale['id'])->where('deleted_at', '=', null)->first();
                $item['salereturn_id'] = $sellReturn->id;
                $item['sale_has_return'] = 'yes';
            } else {
                $item['sale_has_return'] = 'no';
            }

            $data[] = $item;
        }

        $stripe_key = config('app.STRIPE_KEY');

        $customers = Client::where('deleted_at', '=', null)
            ->where('organization_id', $organizationId)
            ->get(['id', 'name']);

        $accounts = Account::where('deleted_at', '=', null)
            ->where('organization_id', $organizationId)
            ->orderBy('id', 'desc')
            ->get(['id', 'account_name']);

        $payment_methods = PaymentMethod::whereNull('deleted_at')->get(['id', 'name']);

        if ($user->is_all_warehouses) {
            $warehouses = Warehouse::where('deleted_at', '=', null)
                ->where('organization_id', $organizationId)
                ->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('deleted_at', '=', null)
                ->where('organization_id', $organizationId)
                ->whereIn('id', $warehouses_id)
                ->get(['id', 'name']);
        }

        return response()->json([
            'stripe_key' => $stripe_key,
            'totalRows' => $totalRows,
            'sales' => $data,
            'customers' => $customers,
            'warehouses' => $warehouses,
            'accounts' => $accounts,
            'payment_methods' => $payment_methods,
        ]);
    }


    //------------- STORE NEW SALE-----------\\

    public function store(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'create', Sale::class);

        request()->validate([
            'client_id' => 'required',
            'warehouse_id' => 'required',
        ]);

        \DB::transaction(function () use ($request) {
            $helpers = new helpers();
            $order = new Sale;

            $order->organization_id = auth()->user()->organization_id;
            $order->is_pos = 0;
            $order->date = $request->date;
            $order->time = now()->toTimeString();
            $order->Ref = $this->getNumberOrder();
            $order->client_id = $request->client_id;
            $order->GrandTotal = $request->GrandTotal;
            $order->warehouse_id = $request->warehouse_id;
            $order->tax_rate = $request->tax_rate;
            $order->TaxNet = $request->TaxNet;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->statut = $request->statut;
            $order->payment_statut = 'unpaid';
            $order->notes = $request->notes;
            $order->user_id = Auth::user()->id;
            $order->save();

            $data = $request['details'];
            foreach ($data as $key => $value) {
                $unit = Unit::where('id', $value['sale_unit_id'])->first();
                $orderDetails[] = [
                    'date' => $request->date,
                    'sale_id' => $order->id,
                    'sale_unit_id' => $value['sale_unit_id'] ?? NULL,
                    'quantity' => $value['quantity'],
                    'price' => $value['Unit_price'],
                    'TaxNet' => $value['tax_percent'],
                    'tax_method' => $value['tax_method'],
                    'discount' => $value['discount'],
                    'discount_method' => $value['discount_Method'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'] ?? NULL,
                    'total' => $value['subtotal'],
                    'imei_number' => $value['imei_number'],
                ];

                if ($order->statut == "completed") {
                    $productQuery = product_warehouse::where('deleted_at', '=', null)
                        ->where('warehouse_id', $order->warehouse_id)
                        ->where('product_id', $value['product_id']);

                    if (!empty($value['product_variant_id'])) {
                        $productQuery->where('product_variant_id', $value['product_variant_id']);
                    }

                    $product_warehouse = $productQuery->first();

                    if ($unit && $product_warehouse) {
                        $adjustment = $value['quantity'] * $unit->operator_value;
                        if ($unit->operator == '/') {
                            $adjustment = $value['quantity'] / $unit->operator_value;
                        }
                        $product_warehouse->qte -= $adjustment;
                        $product_warehouse->save();
                    }
                }
            }

            SaleDetail::insert($orderDetails);

            $role = Auth::user()->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');

            if ($request->payment['status'] != 'pending') {
                $sale = Sale::where('organization_id', auth()->user()->organization_id)->findOrFail($order->id);

                if (!$view_records) {
                    $this->authorizeForUser($request->user('api'), 'check_record', $sale);
                }

                try {
                    $total_paid = $sale->paid_amount + $request['amount'];
                    $due = $sale->GrandTotal - $total_paid;

                    $payment_statut = 'unpaid';
                    if ($due <= 0.0) {
                        $payment_statut = 'paid';
                    } elseif ($due < $sale->GrandTotal) {
                        $payment_statut = 'partial';
                    }

                    if ($request['amount'] > 0 && $request->payment['status'] != 'pending') {
                        if ((int)$request->payment['payment_method_id'] === 1) {
                            $Client = Client::where('organization_id', auth()->user()->organization_id)
                                ->whereId($request->client_id)->first();

                            Stripe\Stripe::setApiKey(config('app.STRIPE_SECRET'));

                            $PaymentWithCreditCard = PaymentWithCreditCard::where('customer_id', $request->client_id)->first();
                            if (!$PaymentWithCreditCard) {
                                $customer = \Stripe\Customer::create([
                                    'source' => $request->token,
                                    'email' => $Client->email,
                                    'name' => $Client->name,
                                ]);
                                $charge = \Stripe\Charge::create([
                                    'amount' => $request['amount'] * 100,
                                    'currency' => 'usd',
                                    'customer' => $customer->id,
                                ]);
                                $PaymentCard['customer_stripe_id'] = $customer->id;
                            } else {
                                $customer_id = $PaymentWithCreditCard->customer_stripe_id;
                                $card_id = $request->card_id;

                                if ($request->is_new_credit_card) {
                                    $customer = \Stripe\Customer::retrieve($customer_id);
                                    $card = \Stripe\Customer::createSource($customer_id, ['source' => $request->token]);
                                    $charge = \Stripe\Charge::create([
                                        'amount' => $request['amount'] * 100,
                                        'currency' => 'usd',
                                        'customer' => $customer_id,
                                        'source' => $card->id,
                                    ]);
                                } else {
                                    $charge = \Stripe\Charge::create([
                                        'amount' => $request['amount'] * 100,
                                        'currency' => 'usd',
                                        'customer' => $customer_id,
                                        'source' => $card_id,
                                    ]);
                                }
                                $PaymentCard['customer_stripe_id'] = $customer_id;
                            }

                            $PaymentSale = new PaymentSale();
                            $PaymentSale->sale_id = $order->id;
                            $PaymentSale->Ref = app('App\Http\Controllers\PaymentSalesController')->getNumberOrder();
                            $PaymentSale->date = Carbon::now();
                            $PaymentSale->payment_method_id = $request->payment['payment_method_id'];
                            $PaymentSale->montant = $request['amount'];
                            $PaymentSale->change = $request['change'];
                            $PaymentSale->notes = NULL;
                            $PaymentSale->user_id = Auth::user()->id;
                            $PaymentSale->account_id = $request->payment['account_id'] ?? NULL;
                            $PaymentSale->save();

                            if ($account = Account::where('organization_id', auth()->user()->organization_id)
                                ->find($request->payment['account_id'])
                            ) {
                                $account->update(['balance' => $account->balance + $request['amount']]);
                            }

                            $sale->update([
                                'paid_amount' => $total_paid,
                                'payment_statut' => $payment_statut,
                            ]);

                            $PaymentCard['customer_id'] = $request->client_id;
                            $PaymentCard['payment_id'] = $PaymentSale->id;
                            $PaymentCard['charge_id'] = $charge->id;
                            $PaymentCard['organization_id'] = auth()->user()->organization_id;
                            PaymentWithCreditCard::create($PaymentCard);
                        } else {
                            PaymentSale::create([
                                'sale_id' => $order->id,
                                'Ref' => app('App\Http\Controllers\PaymentSalesController')->getNumberOrder(),
                                'date' => Carbon::now(),
                                'account_id' => $request->payment['account_id'] ?? NULL,
                                'payment_method_id' => $request->payment['payment_method_id'],
                                'montant' => $request['amount'],
                                'change' => $request['change'],
                                'notes' => NULL,
                                'user_id' => Auth::user()->id,
                            ]);

                            if ($account = Account::where('organization_id', auth()->user()->organization_id)
                                ->find($request->payment['account_id'])
                            ) {
                                $account->update(['balance' => $account->balance + $request['amount']]);
                            }

                            $sale->update([
                                'paid_amount' => $total_paid,
                                'payment_statut' => $payment_statut,
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            }
        }, 10);

        return response()->json(['success' => true]);
    }



    //------------- UPDATE SALE -----------

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Sale::class);

        request()->validate([
            'warehouse_id' => 'required',
            'client_id' => 'required',
        ]);

        \DB::transaction(function () use ($request, $id) {
            $user = auth()->user();
            $organization_id = $user->organization_id;

            $role = $user->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');

            $current_Sale = Sale::where('organization_id', $organization_id)->findOrFail($id);

            if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
                return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
            } else {
                if (!$view_records) {
                    $this->authorizeForUser($request->user('api'), 'check_record', $current_Sale);
                }

                $old_sale_details = SaleDetail::where('sale_id', $id)
                    ->where('organization_id', $organization_id)
                    ->get();

                $new_sale_details = $request['details'];
                $length = sizeof($new_sale_details);

                $new_products_id = [];
                foreach ($new_sale_details as $new_detail) {
                    $new_products_id[] = $new_detail['id'];
                }

                $old_products_id = [];
                foreach ($old_sale_details as $key => $value) {
                    $old_products_id[] = $value->id;

                    if ($value['sale_unit_id'] !== null) {
                        $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                    } else {
                        $product_unit_sale_id = Product::with('unitSale')
                            ->where('id', $value['product_id'])
                            ->first();

                        if ($product_unit_sale_id && $product_unit_sale_id['unitSale']) {
                            $old_unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                        } else {
                            $old_unit = null;
                        }
                    }

                    if ($current_Sale->statut == "completed") {
                        if ($value['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->where('organization_id', $organization_id)
                                ->first();
                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('organization_id', $organization_id)
                                ->first();
                        }

                        if ($product_warehouse && $old_unit) {
                            if ($old_unit->operator == '/') {
                                $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                            } else {
                                $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                            }
                            $product_warehouse->save();
                        }
                    }

                    if (!in_array($old_products_id[$key], $new_products_id)) {
                        $SaleDetail = SaleDetail::where('id', $value->id)
                            ->where('organization_id', $organization_id)
                            ->firstOrFail();
                        $SaleDetail->delete();
                    }
                }

                foreach ($new_sale_details as $prd => $prod_detail) {
                    $get_type_product = Product::where('id', $prod_detail['product_id'])->first()->type;

                    if ($prod_detail['sale_unit_id'] !== null || $get_type_product == 'is_service') {
                        $unit_prod = Unit::where('id', $prod_detail['sale_unit_id'])->first();

                        if ($request['statut'] == "completed") {
                            if ($prod_detail['product_variant_id'] !== null) {
                                $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                    ->where('warehouse_id', $request->warehouse_id)
                                    ->where('product_id', $prod_detail['product_id'])
                                    ->where('product_variant_id', $prod_detail['product_variant_id'])
                                    ->where('organization_id', $organization_id)
                                    ->first();
                            } else {
                                $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                    ->where('warehouse_id', $request->warehouse_id)
                                    ->where('product_id', $prod_detail['product_id'])
                                    ->where('organization_id', $organization_id)
                                    ->first();
                            }

                            if ($product_warehouse && $unit_prod) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }

                        $orderDetails = [
                            'sale_id'              => $id,
                            'date'                 => $request['date'],
                            'price'                => $prod_detail['Unit_price'],
                            'sale_unit_id'         => $unit_prod ? $unit_prod->id : null,
                            'TaxNet'               => $prod_detail['tax_percent'],
                            'tax_method'           => $prod_detail['tax_method'],
                            'discount'             => $prod_detail['discount'],
                            'discount_method'      => $prod_detail['discount_Method'],
                            'quantity'             => $prod_detail['quantity'],
                            'product_id'           => $prod_detail['product_id'],
                            'product_variant_id'   => $prod_detail['product_variant_id'],
                            'total'                => $prod_detail['subtotal'],
                            'imei_number'          => $prod_detail['imei_number'],
                            'organization_id'      => $organization_id,
                        ];

                        if (!in_array($prod_detail['id'], $old_products_id)) {
                            SaleDetail::create($orderDetails);
                        } else {
                            SaleDetail::where('id', $prod_detail['id'])
                                ->where('organization_id', $organization_id)
                                ->update($orderDetails);
                        }
                    }
                }

                $due = $request['GrandTotal'] - $current_Sale->paid_amount;
                if ($due === 0.0 || $due < 0.0) {
                    $payment_statut = 'paid';
                } else if ($due != $request['GrandTotal']) {
                    $payment_statut = 'partial';
                } else {
                    $payment_statut = 'unpaid';
                }

                $current_Sale->update([
                    'date'            => $request['date'],
                    'client_id'       => $request['client_id'],
                    'warehouse_id'    => $request['warehouse_id'],
                    'notes'           => $request['notes'],
                    'statut'          => $request['statut'],
                    'tax_rate'        => $request['tax_rate'],
                    'TaxNet'          => $request['TaxNet'],
                    'discount'        => $request['discount'],
                    'shipping'        => $request['shipping'],
                    'GrandTotal'      => $request['GrandTotal'],
                    'payment_statut'  => $payment_statut,
                    'organization_id' => $organization_id,
                ]);
            }
        }, 10);

        return response()->json(['success' => true]);
    }


    //------------- Remove SALE BY ID -----------\\

    public function destroy(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Sale::class);

        \DB::transaction(function () use ($id, $request) {
            $role = Auth::user()->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $organizationId = auth()->user()->organization_id;

            $current_Sale = Sale::where('organization_id', $organizationId)->findOrFail($id);
            $old_sale_details = SaleDetail::where('sale_id', $id)->get();
            $shipment_data = Shipment::where('sale_id', $id)->first();

            if (SaleReturn::where('sale_id', $id)->whereNull('deleted_at')->exists()) {
                return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
            }

            if (!$view_records) {
                $this->authorizeForUser($request->user('api'), 'check_record', $current_Sale);
            }

            foreach ($old_sale_details as $value) {
                $old_unit = $value->sale_unit_id !== null
                    ? Unit::find($value->sale_unit_id)
                    : optional(Product::with('unitSale')->find($value->product_id))->unitSale;

                if ($current_Sale->statut == "completed") {
                    $query = product_warehouse::whereNull('deleted_at')
                        ->where('warehouse_id', $current_Sale->warehouse_id)
                        ->where('product_id', $value['product_id']);

                    if ($value['product_variant_id'] !== null) {
                        $query->where('product_variant_id', $value['product_variant_id']);
                    }

                    $product_warehouse = $query->first();
                    if ($product_warehouse && $old_unit) {
                        $product_warehouse->qte += $old_unit->operator == '/'
                            ? $value['quantity'] / $old_unit->operator_value
                            : $value['quantity'] * $old_unit->operator_value;
                        $product_warehouse->save();
                    }
                }
            }

            if ($shipment_data) {
                $shipment_data->delete();
            }

            $current_Sale->details()->delete();
            $current_Sale->update([
                'deleted_at' => Carbon::now(),
                'shipping_status' => null,
            ]);

            $Payment_Sale_data = PaymentSale::where('sale_id', $id)->get();
            foreach ($Payment_Sale_data as $Payment_Sale) {
                if ((int)$Payment_Sale->payment_method_id === 1) {
                    optional(PaymentWithCreditCard::where('payment_id', $Payment_Sale->id)->first())->delete();
                }

                $account = Account::find($Payment_Sale->account_id);
                if ($account) {
                    $account->update(['balance' => $account->balance - $Payment_Sale->montant]);
                }

                $Payment_Sale->delete();
            }
        }, 10);

        return response()->json(['success' => true]);
    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Sale::class);

        \DB::transaction(function () use ($request) {
            $role = Auth::user()->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $organizationId = auth()->user()->organization_id;
            $selectedIds = $request->selectedIds;

            foreach ($selectedIds as $sale_id) {
                if (SaleReturn::where('sale_id', $sale_id)->whereNull('deleted_at')->exists()) {
                    return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
                }

                $current_Sale = Sale::where('organization_id', $organizationId)->findOrFail($sale_id);
                $old_sale_details = SaleDetail::where('sale_id', $sale_id)->get();
                $shipment_data = Shipment::where('sale_id', $sale_id)->first();

                if (!$view_records) {
                    $this->authorizeForUser($request->user('api'), 'check_record', $current_Sale);
                }

                foreach ($old_sale_details as $value) {
                    $old_unit = $value->sale_unit_id !== null
                        ? Unit::find($value->sale_unit_id)
                        : optional(Product::with('unitSale')->find($value->product_id))->unitSale;

                    if ($current_Sale->statut == "completed") {
                        $query = product_warehouse::whereNull('deleted_at')
                            ->where('warehouse_id', $current_Sale->warehouse_id)
                            ->where('product_id', $value['product_id']);

                        if ($value['product_variant_id'] !== null) {
                            $query->where('product_variant_id', $value['product_variant_id']);
                        }

                        $product_warehouse = $query->first();
                        if ($product_warehouse && $old_unit) {
                            $product_warehouse->qte += $old_unit->operator == '/'
                                ? $value['quantity'] / $old_unit->operator_value
                                : $value['quantity'] * $old_unit->operator_value;
                            $product_warehouse->save();
                        }
                    }
                }

                if ($shipment_data) {
                    $shipment_data->delete();
                }

                $current_Sale->details()->delete();
                $current_Sale->update([
                    'deleted_at' => Carbon::now(),
                    'shipping_status' => null,
                ]);

                $Payment_Sale_data = PaymentSale::where('sale_id', $sale_id)->get();
                foreach ($Payment_Sale_data as $Payment_Sale) {
                    if ((int)$Payment_Sale->payment_method_id === 1) {
                        optional(PaymentWithCreditCard::where('payment_id', $Payment_Sale->id)->first())->delete();
                    }

                    $account = Account::find($Payment_Sale->account_id);
                    if ($account) {
                        $account->update(['balance' => $account->balance - $Payment_Sale->montant]);
                    }

                    $Payment_Sale->delete();
                }
            }
        }, 10);

        return response()->json(['success' => true]);
    }



    //---------------- Get Details Sale-----------------\\

    public function show(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'view', Sale::class);
        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $sale_data = Sale::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = array();

        if (!$view_records) {
            $this->authorizeForUser($request->user('api'), 'check_record', $sale_data);
        }

        $sale_details['Ref'] = $sale_data->Ref;
        $sale_details['date'] = $sale_data->date . ' ' . $sale_data->time;
        $sale_details['note'] = $sale_data->notes;
        $sale_details['statut'] = $sale_data->statut;
        $sale_details['warehouse'] = $sale_data['warehouse']->name;
        $sale_details['discount'] = $sale_data->discount;
        $sale_details['shipping'] = $sale_data->shipping;
        $sale_details['tax_rate'] = $sale_data->tax_rate;
        $sale_details['TaxNet'] = $sale_data->TaxNet;
        $sale_details['client_name'] = $sale_data['client']->name;
        $sale_details['client_phone'] = $sale_data['client']->phone;
        $sale_details['client_adr'] = $sale_data['client']->adresse;
        $sale_details['client_email'] = $sale_data['client']->email;
        $sale_details['client_tax'] = $sale_data['client']->tax_number;
        $sale_details['GrandTotal'] = number_format($sale_data->GrandTotal, 2, '.', '');
        $sale_details['paid_amount'] = number_format($sale_data->paid_amount, 2, '.', '');
        $sale_details['due'] = number_format($sale_details['GrandTotal'] - $sale_details['paid_amount'], 2, '.', '');
        $sale_details['payment_status'] = $sale_data->payment_statut;

        if (SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->exists()) {
            $sellReturn = SaleReturn::where('sale_id', $id)->where('deleted_at', '=', null)->first();
            $sale_details['salereturn_id'] = $sellReturn->id;
            $sale_details['sale_has_return'] = 'yes';
        } else {
            $sale_details['sale_has_return'] = 'no';
        }

        foreach ($sale_data['details'] as $detail) {
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = $product_unit_sale_id['unitSale'] ? Unit::where('id', $product_unit_sale_id['unitSale']->id)->first() : NULL;
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();
                $data['code'] = $productsVariants->code;
                $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
            }

            $data['quantity'] = $detail->quantity;
            $data['total'] = $detail->total;
            $data['price'] = $detail->price;
            $data['unit_sale'] = $unit ? $unit->ShortName : '';

            $data['DiscountNet'] = ($detail->discount_method == '2')
                ? $detail->discount
                : $detail->price * $detail->discount / 100;

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
            $data['Unit_price'] = $detail->price;
            $data['discount'] = $detail->discount;

            if ($detail->tax_method == '1') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = $tax_price;
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet'] - $tax_price);
                $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
            }

            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $details[] = $data;
        }

        $company = Setting::where('deleted_at', '=', null)->first();

        return response()->json([
            'details' => $details,
            'sale' => $sale_details,
            'company' => $company,
        ]);
    }

    //-------------- Print Invoice ---------------\\

    public function Print_Invoice_POS(Request $request, $id)
    {
        $user = auth('api')->user();
        $helpers = new helpers();
        $details = array();

        $sale = Sale::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->where('organization_id', $user->organization_id)
            ->findOrFail($id);

        $item['id'] = $sale->id;
        $item['Ref'] = $sale->Ref;
        $item['date'] = $sale->date . ' ' . $sale->time;
        $item['discount'] = number_format($sale->discount, 2, '.', '');
        $item['shipping'] = number_format($sale->shipping, 2, '.', '');
        $item['taxe'] = number_format($sale->TaxNet, 2, '.', '');
        $item['tax_rate'] = $sale->tax_rate;
        $item['client_name'] = $sale['client']->name;
        $item['warehouse_name'] = $sale['warehouse']->name;
        $item['GrandTotal'] = number_format($sale->GrandTotal, 2, '.', '');
        $item['paid_amount'] = number_format($sale->paid_amount, 2, '.', '');

        foreach ($sale['details'] as $detail) {
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)
                    ->where('organization_id', $user->organization_id)
                    ->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->where('organization_id', $user->organization_id)
                    ->first();
                $unit = $product_unit_sale_id && $product_unit_sale_id->unitSale
                    ? Unit::where('id', $product_unit_sale_id->unitSale->id)
                    ->where('organization_id', $user->organization_id)
                    ->first()
                    : null;
            }

            if ($detail->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)
                    ->where('organization_id', $user->organization_id)
                    ->first();

                $data['code'] = $productsVariants->code;
                $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];
            } else {
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
            }

            $data['quantity'] = number_format($detail->quantity, 2, '.', '');
            $data['total'] = number_format($detail->total, 2, '.', '');
            $data['unit_sale'] = $unit ? $unit->ShortName : '';
            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $details[] = $data;
        }

        $payments = PaymentSale::with('sale', 'payment_method')
            ->where('sale_id', $id)
            ->where('organization_id', $user->organization_id)
            ->orderBy('id', 'DESC')
            ->get();

        $settings = Setting::where('deleted_at', '=', null)
        ->where('organization_id', $user->organization_id)
        ->first();
        $pos_settings = PosSetting::where('deleted_at', '=', null)->first();
        $symbol = $helpers->Get_Currency_Code();

        return response()->json([
            'symbol' => $symbol,
            'payments' => $payments,
            'setting' => $settings,
            'pos_settings' => $pos_settings,
            'sale' => $item,
            'details' => $details,
        ]);
    }



    //------------- GET PAYMENTS SALE -----------\\

    public function Payments_Sale(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'view', PaymentSale::class);
        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $Sale = Sale::findOrFail($id);

        if (!$view_records) {
            $this->authorizeForUser($request->user('api'), 'check_record', $Sale);
        }

        $payments = PaymentSale::with('sale', 'payment_method')
            ->where('sale_id', $id)
            ->where(function ($query) use ($view_records) {
                if (!$view_records) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            })->orderBy('id', 'DESC')->get();

        $due = $Sale->GrandTotal - $Sale->paid_amount;

        return response()->json(['payments' => $payments, 'due' => $due]);
    }



    //------------- Reference Number Order SALE -----------\\

    //---------------- Get Number Order ----------------\\

    public function getNumberOrder()
    {
        // Get the last sale with a reference that starts with 'SL_'
        $last = DB::table('sales')
            ->where('Ref', 'like', 'SL_%')
            ->latest('id')
            ->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode("_", $item);

            // Ensure valid structure before processing
            if (isset($nwMsg[1]) && is_numeric($nwMsg[1])) {
                $inMsg = $nwMsg[1] + 1;
                $code = $nwMsg[0] . '_' . $inMsg;
            } else {
                $code = 'SL_1111'; // Fallback if reference is corrupted
            }
        } else {
            $code = 'SL_1111';
        }

        return $code;
    }

    //------------- SALE PDF -----------\\

    public function Sale_PDF(Request $request, $id)
    {
        $organization_id = Auth::user()->organization_id;
        $details = [];
        $helpers = new helpers();

        $sale_data = Sale::with('details.product.unitSale')
            ->where('organization_id', $organization_id)
            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $sale['client_name'] = $sale_data['client']->name;
        $sale['client_phone'] = $sale_data['client']->phone;
        $sale['client_adr'] = $sale_data['client']->adresse;
        $sale['client_email'] = $sale_data['client']->email;
        $sale['client_tax'] = $sale_data['client']->tax_number;
        $sale['TaxNet'] = number_format($sale_data->TaxNet, 2, '.', '');
        $sale['discount'] = number_format($sale_data->discount, 2, '.', '');
        $sale['shipping'] = number_format($sale_data->shipping, 2, '.', '');
        $sale['statut'] = $sale_data->statut;
        $sale['Ref'] = $sale_data->Ref;
        $sale['date'] = $sale_data->date . ' ' . $sale_data->time;
        $sale['GrandTotal'] = number_format($sale_data->GrandTotal, 2, '.', '');
        $sale['paid_amount'] = number_format($sale_data->paid_amount, 2, '.', '');
        $sale['due'] = number_format($sale['GrandTotal'] - $sale['paid_amount'], 2, '.', '');
        $sale['payment_status'] = $sale_data->payment_statut;

        $detail_id = 0;
        foreach ($sale_data['details'] as $detail) {
            $unit = null;

            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product = Product::with('unitSale')->find($detail->product_id);
                if ($product && $product->unitSale) {
                    $unit = Unit::find($product->unitSale->id);
                }
            }

            if ($detail->product_variant_id) {
                $variant = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $data['code'] = $variant->code;
                $data['name'] = '[' . $variant->name . ']' . $detail['product']['name'];
            } else {
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];
            }

            $data['detail_id'] = ++$detail_id;
            $data['quantity'] = number_format($detail->quantity, 2, '.', '');
            $data['total'] = number_format($detail->total, 2, '.', '');
            $data['unitSale'] = $unit ? $unit->ShortName : '';
            $data['price'] = number_format($detail->price, 2, '.', '');

            $data['DiscountNet'] = $detail->discount_method == '2'
                ? number_format($detail->discount, 2, '.', '')
                : number_format($detail->price * $detail->discount / 100, 2, '.', '');

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);

            $data['Unit_price'] = number_format($detail->price, 2, '.', '');
            $data['discount'] = number_format($detail->discount, 2, '.', '');

            if ($detail->tax_method == '1') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = number_format($tax_price, 2, '.', '');
            } else {
                $data['Net_price'] = $detail->price - $data['DiscountNet'] - $tax_price;
                $data['taxe'] = number_format($detail->price - $data['Net_price'] - $data['DiscountNet'], 2, '.', '');
            }

            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $details[] = $data;
        }

        $settings = Setting::where('deleted_at', '=', null)->first();
        $symbol = $helpers->Get_Currency_Code();

        $Html = view('pdf.sale_pdf', [
            'symbol' => $symbol,
            'setting' => $settings,
            'sale' => $sale,
            'details' => $details,
        ])->render();

        $arabic = new Arabic();
        $p = $arabic->arIdentify($Html);

        for ($i = count($p) - 1; $i >= 0; $i -= 2) {
            $utf8ar = $arabic->utf8Glyphs(substr($Html, $p[$i - 1], $p[$i] - $p[$i - 1]));
            $Html = substr_replace($Html, $utf8ar, $p[$i - 1], $p[$i] - $p[$i - 1]);
        }

        $pdf = PDF::loadHTML($Html);
        return $pdf->download('sale.pdf');
    }

    //---------------- Show Form Create Sale ---------------\\

    public function create(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'create', Sale::class);

        $user_auth = auth()->user();
        $organization_id = $user_auth->organization_id;

        if ($user_auth->is_all_warehouses) {
            $warehouses = Warehouse::where('organization_id', $organization_id)
                ->where('deleted_at', '=', null)
                ->get(['id', 'name']);
        } else {
            $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            $warehouses = Warehouse::where('organization_id', $organization_id)
                ->where('deleted_at', '=', null)
                ->whereIn('id', $warehouses_id)
                ->get(['id', 'name']);
        }

        $clients = Client::where('organization_id', $organization_id)
            ->where('deleted_at', '=', null)
            ->get(['id', 'name']);

        $accounts = Account::where('organization_id', $organization_id)
            ->where('deleted_at', '=', null)
            ->get(['id', 'account_name']);

        $payment_methods = PaymentMethod::whereNull('deleted_at')->get(['id', 'name']);
        $stripe_key = config('app.STRIPE_KEY');

        return response()->json([
            'stripe_key' => $stripe_key,
            'clients' => $clients,
            'warehouses' => $warehouses,
            'accounts' => $accounts,
            'payment_methods' => $payment_methods,
        ]);
    }

    //------------- Show Form Edit Sale -----------\\

    public function edit(Request $request, $id)
    {
        $organization_id = $request->user('api')->organization_id;

        if (SaleReturn::where('sale_id', $id)
            ->where('organization_id', $organization_id)
            ->where('deleted_at', '=', null)->exists()
        ) {
            return response()->json(['success' => false, 'Return exist for the Transaction' => false], 403);
        } else {
            $this->authorizeForUser($request->user('api'), 'update', Sale::class);

            $role = Auth::user()->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');

            $Sale_data = Sale::with('details.product.unitSale')
                ->where('deleted_at', '=', null)
                ->where('organization_id', $organization_id)
                ->findOrFail($id);

            $details = array();

            if (!$view_records) {
                $this->authorizeForUser($request->user('api'), 'check_record', $Sale_data);
            }

            $sale['client_id'] = ($Sale_data->client_id &&
                Client::where('id', $Sale_data->client_id)
                ->where('organization_id', $organization_id)
                ->where('deleted_at', '=', null)->exists())
                ? $Sale_data->client_id
                : '';

            $sale['warehouse_id'] = ($Sale_data->warehouse_id &&
                Warehouse::where('id', $Sale_data->warehouse_id)
                ->where('organization_id', $organization_id)
                ->where('deleted_at', '=', null)->exists())
                ? $Sale_data->warehouse_id
                : '';

            $sale['date'] = $Sale_data->date;
            $sale['tax_rate'] = $Sale_data->tax_rate;
            $sale['TaxNet'] = $Sale_data->TaxNet;
            $sale['discount'] = $Sale_data->discount;
            $sale['shipping'] = $Sale_data->shipping;
            $sale['statut'] = $Sale_data->statut;
            $sale['notes'] = $Sale_data->notes;

            $detail_id = 0;

            foreach ($Sale_data['details'] as $detail) {
                $data = [];

                if ($detail->sale_unit_id !== null) {
                    $unit = Unit::find($detail->sale_unit_id);
                    $data['no_unit'] = 1;
                } else {
                    $product_unit_sale_id = Product::with('unitSale')
                        ->where('id', $detail->product_id)->first();

                    $unit = $product_unit_sale_id['unitSale'] ?? null;
                    $data['no_unit'] = 0;
                }

                if ($detail->product_variant_id) {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('warehouse_id', $Sale_data->warehouse_id)
                        ->where('product_variant_id', $detail->product_variant_id)
                        ->where('organization_id', $organization_id)
                        ->where('deleted_at', '=', null)->first();

                    $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                        ->where('id', $detail->product_variant_id)->first();

                    $data['product_variant_id'] = $detail->product_variant_id;
                    $data['code'] = $productsVariants->code;
                    $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];
                } else {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('warehouse_id', $Sale_data->warehouse_id)
                        ->where('product_variant_id', '=', null)
                        ->where('organization_id', $organization_id)
                        ->where('deleted_at', '=', null)->first();

                    $data['product_variant_id'] = null;
                    $data['code'] = $detail['product']['code'];
                    $data['name'] = $detail['product']['name'];
                }

                $data['del'] = $item_product ? 0 : 1;

                if ($unit && $unit->operator == '/') {
                    $stock = $item_product ? $item_product->qte * $unit->operator_value : 0;
                } elseif ($unit && $unit->operator == '*') {
                    $stock = $item_product ? $item_product->qte / $unit->operator_value : 0;
                } else {
                    $stock = 0;
                }

                $data['id'] = $detail->id;
                $data['stock'] = $detail['product']['type'] != 'is_service' ? $stock : '---';
                $data['product_type'] = $detail['product']['type'];
                $data['detail_id'] = ++$detail_id;
                $data['product_id'] = $detail->product_id;
                $data['total'] = $detail->total;
                $data['quantity'] = $detail->quantity;
                $data['qte_copy'] = $detail->quantity;
                $data['etat'] = 'current';
                $data['unitSale'] = $unit ? $unit->ShortName : '';
                $data['sale_unit_id'] = $unit ? $unit->id : '';
                $data['is_imei'] = $detail['product']['is_imei'];
                $data['imei_number'] = $detail->imei_number;

                if ($detail->discount_method == '2') {
                    $data['DiscountNet'] = $detail->discount;
                } else {
                    $data['DiscountNet'] = $detail->price * $detail->discount / 100;
                }

                $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);

                $data['Unit_price'] = $detail->price;
                $data['tax_percent'] = $detail->TaxNet;
                $data['tax_method'] = $detail->tax_method;
                $data['discount'] = $detail->discount;
                $data['discount_Method'] = $detail->discount_method;

                if ($detail->tax_method == '1') {
                    $data['Net_price'] = $detail->price - $data['DiscountNet'];
                    $data['taxe'] = $tax_price;
                } else {
                    $data['Net_price'] = $detail->price - $data['DiscountNet'] - $tax_price;
                    $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                }

                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);

                $details[] = $data;
            }

            $user_auth = auth()->user();
            if ($user_auth->is_all_warehouses) {
                $warehouses = Warehouse::where('deleted_at', '=', null)
                    ->where('organization_id', $organization_id)
                    ->get(['id', 'name']);
            } else {
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
                $warehouses = Warehouse::where('deleted_at', '=', null)
                    ->where('organization_id', $organization_id)
                    ->whereIn('id', $warehouses_id)
                    ->get(['id', 'name']);
            }

            $clients = Client::where('deleted_at', '=', null)
                ->where('organization_id', $organization_id)
                ->get(['id', 'name']);

            return response()->json([
                'details' => $details,
                'sale' => $sale,
                'clients' => $clients,
                'warehouses' => $warehouses,
            ]);
        }
    }




    //------------- Show Form Convert To Sale -----------\\

    public function Elemens_Change_To_Sale(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Quotation::class);
        $organization_id = $request->user()->organization_id;

        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $Quotation = Quotation::with('details.product.unitSale')
            ->where('deleted_at', '=', null)
            ->where('organization_id', $organization_id)
            ->findOrFail($id);

        $details = [];

        if (!$view_records) {
            $this->authorizeForUser($request->user('api'), 'check_record', $Quotation);
        }

        $sale['client_id'] = Client::where('id', $Quotation->client_id)->where('deleted_at', null)->where('organization_id', $organization_id)->exists()
            ? $Quotation->client_id
            : '';

        $sale['warehouse_id'] = Warehouse::where('id', $Quotation->warehouse_id)->where('deleted_at', null)->where('organization_id', $organization_id)->exists()
            ? $Quotation->warehouse_id
            : '';

        $sale['date'] = $Quotation->date;
        $sale['TaxNet'] = $Quotation->TaxNet;
        $sale['tax_rate'] = $Quotation->tax_rate;
        $sale['discount'] = $Quotation->discount;
        $sale['shipping'] = $Quotation->shipping;
        $sale['statut'] = 'completed';
        $sale['notes'] = $Quotation->notes;

        $detail_id = 0;

        foreach ($Quotation['details'] as $detail) {
            if ($detail->sale_unit_id !== null || $detail['product']['type'] == 'is_service') {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();

                if ($detail->product_variant_id) {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('product_variant_id', $detail->product_variant_id)
                        ->where('warehouse_id', $Quotation->warehouse_id)
                        ->where('organization_id', $organization_id)
                        ->where('deleted_at', '=', null)
                        ->first();

                    $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                        ->where('id', $detail->product_variant_id)
                        ->where('organization_id', $organization_id)
                        ->where('deleted_at', null)
                        ->first();

                    $data['del'] = $item_product ? 0 : 1;
                    $data['product_variant_id'] = $detail->product_variant_id;
                    $data['code'] = $productsVariants->code;
                    $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];

                    $stock = match (true) {
                        $unit && $unit->operator == '/' => $item_product ? $item_product->qte / $unit->operator_value : 0,
                        $unit && $unit->operator == '*' => $item_product ? $item_product->qte * $unit->operator_value : 0,
                        default => 0,
                    };
                } else {
                    $item_product = product_warehouse::where('product_id', $detail->product_id)
                        ->where('warehouse_id', $Quotation->warehouse_id)
                        ->where('product_variant_id', null)
                        ->where('organization_id', $organization_id)
                        ->where('deleted_at', '=', null)
                        ->first();

                    $data['del'] = $item_product ? 0 : 1;
                    $data['product_variant_id'] = null;
                    $data['code'] = $detail['product']['code'];
                    $data['name'] = $detail['product']['name'];

                    $stock = match (true) {
                        $unit && $unit->operator == '/' => $item_product ? $item_product->qte * $unit->operator_value : 0,
                        $unit && $unit->operator == '*' => $item_product ? $item_product->qte / $unit->operator_value : 0,
                        default => 0,
                    };
                }

                $data['id'] = $id;
                $data['stock'] = $detail['product']['type'] != 'is_service' ? $stock : '---';
                $data['product_type'] = $detail['product']['type'];
                $data['detail_id'] = ++$detail_id;
                $data['quantity'] = $detail->quantity;
                $data['product_id'] = $detail->product_id;
                $data['total'] = $detail->total;
                $data['etat'] = 'current';
                $data['qte_copy'] = $detail->quantity;
                $data['unitSale'] = $unit?->ShortName ?? '';
                $data['sale_unit_id'] = $unit?->id ?? '';
                $data['is_imei'] = $detail['product']['is_imei'];
                $data['imei_number'] = $detail->imei_number;

                $data['DiscountNet'] = $detail->discount_method == '2'
                    ? $detail->discount
                    : $detail->price * $detail->discount / 100;

                $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);

                $data['Unit_price'] = $detail->price;
                $data['tax_percent'] = $detail->TaxNet;
                $data['tax_method'] = $detail->tax_method;
                $data['discount'] = $detail->discount;
                $data['discount_Method'] = $detail->discount_method;

                if ($detail->tax_method == '1') {
                    $data['Net_price'] = $detail->price - $data['DiscountNet'];
                    $data['taxe'] = $tax_price;
                    $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
                } else {
                    $data['Net_price'] = ($detail->price - $data['DiscountNet'] - $tax_price);
                    $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                    $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
                }

                $details[] = $data;
            }
        }

        $user_auth = auth()->user();
        $warehouses = $user_auth->is_all_warehouses
            ? Warehouse::where('deleted_at', null)->where('organization_id', $organization_id)->get(['id', 'name'])
            : Warehouse::where('deleted_at', null)
            ->whereIn('id', UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray())
            ->where('organization_id', $organization_id)
            ->get(['id', 'name']);

        $clients = Client::where('deleted_at', null)->where('organization_id', $organization_id)->get(['id', 'name']);

        return response()->json([
            'details' => $details,
            'sale' => $sale,
            'clients' => $clients,
            'warehouses' => $warehouses,
        ]);
    }



    //------------------- get_Products_by_sale -----------------\\

    public function get_Products_by_sale(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'create', SaleReturn::class);
        $organization_id = $request->user()->organization_id;

        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');

        $SaleReturn = Sale::with('details.product.unitSale')
            ->where('deleted_at', null)
            ->where('organization_id', $organization_id)
            ->findOrFail($id);

        if (!$view_records) {
            $this->authorizeForUser($request->user('api'), 'check_record', $SaleReturn);
        }

        $Return_detail = [
            'client_id' => $SaleReturn->client_id,
            'warehouse_id' => $SaleReturn->warehouse_id,
            'sale_id' => $SaleReturn->id,
            'tax_rate' => 0,
            'TaxNet' => 0,
            'discount' => 0,
            'shipping' => 0,
            'statut' => 'received',
            'notes' => '',
        ];

        $details = [];
        $detail_id = 0;

        foreach ($SaleReturn['details'] as $detail) {
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
                $data['no_unit'] = 1;
            } else {
                $product_unit_sale_id = Product::with('unitSale')->where('id', $detail->product_id)->first();
                $unit = $product_unit_sale_id['unitSale']
                    ? Unit::where('id', $product_unit_sale_id['unitSale']->id)->first()
                    : null;
                $data['no_unit'] = 0;
            }

            if ($detail->product_variant_id) {
                $item_product = product_warehouse::where('product_id', $detail->product_id)
                    ->where('product_variant_id', $detail->product_variant_id)
                    ->where('warehouse_id', $SaleReturn->warehouse_id)
                    ->where('organization_id', $organization_id)
                    ->where('deleted_at', null)
                    ->first();

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)
                    ->where('organization_id', $organization_id)
                    ->first();

                $data['del'] = $item_product ? 0 : 1;
                $data['product_variant_id'] = $detail->product_variant_id;
                $data['code'] = $productsVariants->code;
                $data['name'] = '[' . $productsVariants->name . ']' . $detail['product']['name'];

                $stock = match (true) {
                    $unit && $unit->operator == '/' => $item_product ? $item_product->qte * $unit->operator_value : 0,
                    $unit && $unit->operator == '*' => $item_product ? $item_product->qte / $unit->operator_value : 0,
                    default => 0,
                };
            } else {
                $item_product = product_warehouse::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $SaleReturn->warehouse_id)
                    ->where('product_variant_id', null)
                    ->where('organization_id', $organization_id)
                    ->where('deleted_at', null)
                    ->first();

                $data['del'] = $item_product ? 0 : 1;
                $data['product_variant_id'] = null;
                $data['code'] = $detail['product']['code'];
                $data['name'] = $detail['product']['name'];

                $stock = match (true) {
                    $unit && $unit->operator == '/' => $item_product ? $item_product->qte * $unit->operator_value : 0,
                    $unit && $unit->operator == '*' => $item_product ? $item_product->qte / $unit->operator_value : 0,
                    default => 0,
                };
            }

            $data['id'] = $detail->id;
            $data['stock'] = $detail['product']['type'] != 'is_service' ? $stock : '---';
            $data['detail_id'] = ++$detail_id;
            $data['quantity'] = $detail->quantity;
            $data['sale_quantity'] = $detail->quantity;
            $data['product_id'] = $detail->product_id;
            $data['unitSale'] = $unit?->ShortName ?? '';
            $data['sale_unit_id'] = $unit?->id ?? '';
            $data['is_imei'] = $detail['product']['is_imei'];
            $data['imei_number'] = $detail->imei_number;

            $data['DiscountNet'] = $detail->discount_method == '2'
                ? $detail->discount
                : $detail->price * $detail->discount / 100;

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
            $data['Unit_price'] = $detail->price;
            $data['tax_percent'] = $detail->TaxNet;
            $data['tax_method'] = $detail->tax_method;
            $data['discount'] = $detail->discount;
            $data['discount_Method'] = $detail->discount_method;

            if ($detail->tax_method == '1') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = $tax_price;
                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet'] - $tax_price);
                $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
                $data['subtotal'] = ($data['Net_price'] * $data['quantity']) + ($tax_price * $data['quantity']);
            }

            $details[] = $data;
        }

        return response()->json([
            'details' => $details,
            'sale_return' => $Return_detail,
        ]);
    }




    //------------- Send sale on Email -----------\\

    public function Send_Email(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Sale::class);

        $user = $request->user('api');
        $organization_id = $user->organization_id;

        $sale = Sale::with('client')
            ->where('organization_id', $organization_id)
            ->whereNull('deleted_at')
            ->findOrFail($request->id);

        $helpers = new helpers();
        $currency = $helpers->Get_Currency();

        $settings = Setting::where('organization_id', $organization_id)
            ->whereNull('deleted_at')
            ->first();

        $emailMessage = EmailMessage::where('organization_id', $organization_id)
            ->where('name', 'sale')
            ->first();

        $message_body = $emailMessage ? $emailMessage->body : '';
        $message_subject = $emailMessage ? $emailMessage->subject : '';

        $random_number = Str::random(10);
        $invoice_url = url('/api/sale_pdf/' . $request->id . '?' . $random_number);
        $invoice_number = $sale->Ref;

        $total_amount = $currency . ' ' . number_format($sale->GrandTotal, 2, '.', ',');
        $paid_amount  = $currency . ' ' . number_format($sale->paid_amount, 2, '.', ',');
        $due_amount   = $currency . ' ' . number_format($sale->GrandTotal - $sale->paid_amount, 2, '.', ',');

        $contact_name = $sale['client']->name;
        $business_name = $settings->CompanyName;
        $receiver_email = $sale['client']->email;

        $message_body = str_replace(
            ['{contact_name}', '{business_name}', '{invoice_url}', '{invoice_number}', '{total_amount}', '{paid_amount}', '{due_amount}'],
            [$contact_name, $business_name, $invoice_url, $invoice_number, $total_amount, $paid_amount, $due_amount],
            $message_body
        );

        $email = [
            'subject' => $message_subject,
            'body' => $message_body,
            'company_name' => $business_name,
        ];

        $this->Set_config_mail();

        Mail::to($receiver_email)->send(new CustomEmail($email));

        return response()->json(['message' => 'Email sent successfully'], 200);
    }



    //-------------------Sms Notifications -----------------\\

    public function Send_SMS(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Sale::class);

        $user = $request->user('api');
        $organization_id = $user->organization_id;

        $sale = Sale::with('client')
            ->where('organization_id', $organization_id)
            ->whereNull('deleted_at')
            ->findOrFail($request->id);

        $helpers = new helpers();
        $currency = $helpers->Get_Currency();

        $settings = Setting::where('organization_id', $organization_id)
            ->whereNull('deleted_at')
            ->first();

        $default_sms_gateway = sms_gateway::where('organization_id', $organization_id)
            ->where('id', $settings->sms_gateway)
            ->whereNull('deleted_at')
            ->first();

        $smsMessage = SMSMessage::where('organization_id', $organization_id)
            ->where('name', 'sale')
            ->first();

        $message_text = $smsMessage ? $smsMessage->text : '';

        $random_number = Str::random(10);
        $invoice_url = url('/api/sale_pdf/' . $request->id . '?' . $random_number);
        $invoice_number = $sale->Ref;

        $total_amount = $currency . ' ' . number_format($sale->GrandTotal, 2, '.', ',');
        $paid_amount  = $currency . ' ' . number_format($sale->paid_amount, 2, '.', ',');
        $due_amount   = $currency . ' ' . number_format($sale->GrandTotal - $sale->paid_amount, 2, '.', ',');

        $contact_name = $sale['client']->name;
        $business_name = $settings->CompanyName;
        $receiverNumber = $sale['client']->phone;

        $message_text = str_replace(
            ['{contact_name}', '{business_name}', '{invoice_url}', '{invoice_number}', '{total_amount}', '{paid_amount}', '{due_amount}'],
            [$contact_name, $business_name, $invoice_url, $invoice_number, $total_amount, $paid_amount, $due_amount],
            $message_text
        );

        if ($default_sms_gateway->title == "twilio") {
            try {
                $client = new Client_Twilio(env("TWILIO_SID"), env("TWILIO_TOKEN"));
                $client->messages->create($receiverNumber, [
                    'from' => env("TWILIO_FROM"),
                    'body' => $message_text
                ]);
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } elseif ($default_sms_gateway->title == "termii") {
            $client = new Client_termi();
            $url = 'https://api.ng.termii.com/api/sms/send';

            $payload = [
                'to' => $receiverNumber,
                'from' => env('TERMI_SENDER'),
                'sms' => $message_text,
                'type' => 'plain',
                'channel' => 'generic',
                'api_key' => env('TERMI_KEY'),
            ];

            try {
                $response = $client->post($url, ['json' => $payload]);
                $result = json_decode($response->getBody(), true);
                return response()->json($result);
            } catch (\Exception $e) {
                Log::error("Termii SMS Error: " . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Failed to send SMS'], 500);
            }
        } elseif ($default_sms_gateway->title == "infobip") {
            $configuration = (new Configuration())
                ->setHost(env("base_url"))
                ->setApiKeyPrefix('Authorization', 'App')
                ->setApiKey('Authorization', env("api_key"));

            $client = new Client_guzzle();
            $sendSmsApi = new SendSMSApi($client, $configuration);
            $destination = (new SmsDestination())->setTo($receiverNumber);
            $message = (new SmsTextualMessage())
                ->setFrom(env("sender_from"))
                ->setText($message_text)
                ->setDestinations([$destination]);

            $infobip_request = (new SmsAdvancedTextualRequest())->setMessages([$message]);

            try {
                $smsResponse = $sendSmsApi->sendSmsMessage($infobip_request);
                echo ("Response body: " . $smsResponse);
            } catch (Throwable $apiException) {
                echo ("HTTP Code: " . $apiException->getCode() . "\n");
            }
        }

        return response()->json(['success' => true]);
    }



    // sales_send_whatsapp
    public function sales_send_whatsapp(Request $request)
    {
        $user = $request->user('api');

        // Find sale scoped by user's organization
        $sale = Sale::with('client')
            ->where('deleted_at', '=', null)
            ->where('organization_id', $user->organization_id)
            ->findOrFail($request->id);

        $helpers = new helpers();
        $currency = $helpers->Get_Currency();

        // settings scoped by organization
        $settings = Setting::where('deleted_at', '=', null)
            ->where('organization_id', $user->organization_id)
            ->first();

        // the custom msg of sale
        $smsMessage  = SMSMessage::where('name', 'sale')
            ->where('organization_id', $user->organization_id)
            ->first();

        $message_text = $smsMessage ? $smsMessage->text : '';

        // Tags
        $random_number = Str::random(10);
        $invoice_url = url('/api/sale_pdf/' . $request->id . '?' . $random_number);
        $invoice_number = $sale->Ref;

        $total_amount = $currency . ' ' . number_format($sale->GrandTotal, 2, '.', ',');
        $paid_amount  = $currency . ' ' . number_format($sale->paid_amount, 2, '.', ',');
        $due_amount   = $currency . ' ' . number_format($sale->GrandTotal - $sale->paid_amount, 2, '.', ',');

        $contact_name = $sale['client']->name;
        $business_name = $settings->CompanyName;

        // receiver Number
        $receiverNumber = $sale['client']->phone;

        // Check if the phone number is empty or null
        if (empty($receiverNumber) || $receiverNumber == null || $receiverNumber == 'null' || $receiverNumber == '') {
            return response()->json(['error' => 'Phone number is missing'], 400);
        }

        // Replace the text with tags
        $message_text = str_replace('{contact_name}', $contact_name, $message_text);
        $message_text = str_replace('{business_name}', $business_name, $message_text);
        $message_text = str_replace('{invoice_url}', $invoice_url, $message_text);
        $message_text = str_replace('{invoice_number}', $invoice_number, $message_text);
        $message_text = str_replace('{total_amount}', $total_amount, $message_text);
        $message_text = str_replace('{paid_amount}', $paid_amount, $message_text);
        $message_text = str_replace('{due_amount}', $due_amount, $message_text);

        return response()->json(['message' => $message_text, 'phone' => $receiverNumber]);
    }

    // get_today_sales
    public function get_today_sales(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'Sales_pos', Sale::class);
        $user = $request->user('api');

        $today = Carbon::today()->toDateString();
        $data['today'] = $today;

        $data['total_sales_amount'] = Sale::whereNull('deleted_at')
            ->where('organization_id', $user->organization_id)
            ->whereDate('date', $today)
            ->sum('GrandTotal');

        $data['total_amount_paid'] = Sale::whereNull('deleted_at')
            ->where('organization_id', $user->organization_id)
            ->whereDate('date', $today)
            ->sum('paid_amount');

        // Fetch payment methods by name or ID
        $cashMethod = PaymentMethod::where('name', 'Cash')
            ->where('organization_id', $user->organization_id)
            ->first();

        $creditCardMethod = PaymentMethod::where('name', 'Credit Card')
            ->where('organization_id', $user->organization_id)
            ->first();

        $chequeMethod = PaymentMethod::where('name', 'Cheque')
            ->where('organization_id', $user->organization_id)
            ->first();

        $data['total_cash'] = $cashMethod
            ? PaymentSale::whereNull('deleted_at')
            ->where('organization_id', $user->organization_id)
            ->whereDate('date', $today)
            ->where('payment_method_id', $cashMethod->id)
            ->sum('montant')
            : 0;

        $data['total_credit_card'] = $creditCardMethod
            ? PaymentSale::whereNull('deleted_at')
            ->where('organization_id', $user->organization_id)
            ->whereDate('date', $today)
            ->where('payment_method_id', $creditCardMethod->id)
            ->sum('montant')
            : 0;

        $data['total_cheque'] = $chequeMethod
            ? PaymentSale::whereNull('deleted_at')
            ->where('organization_id', $user->organization_id)
            ->whereDate('date', $today)
            ->where('payment_method_id', $chequeMethod->id)
            ->sum('montant')
            : 0;

        return response()->json($data);
    }


    public function Send_Subscription_Reminder_SMS($subscription_id)
    {
        // Load Subscription details with client relationship
        $subscription = Subscription::with('client')->findOrFail($subscription_id);

        // Retrieve currency and settings
        $helpers = new helpers();
        $currency = $helpers->Get_Currency();

        $settings = Setting::whereNull('deleted_at')->first();
        $default_sms_gateway = sms_gateway::where('id', $settings->sms_gateway)
            ->whereNull('deleted_at')->first();

        // Get SMS message template
        $smsMessage = SMSMessage::where('name', 'subscription_reminder')->first();
        $message_text = $smsMessage ? $smsMessage->text : '';

        // Prepare tags replacement
        $client_name = $subscription->client->name;
        $business_name = $settings->CompanyName;
        $next_billing_date = Carbon::parse($subscription->next_billing_date)->format('Y-m-d');

        // Replace tags in SMS template
        $message_text = str_replace('{client_name}', $client_name, $message_text);
        $message_text = str_replace('{business_name}', $business_name, $message_text);
        $message_text = str_replace('{next_billing_date}', $next_billing_date, $message_text);

        // Receiver phone number
        $receiverNumber = $subscription->client->phone;

        // Send SMS based on the gateway
        if ($default_sms_gateway->title == "twilio") {
            try {
                $account_sid = env("TWILIO_SID");
                $auth_token = env("TWILIO_TOKEN");
                $twilio_number = env("TWILIO_FROM");

                $client = new Client_Twilio($account_sid, $auth_token);
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number,
                    'body' => $message_text
                ]);
            } catch (Exception $e) {
                Log::error('Twilio SMS Error: ' . $e->getMessage());

                ErrorLog::create([
                    'context' => 'Twilio SMS',
                    'message' => $e->getMessage(),
                    'details' => json_encode([
                        'receiver' => $receiverNumber,
                        'trace' => $e->getTraceAsString()
                    ]),
                ]);

                return response()->json(['message' => $e->getMessage()], 500);
            }
        } elseif ($default_sms_gateway->title == "termii") {
            $client = new Client_termi();
            $url = 'https://api.ng.termii.com/api/sms/send';

            $payload = [
                'to' => $receiverNumber,
                'from' => env('TERMI_SENDER'),
                'sms' => $message_text,
                'type' => 'plain',
                'channel' => 'generic',
                'api_key' => env('TERMI_KEY'),
            ];

            try {
                $response = $client->post($url, ['json' => $payload]);
                $result = json_decode($response->getBody(), true);
                return response()->json($result);
            } catch (\Exception $e) {
                Log::error("Termii SMS Error: " . $e->getMessage());

                ErrorLog::create([
                    'context' => 'Termii SMS',
                    'message' => $e->getMessage(),
                    'details' => json_encode([
                        'receiver' => $receiverNumber,
                        'payload' => $payload,
                        'trace' => $e->getTraceAsString()
                    ]),
                ]);

                return response()->json(['status' => 'error', 'message' => 'Failed to send SMS'], 500);
            }
        } elseif ($default_sms_gateway->title == "infobip") {
            $BASE_URL = env("base_url");
            $API_KEY = env("api_key");
            $SENDER = env("sender_from");

            $configuration = (new Configuration())
                ->setHost($BASE_URL)
                ->setApiKeyPrefix('Authorization', 'App')
                ->setApiKey('Authorization', $API_KEY);

            $client = new Client_guzzle();

            $sendSmsApi = new SendSMSApi($client, $configuration);
            $destination = (new SmsDestination())->setTo($receiverNumber);
            $message = (new SmsTextualMessage())
                ->setFrom($SENDER)
                ->setText($message_text)
                ->setDestinations([$destination]);

            $request = (new SmsAdvancedTextualRequest())->setMessages([$message]);

            try {
                $smsResponse = $sendSmsApi->sendSmsMessage($request);
                Log::info("Infobip SMS sent successfully", [$smsResponse]);
            } catch (Throwable $apiException) {
                Log::error("Infobip SMS Error: " . $apiException->getMessage());

                ErrorLog::create([
                    'context' => 'Infobip SMS',
                    'message' => $apiException->getMessage(),
                    'details' => json_encode([
                        'receiver' => $receiverNumber,
                        'trace' => $apiException->getTraceAsString()
                    ]),
                ]);

                return response()->json(['status' => 'error', 'message' => $apiException->getMessage()], 500);
            }
        }

        return response()->json(['success' => true]);
    }



    public function Send_Subscription_Payment_Success_SMS($subscription_id, $invoice_id)
    {
        $user = auth('api')->user();

        $subscription = Subscription::with('client')
            ->where('organization_id', $user->organization_id)
            ->findOrFail($subscription_id);

        $invoice = Sale::where('organization_id', $user->organization_id)
            ->findOrFail($invoice_id);

        $settings = Setting::where('organization_id', $user->organization_id)
            ->first();

        $default_sms_gateway = sms_gateway::where('id', $settings->sms_gateway)
            ->where('organization_id', $user->organization_id)
            ->first();

        $message_text = 'Hello {client_name}, your subscription at {business_name} has been successfully renewed.';

        $tags = [
            '{client_name}' => $subscription->client->name,
            '{business_name}' => $settings->CompanyName,
        ];

        $message_text = str_replace(array_keys($tags), array_values($tags), $message_text);

        $receiverNumber = $subscription->client->phone;

        // Example using Termii Gateway
        if ($default_sms_gateway && $default_sms_gateway->title == "termii") {
            $client = new Client_termi();
            $payload = [
                'to' => $receiverNumber,
                'from' => env('TERMI_SENDER'),
                'sms' => $message_text,
                'type' => 'plain',
                'channel' => 'generic',
                'api_key' => env('TERMI_KEY'),
            ];

            try {
                $response = $client->post('https://api.ng.termii.com/api/sms/send', ['json' => $payload]);
                $result = json_decode($response->getBody(), true);
                return response()->json($result);
            } catch (\Exception $e) {
                Log::error("Termii SMS Error: " . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Failed to send SMS'], 500);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'SMS Gateway not configured or unsupported'], 400);
    }
}
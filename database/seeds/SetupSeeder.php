<?php
namespace Database\Seeders;

use App\Model\Restaurant;
use App\Model\Role;
use App\Model\Route;
use App\Model\User;
use App\Model\WaCompanyPreference as CompanyPreference;
use App\Model\WaDepartment;
use App\Model\WaLocationAndStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;


class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            // Company
            $business = new CompanyPreference();
            $business->name = "Kanini Haraka Ent. Ltd";
            $business->official_company_number = "020 000000";
            $business->tax_authority_reference = "KRA";
            $business->address = "Thika Town, Thika, Kenya";
            $business->telephone_number = "0700000000";
            $business->facsimile_number = "";
            $business->email_address = "hello@khel.com";
            $business->home_currency = "KES";
//            $business->debtors_control_gl_account= $request->debtors_control_gl_account;
//            $business->creditors_control_gl_account= $request->creditors_control_gl_account;
//            $business->payroll_net_pay_clearing_gl_account= $request->payroll_net_pay_clearing_gl_account;
//            $business->goods_received_clearing_gl_account= $request->goods_received_clearing_gl_account;
//            $business->retained_earning_clearing_gl_account= $request->retained_earning_clearing_gl_account;
//            $business->freight_recharged_gl_account= $request->freight_recharged_gl_account;
//            $business->sales_exchange_variances_gl_account= $request->sales_exchange_variances_gl_account;
//            $business->purchases_exchange_variances_gl_account= $request->purchases_exchange_variances_gl_account;
//            $business->payment_discount_gl_account= $request->payment_discount_gl_account;
            $business->save();

            // Restaurant/Branch
            $branch = new Restaurant();
            $branch->name = 'Thika HQ';
            $branch->opening_time = '06:00';
            $branch->closing_time = '22:00';
            $branch->location = 'Thika Town';
            $branch->latitude = -1.0348763085297907;
            $branch->longitude = 37.0774717989923;
            $branch->branch_code = 'HQ';
            $branch->wa_company_preference_id = $business->id;
            $branch->telephone = '0700000000';
            $branch->mpesa_till = '000000';
            $branch->vat = '';
            $branch->pin = '';
            $branch->website_url = '';
            $branch->email = '';
            $branch->save();

            // Department
            $department = new WaDepartment();
            $department->department_name = 'Administration';
            $department->department_code = 'ADMIN';
            $department->restaurant_id = $branch->id;
            $department->save();

            // Cash Sales Route
            $route = Route::create([
                'route_name' => 'Cash Sales',
            ]);

            // Main Store
            $store = new WaLocationAndStore();
            $store->location_code = 'MS';
            $store->location_name = 'Main Store';
            $store->wa_branch_id = $branch->id;
            $store->route_id = $route->id;
            $store->save();

            // Role
            $role = new Role();
            $role->title = 'Super Admin';
            $role->save();

            // User
            $user = new User();
            $user->name = "Kanini Macharia";
            $user->role_id = $role->id;
            $user->restaurant_id = $branch->id;
            $user->wa_department_id = $department->id;
            $user->wa_location_and_store_id = $store->id;
            $user->phone_number = '0700000000';
            $user->email = 'admin@khel.com';
            $user->password = bcrypt(env('DEFAULT_PASSWORD'));
            $user->status = '1';
            $user->route = $route->id;
            $user->save();

            // TODO: WaNumSeriesCodes

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
        }
    }
}
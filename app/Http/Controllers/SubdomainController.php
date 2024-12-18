<?php

namespace App\Http\Controllers;

use App\Models\Tenant;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Database\DatabaseManager;
use Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException;

class SubdomainController extends Controller
{
    public function createSubdomain(Request $request)
    {
        $request->validate([
            'subdomain' => 'required|string|alpha_dash|unique:tenants,id',
        ]);

        $subdomain = $request->input('subdomain');
        $domain = $subdomain . '.' . 'localhost'; // Replace with your base domain.

        // try {
        // Create the tenant
        $tenant = Tenant::create([
            'id' => $subdomain,

        ]);

        $tenant->domains()->create(['domain' => $domain]);

        //tenancy()->initialize($tenant);

        // Attach the domain to the tenant
        // $tenant->domains()->create([
        //     'domain' => $domain,
        // ]);

        // Initialize the tenant's database
        //$tenant->makeCurrent();

        // Run migrations for the tenant's database
        // Artisan::call('tenants:artisan', [
        //     'artisanCommand' => 'migrate',
        //     '--tenant' => $tenant->id,
        //     '--force' => true,
        // ]);

        Artisan::call('tenants:seed', [
            '--tenants' => $tenant->id,
            '--class' => 'TenantSeeder',
        ]);

        return response()->json([
            'message' => 'Subdomain and database created successfully.',
            'subdomain' => $subdomain,
            'domain' => $domain,
        ], 201);
        // }
        //  catch (TenantDatabaseAlreadyExistsException $e) {
        //     return response()->json(['error' => 'Tenant database already exists.'], 400);
        // } 
        // catch (\Exception $e) {
        //     return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        // }
    }
}

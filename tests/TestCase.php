<?php

namespace Paparee\Rakaca\Tests;

use Bale\Api\ApiServiceProvider;
use Bale\Cms\CmsServiceProvider;
use Bale\Core\CoreServiceProvider;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Paparee\Rakaca\RakacaServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            PermissionServiceProvider::class,
            CoreServiceProvider::class,
            CmsServiceProvider::class,
            RakacaServiceProvider::class,
            ApiServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('activitylog.enabled', true);
        $app['config']->set('activitylog.default_auth_driver', 'web');
        $app['config']->set('auth.defaults.guard', 'web');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createUsersTable();
        $this->createPermissionTables();
        $this->createActivityLogTable();
        $this->createBaleTables();
        $this->createRakacaTables();
        $this->createApiTokensTable();
    }

    protected function createUsersTable(): void
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    protected function createPermissionTables(): void
    {
        Schema::create('roles', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('permissions', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->uuid('model_uuid')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->index(['model_id', 'model_type']);
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
        });

        // Spatie uses model_has_roles with morph
        Schema::create('model_has_roles', function ($table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->uuid('model_uuid')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->index(['model_id', 'model_type']);
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });

        Schema::create('role_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });
    }

    protected function createActivityLogTable(): void
    {
        Schema::create('activity_log', function ($table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->json('attribute_changes')->nullable();
            $table->string('event')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });
    }

    protected function createBaleTables(): void
    {
        Schema::create('bale_organizations', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('bale_lists', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('database_host');
            $table->string('database_name')->unique();
            $table->text('database_username');
            $table->string('database_password');
            $table->string('storage_prefix')->nullable();
            $table->boolean('is_active');
            $table->timestamps();
            $table->foreign('organization_id')->references('id')->on('bale_organizations')->cascadeOnDelete();
        });

        Schema::create('bale_users', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('bale_id');
            $table->uuid('user_uuid');
            $table->string('role');
            $table->timestamps();
            $table->foreign('bale_id')->references('id')->on('bale_lists')->cascadeOnDelete();
        });

        Schema::create('tenant_analytics', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('bale_id');
            $table->string('provider');
            $table->string('website_id');
            $table->string('domain')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->foreign('bale_id')->references('id')->on('bale_lists')->cascadeOnDelete();
        });
    }

    protected function createRakacaTables(): void
    {
        Schema::create('rakaca_services', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('actived')->default(true);
            $table->timestamps();
        });

        Schema::create('rakaca_aduan_categories', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('rakaca_aduans', function ($table) {
            $table->uuid('id')->primary();
            $table->string('ref_code')->unique();
            $table->text('nama_lengkap');
            $table->text('nip');
            $table->text('wa_number');
            $table->uuid('aduan_category_id');
            $table->text('deskripsi');
            $table->string('status')->default('pending');
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->foreign('aduan_category_id')->references('id')->on('rakaca_aduan_categories')->cascadeOnDelete();
        });

        Schema::create('rakaca_forms', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('rakaca_service_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('meta')->nullable();
            $table->json('response_form_schema')->nullable();
            $table->boolean('actived')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('rakaca_service_id')->references('id')->on('rakaca_services')->cascadeOnDelete();
        });

        Schema::create('rakaca_submissions', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('user_uuid');
            $table->uuid('rakaca_form_id');
            $table->string('code')->unique();
            $table->string('status')->default('menunggu-berkas');
            $table->json('items')->nullable();
            $table->timestamp('status_changed_at')->nullable();
            $table->timestamp('files_finalized_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('rakaca_form_id')->references('id')->on('rakaca_forms')->cascadeOnDelete();
        });

        Schema::create('rakaca_submission_uploads', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('rakaca_submission_id');
            $table->uuid('user_uuid');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->timestamps();
            $table->foreign('rakaca_submission_id')->references('id')->on('rakaca_submissions')->cascadeOnDelete();
            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
        });

        Schema::create('rakaca_form_responses', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('rakaca_submission_id')->unique();
            $table->timestamp('processed_at')->nullable();
            $table->uuid('processed_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('revise_note')->nullable();
            $table->timestamp('requested_revision_at')->nullable();
            $table->json('resolution_data')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->uuid('resolved_by')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('rakaca_submission_id')->references('id')->on('rakaca_submissions')->cascadeOnDelete();
            $table->foreign('processed_by')->references('uuid')->on('users')->nullOnDelete();
            $table->foreign('resolved_by')->references('uuid')->on('users')->nullOnDelete();
        });

        Schema::create('person_has_services', function ($table) {
            $table->uuid('id')->primary();
            $table->uuid('user_uuid');
            $table->uuid('rakaca_service_id');
            $table->boolean('actived')->default(true);
            $table->timestamps();
            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
            $table->foreign('rakaca_service_id')->references('id')->on('rakaca_services')->cascadeOnDelete();
        });
    }

    protected function createApiTokensTable(): void
    {
        Schema::create('api_tokens', function ($table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->json('abilities')->nullable();
            $table->json('allowed_ips')->nullable();
            $table->json('allowed_hosts')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }
}

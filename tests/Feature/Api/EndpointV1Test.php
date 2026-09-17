<?php

use Bale\Api\Services\TokenManager;
use Illuminate\Support\Str;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

beforeEach(function () {
    $this->tokens = app(TokenManager::class);
});

function createRakacaForm(string $name = 'IT Helpdesk', bool $actived = true): Form
{
    $service = RakacaService::create([
        'name' => 'IT Service',
        'slug' => 'it-service-'.strtolower(str_replace(' ', '-', $name)),
        'actived' => true,
    ]);

    return Form::create([
        'rakaca_service_id' => $service->id,
        'name' => $name,
        'slug' => strtolower(str_replace(' ', '-', $name)),
        'meta' => ['fields' => []],
        'response_form_schema' => [
            ['key' => 'summary', 'label' => 'Summary', 'type' => 'string'],
        ],
        'actived' => $actived,
    ]);
}

test('valid token with form.read ability can list active forms', function () {
    createRakacaForm('Form A');
    createRakacaForm('Form B');
    createRakacaForm('Form C', false);

    $issued = $this->tokens->issue('Client', ['rakaca.form.read']);

    $this->getJson('/api/rakaca/v1/forms', ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertOk()
        ->assertJsonStructure(['data', 'meta'])
        ->assertJsonCount(2, 'data');
});

test('form listing returns paginated metadata', function () {
    createRakacaForm('Paginated Form');

    $issued = $this->tokens->issue('Client', ['rakaca.form.read']);

    $this->getJson('/api/rakaca/v1/forms', ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('meta.per_page', 20)
        ->assertJsonPath('meta.current_page', 1);
});

test('show returns single form with response form schema', function () {
    $form = createRakacaForm('Detailed Form');

    $issued = $this->tokens->issue('Client', ['rakaca.form.read']);

    $this->getJson('/api/rakaca/v1/forms/'.$form->id, ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertOk()
        ->assertJsonPath('data.id', $form->id)
        ->assertJsonCount(1, 'data.response_form_schema');
});

test('inactive form is not found', function () {
    $form = createRakacaForm('Inactive Form', false);

    $issued = $this->tokens->issue('Client', ['rakaca.form.read']);

    $this->getJson('/api/rakaca/v1/forms/'.$form->id, ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertNotFound();
});

test('token without form.read ability is forbidden', function () {
    $issued = $this->tokens->issue('Client', ['rakaca.submission.read']);

    $this->getJson('/api/rakaca/v1/forms', ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertForbidden();
});

test('missing token is unauthorized', function () {
    $this->getJson('/api/rakaca/v1/forms')->assertUnauthorized();
});

test('can create submission with submission.write ability', function () {
    $form = createRakacaForm('Submit Form');

    $issued = $this->tokens->issue('Client', ['rakaca.submission.write']);

    $this->postJson('/api/rakaca/v1/submissions', [
        'rakaca_form_id' => $form->id,
        'items' => [
            'summary' => 'Laptop rusak',
            'note' => 'Tolong diperbaiki',
        ],
    ], ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertCreated()
        ->assertJsonPath('data.rakaca_form_id', $form->id)
        ->assertJsonPath('data.items.data.summary', 'Laptop rusak')
        ->assertJsonStructure(['data' => ['id', 'code', 'status', 'items']]);

    $this->assertDatabaseHas('rakaca_submissions', [
        'rakaca_form_id' => $form->id,
    ]);
});

test('cannot create submission without submission.write ability', function () {
    $form = createRakacaForm('Another Form');

    $issued = $this->tokens->issue('Client', ['rakaca.form.read']);

    $this->postJson('/api/rakaca/v1/submissions', [
        'rakaca_form_id' => $form->id,
        'items' => ['summary' => 'x'],
    ], ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertForbidden();
});

test('submission list requires submission.read ability', function () {
    $issued = $this->tokens->issue('Client', ['rakaca.submission.read']);

    $this->getJson('/api/rakaca/v1/submissions', ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertOk()
        ->assertJsonStructure(['data', 'meta']);
});

test('list submissions returns existing submissions', function () {
    $form = createRakacaForm('List Form');

    $submission = RakacaSubmission::create([
        'user_uuid' => (string) Str::uuid(),
        'rakaca_form_id' => $form->id,
        'code' => 'SUB-TEST-001',
        'status' => 'menunggu-berkas',
        'items' => ['data' => ['summary' => 'test']],
    ]);

    $issued = $this->tokens->issue('Client', ['rakaca.submission.read']);

    $this->getJson('/api/rakaca/v1/submissions', ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.code', 'SUB-TEST-001');
});

test('token immediately blocks requests after revocation', function () {
    $issued = $this->tokens->issue('Client', ['rakaca.form.read']);

    $this->getJson('/api/rakaca/v1/forms', ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertOk();

    $this->tokens->revoke($issued['model']);

    $this->getJson('/api/rakaca/v1/forms', ['Authorization' => 'Bearer '.$issued['plain']])
        ->assertUnauthorized();
});

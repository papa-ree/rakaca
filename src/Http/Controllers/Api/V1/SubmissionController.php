<?php

namespace Paparee\Rakaca\Http\Controllers\Api\V1;

use Bale\Api\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaSubmission;

class SubmissionController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $submissions = RakacaSubmission::query()
            ->with(['form:id,name,slug'])
            ->orderByDesc('created_at')
            ->paginate((int) $request->integer('per_page', 20));

        return $this->jsonPaginated($submissions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rakaca_form_id' => ['required', 'uuid', 'exists:rakaca_forms,id'],
            'items' => ['required', 'array'],
        ]);

        $form = Form::query()->where('actived', true)->find($validated['rakaca_form_id']);

        if (! $form) {
            throw ValidationException::withMessages([
                'rakaca_form_id' => 'The selected form is inactive or does not exist.',
            ]);
        }

        $submission = RakacaSubmission::create([
            'user_uuid' => $request->user('api-token')->getAuthIdentifier(),
            'rakaca_form_id' => $form->id,
            'code' => strtoupper(uniqid('sub_')),
            'status' => SubmissionStatus::MenungguBerkas->value,
            'items' => [
                'id' => Str::uuid()->toString(),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
                'data' => $validated['items'],
            ],
        ]);

        return $this->jsonResponse($submission->load('form:id,name,slug'), 201);
    }
}

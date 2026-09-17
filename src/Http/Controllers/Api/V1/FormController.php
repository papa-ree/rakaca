<?php

namespace Paparee\Rakaca\Http\Controllers\Api\V1;

use Bale\Api\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Paparee\Rakaca\Models\Form;

class FormController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $forms = Form::query()
            ->where('actived', true)
            ->with('service:id,name')
            ->orderByDesc('created_at')
            ->paginate((int) $request->integer('per_page', 20));

        return $this->jsonPaginated($forms);
    }

    public function show(Form $form): JsonResponse
    {
        if (! $form->actived) {
            abort(404, 'Form not found.');
        }

        $form->load('service:id,name');

        return $this->jsonResponse($form);
    }
}

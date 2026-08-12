<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Convention base class for LeadHub form-request validators.
 *
 * Why this directory exists at all:
 *
 *   Before this base class, every controller in app/Http/Controllers
 *   inline-validated with `$request->validate([...])`.  The same lead
 *   payload rules drifted between Api\V1\LeadController,
 *   PublicFormController, and LeadIngestionService — already producing
 *   bugs (only LeadIngestionService lower-cased the email).  New
 *   developers copied whichever controller they happened to look at
 *   first.
 *
 *   App\Http\Requests is now the canonical home for request-shape
 *   rules.  Each Subclass carries the rules() + authorize() logic
 *   and gets type-hinted by the controller method.  Filament resource
 *   forms can re-use the same rule arrays via Rule::imported() or by
 *   instantiating the request class statically.
 *
 * Migration plan:
 *
 *   - New controllers MUST type-hint a Subclass instead of plain Request.
 *   - Existing controllers with shared rule sets (lead intake, form
 *     submission, invitation acceptance) migrate first.
 *   - Single-use validations (one-line $request->validate) can stay
 *     inline if they don't repeat anywhere else.
 *
 * Convention:
 *
 *   - Subclass directory mirrors the controller directory:
 *       App\Http\Controllers\Api\V1\LeadController
 *       App\Http\Requests\Api\V1\StoreLeadRequest
 *   - Request class names end in Request.
 *   - authorize() defaults to true on this base; override per subclass
 *     when an action needs a tenant-ownership or role check.
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * By default any authenticated user can make the request.  Override
     * in subclasses that need ownership / role / scope checks.
     */
    public function authorize(): bool
    {
        return true;
    }
}

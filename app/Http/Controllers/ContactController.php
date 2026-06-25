<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Services\Interfaces\ContactServiceInterface;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Response;

/**
 * Class ContactController
 *
 * Handles contact form submissions.
 */
class ContactController extends Controller
{
    /**
     * @var ContactServiceInterface
     */    
    protected $contactService;

    /**
     * ContactController constructor.
     *
     * @param ContactServiceInterface $contactService
     */
    public function __construct(ContactServiceInterface $contactService)
    {
        $this->contactService = $contactService;
    }
    
    /**
     * Store a contact message.
     *
     * @param StoreContactRequest $request
     * @return JsonResponse
     */
    public function store(StoreContactRequest $request)
    {
        $contact = $this->contactService->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'contact' => new ContactResource($contact)
            ]
        ])->setStatusCode(Response::HTTP_CREATED);
    }
}
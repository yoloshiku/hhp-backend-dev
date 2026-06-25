<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsletterSubscriber\StoreNewsletterSubscriberRequest;
use App\Services\Interfaces\NewsletterSubscriberServiceInterface;
use App\Http\Resources\NewsletterSubscriberResource;
use Illuminate\Http\Response;

/**
 * Class NewsletterSubscriberController
 *
 * Handles newsletter subscriber form submissions.
 */
class NewsletterSubscriberController extends Controller
{
    /**
     * @var NewsletterSubscriberServiceInterface
     */    
    protected $newsletterSubscriberService;

    /**
     * NewsletterSubscriberController constructor.
     *
     * @param NewsletterSubscriberServiceInterface $newsletterSubscriberService
     */
    public function __construct(NewsletterSubscriberServiceInterface $newsletterSubscriberService)
    {
        $this->newsletterSubscriberService = $newsletterSubscriberService;
    }

    /**
     * Store a Newsletter Subscriber.
     *
     * @param StoreNewsletterSubscriberRequest $request
     * @return JsonResponse
     */
    public function store(StoreNewsletterSubscriberRequest $request)
    {
        $newsletterSubscriber = $this->newsletterSubscriberService->store(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Newsletter Subscriber registered successfully',
            'data' => [
                'newsletterSubscriber' => new NewsletterSubscriberResource($newsletterSubscriber)
            ]
        ])->setStatusCode(Response::HTTP_CREATED);
    }    
}

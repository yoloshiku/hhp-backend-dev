<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Services\Interfaces\ContactServiceInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageMail;

/**
 * Class ContactService
 *
 * Handles business logic related to contact messages.
 */
class ContactService implements ContactServiceInterface
{
    /**
     * @var ContactRepositoryInterface
     */    
    protected $contactRepository;

    /**
     * ContactService constructor.
     *
     * @param ContactRepositoryInterface $contactRepository
     */    
    public function __construct(ContactRepositoryInterface $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }
    
    /**
     * Store a new contact message,
     * and send a message to the email
     * specified in .env .
     *
     * @param array $data
     * @return Contact
     */
    public function store(array $data): Contact
    {
        $contact = $this->contactRepository->create($data);
        
        Mail::to(config('mail.admin')) // or admin email
            ->send(new ContactMessageMail($data));
        
        return $contact;
    }
}
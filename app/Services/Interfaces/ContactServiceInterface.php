<?php

namespace App\Services\Interfaces;

use App\Models\Contact;

/**
 * Interface ContactServiceInterface
 *
 * Defines the contract for business logic related to contact messages.
 * 
 * This service layer sits between the controller and the repository
 * and is responsible for handling application logic when a user
 * submits a contact form.
 */
interface ContactServiceInterface
{
    /**
     * Store a new contact message.
     *
     * This method receives validated data from the controller,
     * applies any necessary business logic, and delegates
     * persistence to the repository layer.
     *
     * Example input:
     * [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'message' => 'Hello, I would like more information.'
     * ]
     *
     * @param array $data Validated contact form data
     * @return Contact The newly created Contact model instance
     */    
    public function store(array $data): Contact;
}
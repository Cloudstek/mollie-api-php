<?php

namespace Mollie\API\Resource;

use Mollie\API\Model;

class CustomerResource extends Base\CustomerResourceBase
{
    /**
     * Get customer
     *
     * @param Customer|string $id
     * @return Model\Customer
     */
    public function get($id = null)
    {
        // Get customer ID
        $id = $this->_getCustomerID($id);

        // Get customer
        $resp = $this->api->request->get("/customers/{$id}");

        // Return customer model
        return new Model\Customer($this->api, $resp);
    }

    /**
     * Get all customers
     * @return Model\Customer[]
     */
    public function all()
    {
        $items = [];

        // Get all customers
        $resp = $this->api->request->getAll("/customers");

        if(!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                $items[] = new Model\Customer($this->api, $item);
            }
        }

        return $items;
    }

    /**
     * Get all customers as generator
     * @return Generator
     */
    public function yieldAll()
    {
        // Get all customers
        $resp = $this->api->request->getAll("/customers");

        // Yield all customers
        if(!empty($resp) && is_array($resp)) {
            foreach ($resp as $item) {
                yield new Model\Customer($this->api, $item);
            }
        }
    }

    /**
     * Create customer
     *
     * @see https://www.mollie.com/nl/docs/reference/customers/create
     * @param string $name Customer name
     * @param string $email Customer email
     * @param string $locale Allow you to preset the language to be used in the payment screens shown to the consumer.
     * @param array $metadata Metadata for this customer
     * @return Model\Customer
     */
    public function create($name, $email, $locale = null, array $metadata = null)
    {
        // Convert metadata to JSON
        $metadata = !empty($metadata) ? json_encode($metadata) : null;

        // Create customer
        $resp = $this->api->request->post("/customers", [
            'name'      => $name,
            'email'     => $email,
            'locale'    => $locale,
            'metadata'  => $metadata,
        ]);

        // Return customer model
        return new Model\Customer($this->api, $resp);
    }

    /**
     * Customer payment resource
     * @return Customer\PaymentResource
     */
    public function payment()
    {
        if (empty($this->customer)) {
            throw new \BadMethodCallException("No customer ID was given");
        }

        return new Customer\PaymentResource($this->api, $this->customer);
    }

    /**
     * Customer mandate resource
     *
     * @param Model\Mandate|string $mandate
     * @return Customer\MandateResource
     */
    public function mandate($mandate = null)
    {
        if (empty($this->customer)) {
            throw new \BadMethodCallException("No customer ID was given");
        }

        return new Customer\MandateResource($this->api, $this->customer, $mandate);
    }

    /**
     * Customer subscription resource
     *
     * @param Model\Subscription|string $subscription
     * @return Customer\SubscriptionResource
     */
    public function subscription($subscription = null)
    {
        if (empty($this->customer)) {
            throw new \BadMethodCallException("No customer ID was given");
        }

        return new Customer\SubscriptionResource($this->api, $this->customer, $subscription);
    }
}

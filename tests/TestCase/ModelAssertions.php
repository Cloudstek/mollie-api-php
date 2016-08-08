<?php

namespace Mollie\API\Tests\TestCase;

use Mollie\API\Model\Base\ModelBase;

trait ModelAssertions
{
    /**
     * Check model against response object
     * @param ModelBase $model Resource model to validate
     * @param array|object $reference Reference object (raw response)
     * @param array $mapping Field mapping between resource and reference object
     */
    protected function assertModel(ModelBase $model, $reference, array $mapping)
    {
        foreach($mapping as $k => $v) {
            $k = is_int($k) ? $v : $k; // Handle non-associative arrays

            if(is_array($reference)) {
                $reference = (object) $reference;
            }

            if(!property_exists($reference, $k)) {
                continue;
            }

            // ISO 8601 Date
            if (preg_match('/.+(Datetime|Date)$/', $k) && isset($reference->$k)) {
                $this->assertInstanceOf(\DateTime::class, $model->$v);
                $this->assertEquals(strtotime($reference->$k), $model->$v->format('U'));
                continue;
            }

            // ISO 8601 Duration
            if (preg_match('/.+(Period)$/', $k) && preg_match('^P.+', $reference->$k)) {
                $this->assertInstanceOf(\DateInterval::class, $model->$v);
                continue;
            }

            // JSON metadata
            if($k == 'metadata') {
                $this->assertEquals(json_decode($reference->$k), $model->$v);
                continue;
            }

            // Amount
            if(preg_match('/amount.*$/', $k) && !is_object($reference->$k)) {
                $this->assertTrue(is_float($model->$v) || is_integer($model->$v));
                $this->assertEquals($reference->$k, $model->$v);
                continue;
            }

            $this->assertEquals($reference->$k, $model->$v);
        }
    }

    /**
     * Check model methods
     * @param ModelBase $model Resource model to validate
     * @param array $methods Methods that must be available and callable
     */
    protected function assertModelMethods(ModelBase $model, array $methods)
    {
        foreach($methods as $method) {
            $this->assertTrue(method_exists($model, $method));
            $this->assertTrue(is_callable([$model, $method]));
        }
    }
}
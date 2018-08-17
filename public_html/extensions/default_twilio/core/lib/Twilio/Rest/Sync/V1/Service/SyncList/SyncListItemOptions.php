<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Sync\V1\Service\SyncList;

use Twilio\Options;
use Twilio\Values;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 */
abstract class SyncListItemOptions
{
    /**
     * @param integer $ttl The ttl
     *
     * @return CreateSyncListItemOptions Options builder
     */
    public static function create($ttl = Values::NONE)
    {
        return new CreateSyncListItemOptions($ttl);
    }

    /**
     * @param string $order  The order
     * @param string $from   The from
     * @param string $bounds The bounds
     *
     * @return ReadSyncListItemOptions Options builder
     */
    public static function read($order = Values::NONE, $from = Values::NONE, $bounds = Values::NONE)
    {
        return new ReadSyncListItemOptions($order, $from, $bounds);
    }

    /**
     * @param array   $data The data
     * @param integer $ttl  The ttl
     *
     * @return UpdateSyncListItemOptions Options builder
     */
    public static function update($data = Values::NONE, $ttl = Values::NONE)
    {
        return new UpdateSyncListItemOptions($data, $ttl);
    }
}

class CreateSyncListItemOptions extends Options
{
    /**
     * @param integer $ttl The ttl
     */
    public function __construct($ttl = Values::NONE)
    {
        $this->options['ttl'] = $ttl;
    }

    /**
     * The ttl
     *
     * @param integer $ttl The ttl
     *
     * @return $this Fluent Builder
     */
    public function setTtl($ttl)
    {
        $this->options['ttl'] = $ttl;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString()
    {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Sync.V1.CreateSyncListItemOptions '.implode(' ', $options).']';
    }
}

class ReadSyncListItemOptions extends Options
{
    /**
     * @param string $order  The order
     * @param string $from   The from
     * @param string $bounds The bounds
     */
    public function __construct($order = Values::NONE, $from = Values::NONE, $bounds = Values::NONE)
    {
        $this->options['order'] = $order;
        $this->options['from'] = $from;
        $this->options['bounds'] = $bounds;
    }

    /**
     * The order
     *
     * @param string $order The order
     *
     * @return $this Fluent Builder
     */
    public function setOrder($order)
    {
        $this->options['order'] = $order;
        return $this;
    }

    /**
     * The from
     *
     * @param string $from The from
     *
     * @return $this Fluent Builder
     */
    public function setFrom($from)
    {
        $this->options['from'] = $from;
        return $this;
    }

    /**
     * The bounds
     *
     * @param string $bounds The bounds
     *
     * @return $this Fluent Builder
     */
    public function setBounds($bounds)
    {
        $this->options['bounds'] = $bounds;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString()
    {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Sync.V1.ReadSyncListItemOptions '.implode(' ', $options).']';
    }
}

class UpdateSyncListItemOptions extends Options
{
    /**
     * @param array   $data The data
     * @param integer $ttl  The ttl
     */
    public function __construct($data = Values::NONE, $ttl = Values::NONE)
    {
        $this->options['data'] = $data;
        $this->options['ttl'] = $ttl;
    }

    /**
     * The data
     *
     * @param array $data The data
     *
     * @return $this Fluent Builder
     */
    public function setData($data)
    {
        $this->options['data'] = $data;
        return $this;
    }

    /**
     * The ttl
     *
     * @param integer $ttl The ttl
     *
     * @return $this Fluent Builder
     */
    public function setTtl($ttl)
    {
        $this->options['ttl'] = $ttl;
        return $this;
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString()
    {
        $options = array();
        foreach ($this->options as $key => $value) {
            if ($value != Values::NONE) {
                $options[] = "$key=$value";
            }
        }
        return '[Twilio.Sync.V1.UpdateSyncListItemOptions '.implode(' ', $options).']';
    }
}
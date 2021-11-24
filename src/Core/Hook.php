<?php

namespace UPFlex\MixUp\Core;

use UPFlex\MixUp\Core\Interfaces\IHooks;

abstract class Hook implements IHooks
{
    private array $hooks = [];
    private int $accepted_args;
    private array $callback;
    private string $name;
    private int $priority;

    /**
     * @param $name
     * @param $callback
     * @param int $priority
     * @param int $accepted_args
     */
    public function addHook($name, $callback, int $priority = 10, int $accepted_args = 1): void
    {
        $this->hooks = array_merge($this->hooks, [
            [
                'accepted_args' => $accepted_args,
                'callback' => $callback,
                'name' => $name,
                'priority' => $priority,
            ]
        ]);
    }

    /**
     * @return int
     */
    public function getAcceptedArgs(): int
    {
        return $this->accepted_args;
    }

    /**
     * @return array
     */
    public function getCallback(): array
    {
        return $this->callback;
    }

    /**
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $accepted_args
     */
    public function setAcceptedArgs(int $accepted_args): void
    {
        $this->accepted_args = $accepted_args;
    }

    /**
     * @param array $callback
     */
    public function setCallback(array $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}

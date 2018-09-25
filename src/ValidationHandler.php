<?php
/**
 * kiwi-suite/command-bus-validation (https://github.com/kiwi-suite/command-bus-validation)
 *
 * @package kiwi-suite/command-bus-validation
 * @link https://github.com/kiwi-suite/command-bus-validation
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace KiwiSuite\CommandBusValidation;

use KiwiSuite\CommandBus\Result\Result;
use KiwiSuite\Contract\CommandBus\CommandInterface;
use KiwiSuite\Contract\CommandBus\DispatchInterface;
use KiwiSuite\Contract\CommandBus\HandlerInterface;
use KiwiSuite\Contract\CommandBus\ResultInterface;
use KiwiSuite\Validation\Validator;

final class ValidationHandler implements HandlerInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * ValidationHandler constructor.
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param CommandInterface $command
     * @param DispatchInterface $dispatcher
     * @throws \Exception
     * @return ResultInterface
     */
    public function handle(CommandInterface $command, DispatchInterface $dispatcher): ResultInterface
    {
        if (!$this->validator->supports($command)) {
            return $dispatcher->dispatch($command);
        }

        $validationResult = $this->validator->validate($command);
        if ($validationResult->isSuccessful()) {
            return $dispatcher->dispatch($command);
        }

        return new Result(ResultInterface::STATUS_ERROR, $command, (array) $validationResult->all());
    }
}

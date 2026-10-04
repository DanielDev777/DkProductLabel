<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel\Validation;

use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\InsertCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ProductLabelColorValidator implements EventSubscriberInterface
{
    public const VIOLATION_INVALID_COLOR = 'DK_PRODUCT_LABEL_INVALID_COLOR';

    private const HEX_COLOR_PATTERN = '/^#[0-9A-Fa-f]{6}$/';

    private const MESSAGE = 'The color must be a hex value like #FF0000.';

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => 'validateColor',
        ];
    }

    public function validateColor(PreWriteValidationEvent $event): void
    {
        $violations = new ConstraintViolationList();

        foreach ($event->getCommandsForEntity(ProductLabelDefinition::ENTITY_NAME) as $command) {
            if (!$this->writesColor($command)) {
                continue;
            }

            $color = $command->getPayload()['color'];
            if (!$this->isValidColor($color)) {
                $violations->add($this->createViolation($command, $color));
            }
        }

        if ($violations->count() > 0) {
            $event->getExceptions()->add(new WriteConstraintViolationException($violations));
        }
    }

    private function writesColor(WriteCommand $command): bool
    {
        return ($command instanceof InsertCommand || $command instanceof UpdateCommand)
            && \array_key_exists('color', $command->getPayload());
    }

    private function isValidColor(mixed $color): bool
    {
        return \is_string($color) && preg_match(self::HEX_COLOR_PATTERN, $color) === 1;
    }

    private function createViolation(WriteCommand $command, mixed $color): ConstraintViolation
    {
        return new ConstraintViolation(
            self::MESSAGE,
            self::MESSAGE,
            [],
            null,
            $command->getPath() . '/color',
            $color,
            null,
            self::VIOLATION_INVALID_COLOR,
        );
    }
}

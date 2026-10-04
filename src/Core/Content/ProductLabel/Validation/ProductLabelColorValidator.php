<?php

declare(strict_types=1);

namespace Dk\ProductLabel\Core\Content\ProductLabel\Validation;

use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\InsertCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\UpdateCommand;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Shopware\Core\Framework\Validation\WriteConstraintViolationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ProductLabelColorValidator implements EventSubscriberInterface
{
    public const VIOLATION_INVALID_COLOR = 'DK_PRODUCT_LABEL_INVALID_COLOR';

    private const HEX_COLOR_PATTERN = '/^#[0-9A-Fa-f]{6}$/';

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
            if (!$command instanceof InsertCommand && !$command instanceof UpdateCommand) {
                continue;
            }

            $payload = $command->getPayload();
            if (!\array_key_exists('color', $payload)) {
                continue;
            }

            $color = $payload['color'];
            if (\is_string($color) && preg_match(self::HEX_COLOR_PATTERN, $color) === 1) {
                continue;
            }

            $message = 'The color must be a hex value like #FF0000.';
            $violations->add(new ConstraintViolation(
                $message,
                $message,
                [],
                null,
                $command->getPath() . '/color',
                $color,
                null,
                self::VIOLATION_INVALID_COLOR,
            ));
        }

        if ($violations->count() > 0) {
            $event->getExceptions()->add(new WriteConstraintViolationException($violations));
        }
    }
}

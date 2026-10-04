<?php

declare(strict_types=1);

use Dk\ProductLabel\Core\Content\Product\ProductLabelExtension;
use Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelProduct\ProductLabelProductDefinition;
use Dk\ProductLabel\Core\Content\ProductLabel\Aggregate\ProductLabelTranslation\ProductLabelTranslationDefinition;
use Dk\ProductLabel\Core\Content\ProductLabel\ProductLabelDefinition;
use Dk\ProductLabel\Core\Content\ProductLabel\Validation\ProductLabelColorValidator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ProductLabelDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => ProductLabelDefinition::ENTITY_NAME]);
    $services->set(ProductLabelTranslationDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => ProductLabelTranslationDefinition::ENTITY_NAME]);
    $services->set(ProductLabelProductDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => ProductLabelProductDefinition::ENTITY_NAME]);

    $services->set(ProductLabelExtension::class)
        ->tag('shopware.entity.extension');

    $services->set(ProductLabelColorValidator::class)
        ->tag('kernel.event_subscriber');
};

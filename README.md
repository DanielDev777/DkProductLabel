# DkProductLabel

Shopware 6.7 plugin for product labels like "New", "Sale" or "Limited Edition". Labels are managed in the administration, assigned to products and shown in the storefront.

## Setup

Requirements: Docker with Docker Compose. Ports 80 and 3306 need to be free.

```bash
git clone https://github.com/DanielDev777/DkProductLabel.git
cd DkProductLabel
docker compose up -d
```

The first start takes a few minutes. Wait until `docker compose logs -f shop` shows "container IS READY", then install the plugin and build the assets:

```bash
docker compose exec shop bash custom/plugins/DkProductLabel/bin/setup.sh
```

- Storefront: http://localhost
- Administration: http://localhost/admin (user `admin`, password `shopware`)

Labels are managed under Catalogues > Product labels. They are assigned to products in the "Labels" tab on the product detail page.

### Tests and code quality

Run inside the plugin directory in the container:

```bash
docker compose exec -w /var/www/html/custom/plugins/DkProductLabel shop composer test
docker compose exec -w /var/www/html/custom/plugins/DkProductLabel shop composer phpstan
docker compose exec -w /var/www/html/custom/plugins/DkProductLabel shop composer cs-check
```

On Windows with Git Bash, prefix these with `MSYS_NO_PATHCONV=1`, otherwise Git Bash rewrites the container path.

## Design decisions

**Repository and environment.** The repository is the plugin itself. Shopware is not part of it: locally it comes from the `dockware/shopware` image, in CI from `shopware/github-actions/setup-extension`. Both use Shopware 6.7.14.2.

**Data model.** `product_label` with a translation table for the name and a ManyToMany mapping table to `product`. The association is added to the product with an `EntityExtension`, so core code stays untouched. The mapping table contains `product_version_id` like the core `product_tag` table, so label assignments work with the product versioning in the administration. The color is validated in a `PreWriteValidationEvent` subscriber (`#RRGGBB`), because it ends up in an inline style in the storefront and can also be written through the API. Services are registered in `services.php`, which is what the 6.7 plugin generator creates.

**Storefront.** A subscriber adds the `productLabels` association to the criteria of every storefront product query (detail page, listing, search, suggest, cross-selling, product sliders). The association is already filtered to active labels within their validity period and sorted by priority, so the templates only render. The current time comes from an injected clock and is converted to UTC, because the DAL stores dates in UTC. One Twig partial renders the labels and is included in the product box and the buy widget. Since 6.7 the product detail page is built from CMS elements, so the buy widget is the place where the product is available. The SCSS component uses the Bootstrap variables of the theme, the color of each label is passed as a CSS custom property.

**Administration.** The module (list and detail page) follows the structure of the core manufacturer module. The product tab is added in the `sw_product_detail_content_tabs_additional` block, its route is registered with `routeMiddleware`. The assigned labels are part of the product, so they are saved with the normal product save button.

**Tests.** An integration test writes a label with translations and a product assignment through the DAL and reads it back from both sides. A second one checks that an invalid color is rejected. The unit tests cover the subscriber: the filters and sorting with a fixed clock, the UTC conversion and, with a data provider, every subscribed event.

**CI.** GitHub Actions runs three jobs: PHP-CS-Fixer (PER-CS 2.0, same rules as Shopware's cs-fixer action), PHPStan on level max, and a job that installs Shopware, checks that the plugin is installed and active, reinstalls it and runs the tests. The PHPStan versions in `tools/composer.json` are pinned to the ones Shopware 6.7.14.2 uses, because Shopware's own PHPStan is loaded through its autoloader in CI and different versions conflict.

## What I would improve with more time

- **Labels for variants.** Variants only show their own labels, not the ones of the parent product. Core solves this for tags with the `Inherited` flag, which needs an additional column in the `product` table.
- **Search in the label list.** The administration list has no search yet.
- **Products on the label detail page.** A list of the products that have the label, so you can see where a label is used without opening every product.

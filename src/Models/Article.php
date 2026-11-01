<?php

namespace Kalakotra\Baudigit\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\DropdownField;
use SilverStripe\i18n\i18nEntityProvider;
use Kalakotra\Baudigit\Utils\DataObjectPermissions;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\SiteConfig\SiteConfig;

class Article extends DataObject implements PermissionProvider, i18nEntityProvider
{
    use DataObjectPermissions;
    private static $table_name = 'baudigit_Article';

    private static $singular_name = 'Artikel';

    private static $plural_name = 'Artikel';

    private static $default_sort = 'Title ASC';

    public function provideI18nEntities()
    {
        return [
            __CLASS__ . '.SINGULARNAME' => 'Artikel',
            __CLASS__ . '.PLURALNAME' => 'Artikel',
            __CLASS__ . '.PLURALS' => [
                'one' => 'Artikel',
                'other' => '{count} Artikel',
            ],
        ];
    }

    private static $db = [
        'InternalID' => 'Varchar(100)',
        'Title' => 'Varchar(255)',
        'Unit' => 'Int', // 0=Stück, 1=m, 2=m², 3=m³, 4=l, 5=kg, 6=Stunde, 7=Sonstige
        'BuyPriceNetto' => 'Decimal(19,2)',
        'BuyPriceBrutto' => 'Decimal(19,2)',
        'SellPriceNetto' => 'Decimal(19,2)',
        'SellPriceBrutto' => 'Decimal(19,2)',
        'Description' => 'Text',
    ];

    private static $has_one = [
        
    ];

    private static $summary_fields = [
        'InternalID' => 'Interne ID',
        'Title' => 'Titel',
        'Unit' => 'Einheit',
        'BuyPriceNetto' => 'EP netto',
        'BuyPriceBrutto' => 'EP brutto',
        'SellPriceNetto' => 'VP netto',
        'SellPriceBrutto' => 'VP brutto',
        'Description' => 'Beschreibung',
    ];

    private static $field_labels = [
        'InternalID' => 'Interne ID',
        'Title' => 'Titel',
        'Unit' => 'Einheit',
        'BuyPriceNetto' => 'EP netto',
        'BuyPriceBrutto' => 'EP brutto',
        'SellPriceNetto' => 'VP netto',
        'SellPriceBrutto' => 'VP brutto',
        'Description' => 'Beschreibung',
    ];
    public function getUnitLabel()
    {
        $units = $this->getUnitOptions();
        return isset($units[$this->Unit]) ? $units[$this->Unit] : 'Unbekannt';
    }

    public function getUnitOptions()
    {
        return [
            0 => 'Stück',
            1 => 'm',
            2 => 'm²',
            3 => 'm³',
            4 => 'l',
            5 => 'kg',
            6 => 'Stunde',
            7 => 'Sonstige',
        ];
    }

    // return unit by __BT-130 format
    public function getUnitShortLabel()
    {
        $units = [
            0 => 'STK',
            1 => 'M',
            2 => 'M2',
            3 => 'M3',
            4 => 'L',
            5 => 'KG',
            6 => 'STD',
            7 => 'SNS',
        ];
        return isset($units[$this->Unit]) ? $units[$this->Unit] : 'UNB';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('BuyPriceBrutto')->setReadonly(true);

        $fields->dataFieldByName('SellPriceBrutto')->setReadonly(true);

        $fields->addFieldToTab('Root.Main', DropdownField::create(
            'Unit',
            'Einheit',
            [
                0 => 'Stück',
                1 => 'm',
                2 => 'm²',
                3 => 'm³',
                4 => 'l',
                5 => 'kg',
                6 => 'Stunde',
                7 => 'Sonstige',
            ]
        ), 'BuyPriceNetto');
        
        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $config = SiteConfig::current_site_config();

        if ($this->BuyPriceNetto) {
            $this->BuyPriceBrutto = $this->BuyPriceNetto + $this->BuyPriceNetto * ($config->InvoiceTax / 100);
        }

        if ($this->SellPriceNetto) {
            $this->SellPriceBrutto = $this->SellPriceNetto + $this->SellPriceNetto * ($config->InvoiceTax / 100);
        }

        // if no InternalID is set, generate one
        if (!$this->InternalID) {
            $lastArticle = Article::get()->sort('Created', 'DESC')->first();
            if ($lastArticle && $lastArticle->InternalID) {
                // extract number from InternalID (format ART-0001)
                if (preg_match('/ART-(\d{4})/', $lastArticle->InternalID, $matches)) {
                    $lastNumber = (int)$matches[1];
                    $newNumber = $lastNumber + 1;
                    $this->InternalID = 'ART-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                } else {
                    $this->InternalID = 'ART-0001';
                }
            } else {
                $this->InternalID = 'ART-0001';
            }
        }

        // check if InternalID is unique if not, append a suffix
        $existingArticle = Article::get()->filter('InternalID', $this->InternalID)->exclude('ID', $this->ID)->first();
        if ($existingArticle) {
            $suffix = 1;
            do {
                $newInternalID = $this->InternalID . '-' . $suffix;
                $existingArticle = Article::get()->filter('InternalID', $newInternalID)->exclude('ID', $this->ID)->first();
                $suffix++;
            } while ($existingArticle);
            $this->InternalID = $newInternalID;
        }
    }

    public function formatPrice($price, $withCurrency = true) {
        $value = number_format($price, 2, ',', '.') . ' €';
        return $withCurrency ? $value : rtrim($value, ' €');
    }

}
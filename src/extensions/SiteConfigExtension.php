<?php

namespace SilverCommerce\Settings\Extensions;

use SilverStripe\i18n\i18n;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class SiteConfigExtension extends DataExtension
{
    private static $db = [
        "SiteLocale" => "Varchar(5)",
        "ContactPhone" => "Varchar(25)",
        "ContactEmail" => "Varchar(255)",
        "ContactAddress" => "Text",
        "ShowPriceAndTax" => "Boolean",
        "ShowPriceTaxString" => "Boolean"
    ];

    private static $has_one = [
        "CardLogos" => Image::class
    ];

    private static $casting = [
        "InlineContactAddress" => "Text",
        "TrimmedContactPhone" => "Varchar(15)"
    ];

    public function getInlineContactAddress()
    {
        return trim(preg_replace('/\s\s+/', ', ', $this->owner->ContactAddress));
    }

    public function getTrimmedContactPhone()
    {
        return trim(str_replace(" ","",$this->owner->ContactPhone));
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName("ContactPhone");
        $fields->removeByName("ContactEmail");
        $fields->removeByName("ContactAddress");
        $fields->removeByName("SiteLocale");
        $fields->removeByName("ShowPriceAndTax");
        $fields->removeByName("ShowPriceTaxString");

        $fields->addFieldsToTab(
            "Root.Main",
            [
                DropdownField::create(
                    "SiteLocale",
                    $this->owner->fieldLabel("SiteLocale"),
                    i18n::getSources()->getKnownLocales()
                ),
                TextField::create(
                    "ContactPhone",
                    $this->owner->fieldLabel("ContactPhone")
                ),
                TextField::create(
                    "ContactEmail",
                    $this->owner->fieldLabel("ContactEmail")
                ),
                TextareaField::create(
                    "ContactAddress",
                    $this->owner->fieldLabel("ContactAddress")
                )
            ]
        );

        $fields->addFieldToTab(
            "Root.Shop",
            ToggleCompositeField::create(
                'MiscSettings',
                _t("Settings.MiscSettings", "Misc Settings"),
                [
                    CheckboxField::create("ShowPriceAndTax")
                        ->setDescription(_t(
                            "SilverCommerce\Settings.ShowPriceAndTaxDescription",
                            "Show product prices including tax"
                        )),
                    CheckboxField::create("ShowPriceTaxString")
                        ->setDescription(_t(
                            "SilverCommerce\Settings.ShowProductTaxStringDescription",
                            "Show 'inc/exc TAX' after price"
                        )),
                    UploadField::create(
                        "CardLogos",
                        $this->owner->fieldLabel("CardLogos")
                    )
                ]
            )
        );
    }

    public function onBeforeWrite()
    {
        if (empty($this->owner->SiteLocale)) {
            $this->owner->SiteLocale = i18n::config()->default_locale;
        }
    }
}
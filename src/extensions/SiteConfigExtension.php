<?php

namespace SilverCommerce\Settings\Extensions;

use Alcohol\ISO4217;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\i18n\i18n;

class SiteConfigExtension extends DataExtension
{
    private static $db = [
        "SiteLocale" => "Varchar(5)",
        "ContactPhone" => "Varchar(15)",
        "ContactEmail" => "Varchar(255)",
        "ContactAddress" => "Text",
        "ShowPriceAndTax" => "Boolean",
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
                    CheckboxField::create(
                        "ShowPriceAndTax",
                        $this->owner->fieldLabel("ShowPriceAndTax")
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
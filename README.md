# SilverCommerce Global Settings

Core settings for the SilverCommerce platform. As several modules in SilverCommerce
have overlapping settings, this module is designed to hold common settings
across all modules (as well as other more global settings, such as contact details).

This module also injects some system wide settings before controller init, namely:

    i18n::set_locale()
    DBCurrency::$currency_symbol
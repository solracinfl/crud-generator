# Laravel CRUD Generator

[![Build Status](https://travis-ci.org/appzcoder/crud-generator.svg)](https://travis-ci.org/appzcoder/crud-generator.svg)
[![Total Downloads](https://poser.pugx.org/appzcoder/crud-generator/d/total.svg)](https://packagist.org/packages/appzcoder/crud-generator)
[![Latest Stable Version](https://poser.pugx.org/appzcoder/crud-generator/v/stable.svg)](https://packagist.org/packages/appzcoder/crud-generator)
[![License](https://poser.pugx.org/appzcoder/crud-generator/license.svg)](https://packagist.org/packages/appzcoder/crud-generator)

This Generator package provides various generators like CRUD, API, Controller, Model, Migration, View for your painless development of your applications.

## Requirements
    Laravel >=5.1
    PHP >= 5.5.9

## Installation
```
composer require appzcoder/crud-generator --dev
```

## Documentation
Go through to the [detailed documentation](doc#readme)

Fork:
Added the ability to generate all component with one command.  You can generate Model, Controller, Request and List, Detail and View pages.


Example:
 php artisan crud:generate MarketingCampaign 'Marketing Campaign' 'marketing_campaign_id' 'marketing_campaign' true  --fields=‘marketing_campaign_type_id:select, marketing_campaign_lead_source_id:select, marketing_campaign_status_id:select, name:string, description:text, start_date:date, end_date:date, budget:amount, actual:amount, expected_revenue:amount, expected_response:decimal, active:checkbox'

## Screencast

[![Screencast](http://img.youtube.com/vi/831-PFBsYfw/0.jpg)](https://www.youtube.com/watch?v=K2G3kMQtY5Y)

#### If you're still looking for easier one then try this [Admin Panel](https://github.com/appzcoder/laravel-admin)

## Author

[Sohel Amin](http://sohelamin.com) :email: [Email Me](mailto:sohelamincse@gmail.com)

## License

This project is licensed under the MIT License - see the [License File](LICENSE) for details

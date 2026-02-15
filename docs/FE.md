# LiteERP – Brain of Extension Core

This is the core system that powers LiteERP extensions, written in JavaScript.

## Core Structure

**Path:** `resources/js/core`

The core works similarly to Laravel's core architecture.  
All extensions are registered and booted via `ServiceProvider.js`.

To fully understand how the core and extensions interact, you should be familiar with the **Singleton pattern**.

📖 Reference:  
https://refactoring.guru/design-patterns/singleton

---

## Example: HRM Extension

**File:** `extensions/Hrm/Resources/js/autoload.js`

```js
import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'
import HrmApp from './app.jsx'

export default class ServiceProvider extends Extension {
    register() {
        RegisterRoute({
            path: '/hrm',
            element: HrmApp
        })
    }

    boot() {
        console.log('HRM loaded')
    }
}
```
This only register React routers, it can not display menu on sidebar dashboard. To add new menu into dashboard you need consider php hook, because Frontend can not handle user role.

Even maybe you don't need React Router but you keep implement default as:

```js
import Extension from '@core/Extension'
import RegisterRoute from '@core/RegisterRoute'

export default class ServiceProvider extends Extension {
    register() {
        
    }

    boot() {
        console.log('HRM loaded')
    }
}
```

**File:** `extensions/Hrm/Resources/js/app.jsx`

```js
import React, { useState } from 'react';
import DashboardLayout from '@layouts/DashboardLayout'
const HrmApp = () => {
    
    return (
        <DashboardLayout>
            <div className="container mt-5">
                <h1>Hello</h1>
            </div>
        </DashboardLayout>

    );
};

export default HrmApp;

```

## Alias  

    @i18n =>  resources/js/i18n
    @components => resources/js/react/components
    @pages => resources/js/react/pages
    @common => resources/js/react/common
    @redux => resources/js/react/redux
    @libraries => resources/js/react/libraries
    @wrappers => resources/js/react/wrappers
    @layouts => resources/js/react/layouts
    @core => resources/js/core

## i18n

Default core autoload language files and you need use i18n. Folders: 

    extensions\YourExt\Resources\js\i18n\en\message.js
    extensions\YourExt\Resources\js\i18n\vi\message.js
    extensions\YourExt\Resources\js\i18n\ja\message.js

And import: 

    import {useI18n} from '@i18n/useI18n'

## CSS 

Default core autoload css file at:

    extensions\YourExt\Resources\css\autoload.css

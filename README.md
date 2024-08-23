# ajax-plugin-session v1.2.0
session plugin for fmihel/ajax solution
see and install https://github.com/fmihel/ajax

[1. Install plugin](#install)<br/>
[2. Simple use](#simpleuse)<br/>
[3. API](#api)<br/>

# <a name="install">Install plugin</a>

```bash
$ npm i fmihel-ajax-plugin-session
$ composer require fmihel/ajax-plugin-session 
```
run script for remove js files from vendor path 
```bash
$ cd ./vendor/fmihel/ajax-plugin-session && ./composer-after-install.sh && cd ../../../

```


# <a name="simpleuse">Simple use</a>
file struct
```
path
  |-server
  |   |-session
  |   |    |-MySession.php
  |   |-mod.php
  |-index.php
  |
  |-client
      |-index.js
      |-session.js
```

```client/index.js```
```js 
import ajax from 'fmihel-ajax';
import session from './session.js';

session.autorize({ 
    login:'1',
    pass:'1',
})
    .then(()=>{
        return ajax::send({
            to:'server/mod',
            data:{ msg: 'send msg to server',any_num:10,arr:[1,32,4,2]},
        })
    })
    .then((data)=>{
        console.info(data);
        return session.logout();
    })
    .catch((e)=>{
        console.error(e);
    });

```

```client/session.js```
```js 
import ajax from 'fmihel-ajax';
import Session from 'fmihel-ajax-plugin-session';

export default ajax.addPlugin(new Session(ajax));

```

```server/session/MySession.php```
```php
<?php

use fmihel\ajax\plughin\session\SessionDefault;

class MySession extends SessionDefault{

    function __construct()
    {
        $this->users = [
            ['id' => '1', 'login' => '1', 'pass' => '1', 'sid' => '3992', 'rights' => ['admin']],
            ['id' => '2', 'login' => '2', 'pass' => '2', 'sid' => '3993', 'rights' => ['manager']],
        ];
    }

}
```


```index.php```
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/server/session/MySession.php';

use fmihel\ajax\ajax;
use fmihel\ajax\plugin\session\SessionPlugin;

if (ajax::enabled()) {
    try {

        ajax::init([
            'plugins' => [
                new SessionPlugin('MySession'),
            ]
        ]);
        require_once ajax::module();
        ajax::done();

    } catch (\Exception $e) {
        ajax::error($e);
    };

};

```

```server/mod.php``` 
```php
<?php
    use fmihel\ajax;
    error_log(print_r(ajax::$data,true));
    ajax::out('hi, from server');
    
```

# <a name="api">API</a>
#####  ``server/php``

class ```SessionPlugin```
|method|result|param|notes|
|----|----|----|----|
|__construct(string `$sessionClass`,array `$params`)|||create session plugin and connect to session handler  |
|||$sessionClass`|name of session handler, see `iSession` interface |
|||$params|array of addition params: <br> `exclude` - list of path that will be ignored by the plugin, path may be as `string` or `function($path)` - return true if path need ignored |
|||||




#####  ``client/javascript``
class ```Session``` 
|method|result|notes|
|----|----|----|
|autorize({login,pass,sid})|promise|try autorize with login and pass or sid(session id)|
|logout()|promise|break current session|
|enabled()|bool|session status|
|on(event,callback)|function|event listener. event = ``'autorize'`` or ``'logout'``,return function for off listen|
||||




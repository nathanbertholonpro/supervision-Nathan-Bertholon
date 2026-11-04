# Vos tests (séance 8)

Écrivez ici **au moins deux tests** qui protègent une règle de gestion que vous avez codée.

```php
<?php

namespace Tests\Feature\Etudiant;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('etudiant')]
class MesReglesTest extends TestCase
{
    use RefreshDatabase;

    public function test_…(): void
    {
        // Préparer · Agir · Vérifier
    }
}
```

Un bon test **échoue** quand on casse la règle : cassez-la exprès (`>=` au lieu de `>`, une condition
supprimée…), relancez `php artisan test --group=etudiant`, constatez le rouge, puis remettez le code.

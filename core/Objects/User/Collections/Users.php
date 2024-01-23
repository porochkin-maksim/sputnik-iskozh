<?php declare(strict_types=1);

namespace Core\Objects\User\Collections;

use App\Models\User;
use Core\Collections\WrongClassException;
use Illuminate\Support\Collection;

class Users extends Collection
{
    public function __construct($items = [])
    {
        foreach ($items as $item) {
            if(!$item instanceof User) {
                throw new WrongClassException(get_class($item));
            }
        }
        parent::__construct($items);
    }
}

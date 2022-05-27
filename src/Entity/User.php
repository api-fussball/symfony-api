<?php declare(strict_types=1);

namespace App\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class User
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    public ?int $id;

    #[ORM\Column(type: Types::STRING, unique: true)]
    public string $email = '';

    #[ORM\Column(type: Types::STRING, length: 64,unique: true)]
    public string $token = '';

    /**
     * @var array<string, int>
     */
    #[ORM\Column(type: Types::JSON)]
    public array $count = [];
}

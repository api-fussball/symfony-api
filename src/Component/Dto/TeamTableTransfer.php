<?php declare(strict_types=1);

namespace App\Component\Dto;

final class TeamTableTransfer
{
    public int $place = 0;
    public string $team = '';
    public string $img = '';
    public int $games = 0;
    public int $won = 0;
    public int $draw = 0;
    public int $lost = 0;
    public string $goal = '';
    public int $goalDifference = 0;
    public int $points = 0;
    public bool $isRelegation = false;
    public bool $isPromotion = false;
}

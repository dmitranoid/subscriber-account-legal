<?php
declare(strict_types=1);

namespace App\Application\Actions\Admin\User;

use App\Application\Actions\Action;
use App\Application\View\ViewInterface;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\SqlUserRepository;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Views\Twig;

abstract class UserAction extends Action
{

}

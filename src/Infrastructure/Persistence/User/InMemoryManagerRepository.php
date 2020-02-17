<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

class InMemoryManagerRepository
{
    /**
     * @var User[]
     */
    private $users;

    /**
     * InMemoryUserRepository constructor.
     *
     * @param array|null $users
     */
    public function __construct(array $users = null)
    {
        $this->users = $users ?? [
            111 => ['pwd'=>'111', 'fio' => 'Савко Алла Викторовна', 'email'=>'a.v.savko@brestenergo.by'],
            222 => ['pwd'=>'222', 'fio' => 'Смирнова Инна Александровна', 'email'=>'i.a.smirnova@brestenergo.by'],
            333 => ['pwd'=>'333', 'fio' => 'Василевич Алла Николаевна', 'email'=>'a.n.vasilevich@brestenergo.by'],
            444 => ['pwd'=>'444', 'fio' => 'Руф Ольга Ивановна', 'email'=>'o.i.ruf@brestenergo.by'],
            555 => ['pwd'=>'555', 'fio' => 'Астафьева Татьяна Владимировна', 'email'=>'t.v.astafeva@brestenergo.by'],
            666 => ['pwd'=>'666', 'fio' => 'Шульченкова Светлана Павловна ', 'email'=>'s.p.shulchenkova@brestenergo.by'],
            777 => ['pwd'=>'777', 'fio' => 'Артаус Светлана Сергеевна', 'email'=>'s.s.artaus@brestenergo.by'],
            888 => ['pwd'=>'888', 'fio' => 'Артаус Светлана Сергеевна', 'email'=>'s.s.artaus@brestenergo.by'],
            999 => ['pwd'=>'999', 'fio' => 'Артаус Светлана Сергеевна', 'email'=>'s.s.artaus@brestenergo.by'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }

    /**
     * {@inheritdoc}
     */
    public function findByPwd(string $pwd):? array
    {
        if (!isset($this->users[$pwd])) {
            return null;
        }
        return $this->users[$pwd];
    }
}

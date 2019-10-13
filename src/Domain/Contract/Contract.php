<?php
declare(strict_types=1);


namespace App\Domain\Contract;


use JsonSerializable;

class Contract implements JsonSerializable
{
    private $nom;

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @return mixed
     */
    public function getNomAdd()
    {
        return $this->nomAdd;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getOrgName()
    {
        return $this->orgName;
    }

    /**
     * @return mixed
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    private $nomAdd;

    private $date;

    private $orgName;

    private $fio;

    private $phone;

    public function __construct($nom, $nomAdd, $date, $orgName, $fio, $phone)
    {
        $this->nom = $nom;
        $this->nomAdd = $nomAdd;
        $this->date = $date;
        $this->orgName = $orgName;
        $this->fio = $fio;
        $this->phone = $phone;
    }


    public function jsonSerialize()
    {
        return [
            'nom' => $this->nom,
            'nomAdd' => $this->nomAdd,
            'date' => $this->date,
            'orgName' => $this->orgName,
            'fio' => $this->fio,
            'phone' => $this->phone,
        ];
    }

}
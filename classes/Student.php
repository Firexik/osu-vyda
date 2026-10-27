<?php

declare(strict_types=1);

class Student {

    //Properties
    private int $id;
    private string $name;
    private string $surname;

    //Methods
    function __construct(int $id, string $name, string $surname) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setSurname(string $surname): void {
        $this->surname = $surname;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getSurname(): string {
        return $this->surname;
    }
}
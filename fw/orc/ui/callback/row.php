<?php
namespace ORC\UI\CallBack;
interface Row {
    public function call(\ORC\UI\Data\Table $table, \ORC\UI\Data\Row $row);
}
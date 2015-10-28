<?php
namespace ORC\DAO;
class CrossTable {
	public function __construct(Table $table) {
		
	}
	
	public function join(Table $table, $field, $type) {
		
	}
	
	const INNER_JOIN = 1;
	const LEFT_JOIN = 2;
	const RIGHT_JOIN = 3;
}
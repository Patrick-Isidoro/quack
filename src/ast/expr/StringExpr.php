<?php
/**
 * Quack Compiler and toolkit
 * Copyright (C) 2016 Marcelo Camargo <marcelocamargo@linuxmail.org> and
 * CONTRIBUTORS.
 *
 * This file is part of Quack.
 *
 * Quack is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Quack is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Quack.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace QuackCompiler\Ast\Expr;

use \QuackCompiler\Ast\Types\LiteralType;
use \QuackCompiler\Parser\Parser;
use \QuackCompiler\Types\NativeQuackType;

class StringExpr extends Expr
{
    public $value;
    public $delimiter;

    public function __construct($value, $delimiter)
    {
        $this->value = $value;
        $this->delimiter = $delimiter;
    }

    public function format(Parser $parser)
    {
        $source = $this->delimiter;
        $source .= $this->value;
        $source .= $this->delimiter;

        return $this->parenthesize($source);
    }

    public function injectScope(&$parent_scope)
    {
        // Pass
    }

    public function getType()
    {
        return new LiteralType(NativeQuackType::T_STR);
    }
}

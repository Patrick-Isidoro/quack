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
namespace QuackCompiler\Ast\Stmt;

use \QuackCompiler\Intl\Localization;
use \QuackCompiler\Parser\Parser;
use \QuackCompiler\Scope\Kind;
use \QuackCompiler\Scope\ScopeError;

class FnSignatureStmt extends Stmt
{
    public $name;
    public $parameters;
    public $type;
    public $native;

    public function __construct($name, $parameters, $type)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->type = $type;
    }

    public function format(Parser $parser)
    {
        $source = '';
        if ($this->native) {
            $source .= 'native fn ';
        }
        $source .= $this->name;
        $source .= '(';
        $source .= implode(', ', array_map(function ($param) {
            $parameter = $param->name;

            if (!is_null($param->type)) {
                $parameter .= ' :: ' . $param->type;
            }

            return $parameter;
        }, $this->parameters));
        $source .= ')';

        if (!is_null($this->type)) {
            $source .= ' -> ' . $this->type;
        }

        if ($this->native) {
            $source .= PHP_EOL;
        }

        return $source;
    }

    public function injectScope(&$parent_scope)
    {
        foreach ($this->parameters as $param) {
            if ($parent_scope->hasLocal($param->name)) {
                throw new ScopeError(Localization::message('SCO060', [$param->name, $this->name]));
            }

            // TODO: inject type too?
            $parent_scope->insert($param->name, Kind::K_INITIALIZED | Kind::K_MUTABLE | Kind::K_VARIABLE | Kind::K_PARAMETER);
        }
    }

    public function runTypeChecker()
    {
        // Pass :)
    }
}

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

use \Exception;
use \QuackCompiler\Parser\Parser;

class ProgramStmt extends Stmt
{
    public $stmt_list;

    public function __construct($stmt_list)
    {
        $this->stmt_list = $stmt_list;
    }

    public function format(Parser $parser)
    {
        $source = '';

        foreach ($this->stmt_list as $stmt) {
            // TODO: Remove this after porting signature to a node
            if ($stmt instanceof \stdClass) {
                continue;
            }

            $source .= $stmt->format($parser);
        }

        return $source;
    }

    public function injectScope(&$parent_scope)
    {
        $this->createScopeWithParent($parent_scope);
        $this->bindDeclarations($this->stmt_list);

        foreach ($this->stmt_list as $node) {
            // TODO: Remove this after porting signature to a node
            if ($node instanceof \stdClass) {
                continue;
            }

            $node->injectScope($this->scope);
        }
    }

    public function runTypeChecker()
    {
        foreach ($this->stmt_list as $node) {
            // TODO: Remove this after porting signature to a node
            if ($node instanceof \stdClass) {
                continue;
            }

            $node->runTypeChecker();
        }
    }

    public function attachValidAST($ast)
    {
        $safe_stmt_list = $this->stmt_list; // copy array
        try {
            $this->stmt_list = array_merge($this->stmt_list, $ast->stmt_list);
            $this->injectScope($this->scope->parent);
            $this->runTypeChecker();
        } catch (\Exception $e) {
            // rollback in case of error
            $this->stmt_list = $safe_stmt_list;
            throw $e;
        }
    }
}

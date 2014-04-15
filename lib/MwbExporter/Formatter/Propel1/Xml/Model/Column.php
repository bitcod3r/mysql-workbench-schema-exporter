<?php
/*
 * The MIT License
 *
 * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
 * Copyright (c) 2012-2014 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace MwbExporter\Formatter\Propel1\Xml\Model;

use MwbExporter\Model\Column as BaseColumn;
use MwbExporter\Writer\WriterInterface;

class Column extends BaseColumn
{
    public function write(WriterInterface $writer)
    {
        $type = strtoupper($this->getFormatter()->getDatatypeConverter()->getType($this));

        if($type == 'DECIMAL'){
            $type = $type.'" size="'.$this->parameters->get('precision').'" scale="'.$this->parameters->get('scale');
        }
        if($type == 'ENUM' or $type == 'SET'){
            $type = $type.'" sqlType="'.$type.$this->parameters->get('datatypeExplicitParams').'" valueSet="'.substr($this->parameters->get('datatypeExplicitParams'), 1, -1);
        }

        $writer
            ->write('<column name="%s" type="%s"%s%s%s%s%s%s />',
                $this->getColumnName(),                                                                                // name
                $type,                                                                                                 // type
                ($this->isPrimary                        == 1  ? ' primaryKey="true"'                           : ''), // primaryKey
                ($this->parameters->get('length')         > 0  ? ' size="'.$this->parameters->get('length').'"' : ''), // size
                ($this->isNotNull()                            ? ' required="true"'                             : ''), // required
                ($this->isAutoIncrement()                      ? ' autoIncrement="true"'                        : ''), // autoIncrement
                (($defaultValue = $this->getDefaultValue()) && !in_array($defaultValue, array('CURRENT_TIMESTAMP')) ? ' defaultValue="'.$defaultValue.'"' : ''), // defaultValue
                ($defaultValue                                 ? ' defaultExpr="'.$defaultValue.'"'             : '') // defaultExpr
            )
        ;

        return $this;
    }
}

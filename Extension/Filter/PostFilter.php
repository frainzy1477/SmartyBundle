<?php
/**
* This file is part of NoiseLabs-SmartyBundle
*
* NoiseLabs-SmartyBundle is free software; you can redistribute it
* and/or modify it under the terms of the GNU Lesser General Public
* License as published by the Free Software Foundation; either
* version 3 of the License, or (at your option) any later version.
*
* NoiseLabs-SmartyBundle is distributed in the hope that it will be
* useful, but WITHOUT ANY WARRANTY; without even the implied warranty
* of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public
* License along with NoiseLabs-SmartyBundle; if not, see
* <http://www.gnu.org/licenses/>.
*
* Copyright (C) 2011-2012 Vítor Brandão
*
* @category    NoiseLabs
* @package     SmartyBundle
* @copyright   (C) 2011-2012 Vítor Brandão <noisebleed@noiselabs.org>
* @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL-3
* @link        http://www.noiselabs.org
*/

namespace NoiseLabs\Bundle\SmartyBundle\Extension\Filter;

/**
 * Postfilters are used to process the compiled output of the template (the PHP
 * code) immediately after the compilation is done but before the compiled
 * template is saved to the filesystem. The first parameter to the postfilter
 * `function is the compiled template code, possibly modified by other
 * postfilters. The plugin is supposed to return the modified version of this
 * code.
 *
 * See {@link http://www.smarty.net/docs/en/plugins.prefilters.postfilters.tpl}.
 *
 * @author Vítor Brandão <noisebleed@noiselabs.org>
 */
class PostFilter extends AbstractFilter
{
    public function getType()
    {
        return 'post';
    }
}
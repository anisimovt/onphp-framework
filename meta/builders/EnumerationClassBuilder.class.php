<?php
/***************************************************************************
 *   Copyright (C) 2006 by Konstantin V. Arkhipov                          *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/
/* $Id$ */

	/**
	 * @ingroup Builders
	**/
	final class EnumerationClassBuilder extends BaseBuilder
	{
		public static function build(MetaClass $class)
		{
			$out = self::getHead();
			
			if ($type = $class->getType())
				$type = "{$type->getName()} ";
			else
				$type = null;
			
			$out .= <<<EOT
{$type}class {$class->getName()} extends Enumeration
{
	// implement me!
}

EOT;
			
			return $out.self::getHeel();
		}
		
		protected static function getHead()
		{
			$head = self::startCap();
			
			$head .=
				' *   This file will never be generated again -'
				.' feel free to edit.            *';

			return $head."\n".self::endCap();
		}
	}
?>
<?php
/****************************************************************************
 *   Copyright (C) 2004-2007 by Konstantin V. Arkhipov, Anton E. Lebedevich *
 *                                                                          *
 *   This program is free software; you can redistribute it and/or modify   *
 *   it under the terms of the GNU General Public License as published by   *
 *   the Free Software Foundation; either version 3 of the License, or      *
 *   (at your option) any later version.                                    *
 *                                                                          *
 ****************************************************************************/
/* $Id$ */

	/**
	 * @ingroup Primitives
	**/
	class PrimitiveInteger extends PrimitiveNumber
	{
		const SIGNED_SMALL_MIN = -32768;
		const SIGNED_SMALL_MAX = +32767;
		
		const SIGNED_MIN = -2147483648;
		const SIGNED_MAX = +2147483647;
		
		const UNSIGNED_SMALL_MAX = 65535;
		const UNSIGNED_MAX = 4294967295;
		
		protected function checkNumber($number)
		{
			Assert::isInteger($number);
		}
		
		protected function castNumber($number)
		{
			return (int) $number;
		}
	}
?>
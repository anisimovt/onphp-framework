<?php
/****************************************************************************
 *   Copyright (C) 2011 by Evgeny V. Kokovikhin                             *
 *   This program is free software; you can redistribute it and/or modify   *
 *   it under the terms of the GNU Lesser General Public License as         *
 *   published by the Free Software Foundation; either version 3 of the     *
 *   License, or (at your option) any later version.                        *
 *                                                                          *
 ****************************************************************************/

	/**
	 * One more Aggregate cache.
	 *
	 * @ingroup Cache
	**/
	final class CyclicAggregateCache extends BaseAggregateCache
	{
		const DEFAULT_POINTS_FOR_PEER = 64;
		
		private $sorted = false;
		
		private $pointCount = self::DEFAULT_POINTS_FOR_PEER;
		
		private $pointToPeer = array();
		private $peerToPoint = array();

		/**
		 * @return CyclicAggregateCache
		**/
		public static function create()
		{
			return new self();
		}

		public function addPeer($label, CachePeer $peer, $weight = 1)
		{
			Assert::isGreaterOrEqual($weight, 0);

			$this->doAddPeer($label, $peer);
			
			$this->peerToPoint[$label] = array();
			
			for ($i = 0; $i < round($this->pointCount * $weight); $i++) {
				$point = $this->hash($label.$i);
			
				$this->pointToPeer[$point] = $label;
				$this->peerToPoint[$label][] = $point;
			}
			
			$this->sorted = false;
			
			return $this;
		}

		public function dropPeer($label)
		{
			parent::dropPeer($label);
			
			foreach ($this->peerToPoint[$label] as $point)
				unset($this->pointToPeer[$point]);
			
			unset($this->peerToPoint[$label]);
			
			return $this;
		}
		
		public function getLabel($key)
		{
			return $this->guessLabel($key);
		}

		protected function guessLabel($key)
		{
			$label = null;
			
			while ($label = $this->getLabelByKey($key))
				if (!$this->peers[$label]['object']->isAlive())
					$this->checkAlive();
				else
					break;
			
			return $label;
		}

		private function getLabelByKey($key)
		{
			if (!$this->peers)
				throw new RuntimeException('All peers are dead');
			
			if (count($this->peers) == 1)
				return key($this->peers);
			
			if (!$this->sorted)
				$this->sortPeers();

			$point = $this->hash($key);
		
			while ($peer = current($this->pointToPeer)) {
				if ($point <= key($this->pointToPeer))
					return $peer;

				next($this->pointToPeer);
			}
			
			end($this->pointToPeer);
			
			if ($point > key($this->pointToPeer)) {
				
				return reset($this->pointToPeer);
			}

			Assert::isUnreachable();
		}
		
		private function hash($key)
		{
			return hexdec(substr(sha1($key), 0, 8));
		}

		private function sortPeers()
		{
			ksort($this->pointToPeer);
			
			$this->sorted = true;
			
			return $this;
		}
	}
?>
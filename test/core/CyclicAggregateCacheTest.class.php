<?php
	final class StubPeer extends CachePeer
	{
		private $hostname = null;
		protected $alive = true;
		
		public static function create($hostname)
		{
			return new self($hostname);
		}
		
		public function __construct($hostname)
		{
			$this->hostname = $hostname;
		}
		
		public function setAlive($isAlive = true)
		{
			$this->alive = ($isAlive === true);
			
			return $this;
		}

		public function get($key)
		{
			return $this->hostname;
		}

		public function delete($key)
		{
			// TODO: Implement delete() method.
		}

		public function increment($key, $value)
		{
			// TODO: Implement increment() method.
		}

		public function decrement($key, $value)
		{
			// TODO: Implement decrement() method.
		}

		protected function store(
			$action, $key, $value, $expires = Cache::EXPIRES_MEDIUM
		)
		{
			// TODO: Implement store() method.
		}

		public function append($key, $data)
		{
			// TODO: Implement append() method.
		}
	}

	class CyclicAggregateCacheTest extends TestCase
	{
		public function testAddPeer()
		{
			$cache = CyclicAggregateCache::create();
			
			try {
				$cache->addPeer(
					'test',
					PeclMemcached::create(),
					-42
				);
				
				$this->fail('Exprected assert eception');
			} catch (WrongArgumentException $e) { /* ^_^ */ }
		}
		
		public function testPeerKeep()
		{
			$cache =
				CyclicAggregateCache::create();
			
			for ($i = 0; $i < 10; $i++)
				$cache->addPeer(
					'test'.$i,
					StubPeer::create('test'.$i),
					1
				);
			
			$testKey = 'test_key';
			
			$firstGet = $cache->get($testKey);
			$this->assertEquals($firstGet, $cache->get($testKey));
		}
		
		public function testAlive()
		{
			$peerNameList = array();
			
			for ($i = 0; $i < 10; $i++) {
				$name = 'test'.$i;
				
				$peerNameList[$name] = StubPeer::create($name);
			}
			
			$cache = CyclicAggregateCache::create();
			
			foreach ($peerNameList as $name => $peer)
				$cache->addPeer($name, $peer, 1);
			
			$valueList = array();
			
			for($i = 0; $i < 10; $i++) {
				$key = 'value'.$i;
				
				$valueList[$key] = $cache->get($key);
			}
			
			$keyList = array_keys($valueList);
			
			$key = reset($keyList);
			
			foreach ($peerNameList as $name => $peer)
				if ($name != $valueList[$key])
					$peer->setAlive(false);
			
			foreach ($valueList as $testKey => $host)
				$this->assertEquals($valueList[$key], $cache->get($testKey));
		}
	}

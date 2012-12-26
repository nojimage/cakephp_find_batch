<?php

class FindBatchItem extends CakeTestModel {

	public $actsAs = array('FindBatch.FindBatch');

}

/**
 * @property FindBatchItem $FindBatchItem
 */
class FindBatchTestCase extends CakeTestCase {

	public $fixtures = array(
		'plugin.find_batch.find_batch_item',
	);

	public $resultsBuffer = array();

	public function setUp() {
		parent::setUp();
		$this->FindBatchItem = ClassRegistry::init('FindBatchItem');
	}

	public function tearDown() {
		unset($this->FindBatchItem);
		parent::tearDown();
	}

	public function testItemIsAsExpected() {
		$this->assertIsA($this->FindBatchItem, 'FindBatchItem');
		$this->assertIsA($this->FindBatchItem->Behaviors->FindBatch, 'FindBatchBehavior');
		$this->assertTrue($this->FindBatchItem->Behaviors->attached('FindBatch'));
		$this->assertTrue($this->FindBatchItem->Behaviors->enabled('FindBatch'));
	}

	public function testFindBatch() {
		$resultsBuffer = array();

		$test = $this;

		$this->FindBatchItem->findBatch(array(
			'limit' => 5,
			'order' => array('title' => 'asc')
		), function($results, $batchInfo) use (&$resultsBuffer, $test) {
			$test->assertEquals(11, $batchInfo['totalRecords']);
			$resultsBuffer[] = $results;
		});

		$this->assertEquals(array(
			'Find Batch Item 01',
			'Find Batch Item 02',
			'Find Batch Item 03',
			'Find Batch Item 04',
			'Find Batch Item 05',
		), Set::extract('/FindBatchItem/title', $resultsBuffer[0]));

		$this->assertEquals(array(
			'Find Batch Item 06',
			'Find Batch Item 07',
			'Find Batch Item 08',
			'Find Batch Item 09',
			'Find Batch Item 10',
		), Set::extract('/FindBatchItem/title', $resultsBuffer[1]));

		$this->assertEquals(array(
			'Find Batch Item 11',
		), Set::extract('/FindBatchItem/title', $resultsBuffer[2]));
	}

	public function testFindBatchWithOldStyleCallback() {
		$resultsBuffer = array();

		$this->FindBatchItem->findBatch(array(
			'limit' => 5,
			'order' => array('title' => 'asc')
			), array($this, 'oldStyleCallback'));

		$this->assertEquals(array(
			'Find Batch Item 01',
			'Find Batch Item 02',
			'Find Batch Item 03',
			'Find Batch Item 04',
			'Find Batch Item 05',
		), Set::extract('/FindBatchItem/title', $this->resultsBuffer[0]));

		$this->assertEquals(array(
			'Find Batch Item 06',
			'Find Batch Item 07',
			'Find Batch Item 08',
			'Find Batch Item 09',
			'Find Batch Item 10',
		), Set::extract('/FindBatchItem/title', $this->resultsBuffer[1]));

		$this->assertEquals(array(
			'Find Batch Item 11',
		), Set::extract('/FindBatchItem/title', $this->resultsBuffer[2]));
	}

	public function oldStyleCallback($results, $batchInfo) {
		$this->assertEquals(11, $batchInfo['totalRecords']);
		$this->resultsBuffer[] = $results;
	}

}

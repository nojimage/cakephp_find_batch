<?php

App::uses('ModelBehavior', 'Model');

class FindBatchBehavior extends ModelBehavior {

	protected $_defaults = array();

	public function setup(&$model, $config = array()) {
		$this->settings[$model->name] = am($this->_defaults, $config);
	}

	public function findBatch(&$model, $settings, $callback) {
		$settings = am(array(
			'limit' => 100
			), $settings);

		$nextOffset = 0;

		$countSettings = $settings;
		unset($countSettings['fields']);
		unset($countSettings['limit']);
		$totalRecords = $model->find('count', $countSettings);

		while ($records = $model->find('all', am($settings, array(
			'offset' => $nextOffset
		)))) {
			$batchInfo = array(
				'totalRecords' => $totalRecords,
				'offset' => $nextOffset,
				'start' => $nextOffset + 1,
				'end' => $nextOffset + count($records),
				'alias' => $model->alias
			);

			if (is_callable($callback)) {
				if (is_array($callback)) {
					call_user_func($callback, $records, $batchInfo);
				} else {
					$callback($records, $batchInfo);
				}
			} else {
				throw new CakeException('Second argument is not callable.');
			}
			$nextOffset += $settings['limit'];
		}
	}

}
